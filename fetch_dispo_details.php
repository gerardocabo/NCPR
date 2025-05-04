<?php
require 'connection.php'; // Ensure your PDO connection file is correctly included

header('Content-Type: application/json');

try {
    // Ensure connection exists
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Ensure POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if ncpr_num is set
    if (!isset($_POST['ncpr_num'])) {
        throw new Exception('Missing ncpr_num parameter');
    }

    $ncpr_num = $_POST['ncpr_num'];

    $query = "
            SELECT 
                d.ncpr_num,
                d.id,
                d.containment,
                d.id_no,
                d.name,
                d.car_no,
                d.scar_no,
                d.document_alert,
                d.contact_person,
                d.notes,
                d.other_specify,
                d.yield_off,
                d.da_no,
                d.rework_da_no,
                d.wis_no,
                d.repair_DA,
                d.scrap_amount,
                d.shipment_date,
                d.reason,
                f.file_name, f.file_path,

                -- Grouped fields
                GROUP_CONCAT(DISTINCT CONCAT(r.field_name, ':', r.field_value) SEPARATOR ' | ') AS radio_fields,
                GROUP_CONCAT(DISTINCT p.checkbox_name SEPARATOR ', ') AS checkboxes,
                GROUP_CONCAT(DISTINCT p2.key SEPARATOR ', ') AS intervention_checkboxes,
                GROUP_CONCAT(DISTINCT CONCAT(dti.input_name, ':', dti.inputted_data) SEPARATOR ', ') AS intervention_inputs,
                GROUP_CONCAT(DISTINCT CONCAT(a.approver_id, '::', 
                                                a.approver_role, '::', 
                                                k.fname, '::', 
                                                k.lname, '::', 
                                                a.approval_date, '::', 
                                                a.status) 
                                            SEPARATOR '||') AS approver_data,
                GROUP_CONCAT(DISTINCT CONCAT(f.file_name, ':', f.file_path) SEPARATOR ', ') AS files

            FROM disposition_tbl d
            LEFT JOIN dispo_radio_values r ON d.ncpr_num = r.ncpr_num
            LEFT JOIN dispo_table dt ON d.ncpr_num = dt.ncpr_num
            LEFT JOIN predefined_checkboxes p ON dt.checkbox_id = p.id
            LEFT JOIN dispo_table_intervention di ON d.ncpr_num = di.ncpr_num
            LEFT JOIN predefined_checkboxes_2 p2 ON di.checkbox_id = p2.id
            LEFT JOIN disposition_tbl_intervention dti ON d.ncpr_num = dti.ncpr_num
            LEFT JOIN dispo_approval a ON d.ncpr_num = a.ncpr_num
            LEFT JOIN users u ON a.approver_id = u.id
            LEFT JOIN key_person k ON u.person_id = k.id
            LEFT JOIN uploaded_filedispo f ON f.ncpr_num = d.ncpr_num
            WHERE d.ncpr_num = :ncpr_num
            GROUP BY d.ncpr_num
        ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':ncpr_num', $ncpr_num, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$results) {
        throw new Exception('No matching records found');
    }

    // Assume $results[0] exists from PDO fetch
    $row = $results[0];

    $disposition = [
        'id' => $row['id'],
        'ncpr_num' => $row['ncpr_num'],
        'RR_display' => $row['reason'],
        'containment' => $row['containment'],
        'id_no' => $row['id_no'],
        'name' => $row['name'],
        'car_no' => $row['car_no'],
        'scar_no' => $row['scar_no'],
        'document_alert' => $row['document_alert'],
        'contact_person' => $row['contact_person'],
        'impact_analysis' => $row['notes'],
        'other_specify' => $row['other_specify'],
        'yield_off' => $row['yield_off'],
        'da_no' => $row['da_no'],
        'rework_da_no' => $row['rework_da_no'],
        'wis_no' => $row['wis_no'],
        'repair_DA' => $row['repair_DA'],
        'scrap_amount' => $row['scrap_amount'],
        'shipment_date' => $row['shipment_date'],
        //'created_at' => $row['created_at'],
        //'updated_at' => $row['updated_at'],

        // Radio fields
        'corrective_action' => null,
        'pff' => null,
        'bd_report' => null,
        'mrb' => null,
        'customer_approval' => null,

        // Arrays
        'checkboxes' => [],
        'intervention_checkboxes' => [],
        'intervention_inputs' => [],
        'approvers' => [],
        'files_attach' => []
    ];

    // Parse grouped radio_fields (if your SQL used the format: "field_name:field_value")
    if (!empty($row['radio_fields'])) {
        $radioPairs = explode(' | ', $row['radio_fields']);
        foreach ($radioPairs as $pair) {
            list($field, $value) = explode(':', $pair);
            switch ($field) {
                case 'corrective_action':
                    $disposition['corrective_action'] = $value;
                    break;
                case 'potential_failure':
                    $disposition['pff'] = $value;
                    break;
                case 'bd_report':
                    $disposition['bd_report'] = $value;
                    break;
                case 'mrb':
                    $disposition['mrb'] = $value;
                    break;
                case 'customer_approval':
                    $disposition['customer_approval'] = $value;
                    break;
            }
        }
    }

    // Parse checkboxes (assumes comma-separated values)
    if (!empty($row['checkboxes'])) {
        $checkboxNames = array_unique(array_map('trim', explode(',', $row['checkboxes'])));
        foreach ($checkboxNames as $name) {
            $disposition['checkboxes'][] = ['checkbox_name' => $name];
        }
    }

    // Parse intervention checkboxes (assumes comma-separated values)
    if (!empty($row['intervention_checkboxes'])) {
        $interventionCheckboxNames = array_unique(array_map('trim', explode(',', $row['intervention_checkboxes'])));
        foreach ($interventionCheckboxNames as $name) {
            $disposition['intervention_checkboxes'][] = ['checkbox_name' => $name];
        }
    }

    // Parse intervention inputs (assumes "input_name:inputted_data" format)
    if (!empty($row['intervention_inputs'])) {
        $inputs = array_map('trim', explode(',', $row['intervention_inputs']));
        foreach ($inputs as $input) {
            list($inputName, $inputData) = explode(':', $input);
            $disposition['intervention_inputs'][] = [
                'input_name' => $inputName,
                'inputted_data' => $inputData
            ];
        }
    }

    // Parse approvers (limit to 3, assuming comma-separated roles and names are in same order)
    $disposition['approvers'] = [];

    if (!empty($row['approver_data'])) {
        $approverEntries = explode('||', $row['approver_data']);
        foreach ($approverEntries as $entry) {
            list($id, $role, $fname, $lname, $timestamp, $status) = explode('::', $entry);
            $formattedDateTime = date('d/m/Y \a\t g:i A', strtotime($timestamp));
            $disposition['approvers'][] = [
                'approver_id' => $id,
                'approver_role' => $role,
                'fname' => $fname,
                'lname' => $lname,
                'approval_date' => $formattedDateTime
            ];

            if ($role === 'SHELDAHL REPRESENTATIVE') { // Replace 'specific_role' with the desired role
                $disposition['dispo_status'] = $status; // Assign the value to dispoStatus
            }
        }
    }

    $disposition['files_attach'] = [];

    if (!empty($row['files'])) {
        $files = array_map('trim', explode(',', $row['files']));
        foreach ($files as $file) {
            if (strpos($file, ':') !== false) {
                list($fileName, $filePath) = explode(':', $file, 2);
                $disposition['files_attach'][] = [
                    'name' => $fileName,
                    'path' => $filePath
                ];
            }
        }
    }

    echo json_encode($disposition);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
