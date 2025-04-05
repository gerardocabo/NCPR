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
        d.id, d.ncpr_num, d.containment, d.id_no, d.name, d.car_no, d.scar_no, 
        d.document_alert, d.contact_person, d.notes, d.other_specify, d.yield_off, d.da_no, 
        d.rework_da_no, d.wis_no, d.repair_DA, d.scrap_amount, d.shipment_date, d.created_at, d.updated_at,
        r.field_name, r.field_value,
        p.id AS checkbox_id, p.checkbox_name,
        a.approver_id, a.approver_role,
        k.fname, k.lname
    FROM disposition_tbl d
    LEFT JOIN dispo_radio_values r ON d.ncpr_num = r.ncpr_num
    LEFT JOIN dispo_table dt ON d.ncpr_num = dt.ncpr_num
    LEFT JOIN predefined_checkboxes p ON dt.checkbox_id = p.id
    LEFT JOIN dispo_approval a ON d.ncpr_num = a.ncpr_num
    LEFT JOIN users u ON a.approver_id = u.id
    LEFT JOIN key_person k ON u.person_id = k.id
    WHERE d.ncpr_num = :ncpr_num
";


    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':ncpr_num', $ncpr_num, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$results) {
        throw new Exception('No matching records found');
    }

    // Extract primary disposition data
    $disposition = [
        'id' => $results[0]['id'],
        'ncpr_num' => $results[0]['ncpr_num'],
        'containment' => $results[0]['containment'],
        'id_no' => $results[0]['id_no'],
        'name' => $results[0]['name'],
        'car_no' => $results[0]['car_no'],
        'scar_no' => $results[0]['scar_no'],
        'document_alert' => $results[0]['document_alert'],
        'contact_person' => $results[0]['contact_person'],
        'notes' => $results[0]['notes'],
        'other_specify' => $results[0]['other_specify'],
        'yield_off' => $results[0]['yield_off'],
        'da_no' => $results[0]['da_no'],
        'rework_da_no' => $results[0]['rework_da_no'],
        'wis_no' => $results[0]['wis_no'],
        'repair_DA' => $results[0]['repair_DA'],
        'scrap_amount' => $results[0]['scrap_amount'],
        'shipment_date' => $results[0]['shipment_date'],
        'created_at' => $results[0]['created_at'],
        'updated_at' => $results[0]['updated_at'],
        'corrective_action' => null,
        'pff' => null,
        'bd_report' => null,
        'mrb' => null,
        'customer_approval' => null, // Added customer approval
        'checkboxes' => [], // Array for checkboxes
        'approvers' => [] // Array for approvers
    ];
    $checkboxNames = []; // Array to track unique checkboxes
    $maxApprovers = 3;  // Limit to 3 approvers
    $approverCount = 0;  // Initialize counter for approvers
    $approverRoles = []; // Array to track unique approver roles

    // Process radio values and checkboxes
    foreach ($results as $row) {
        if (!empty($row['field_name']) && !empty($row['field_value'])) {
            switch ($row['field_name']) {
                case 'corrective_action':
                    $disposition['corrective_action'] = $row['field_value'];
                    break;
                case 'potential_failure':
                    $disposition['pff'] = $row['field_value'];
                    break;
                case 'bd_report':
                    $disposition['bd_report'] = $row['field_value'];
                    break;
                case 'mrb':
                    $disposition['mrb'] = $row['field_value'];
                    break;
                case 'customer_approval': // Added case for customer approval
                    $disposition['customer_approval'] = $row['field_value'];
                    break;
            }
        }

        // Process checkboxes, but ensure unique values
        if (!empty($row['checkbox_id']) && !empty($row['checkbox_name']) && !in_array($row['checkbox_name'], $checkboxNames)) {
            $disposition['checkboxes'][] = [
                'checkbox_name' => $row['checkbox_name']
            ];
            $checkboxNames[] = $row['checkbox_name']; // Track unique checkboxes
        }

        // Process approvers (approver_id and approval_role)
        if (!empty($row['approver_id']) && !empty($row['approver_role']) && $approverCount < $maxApprovers) {
            // Ensure the role is unique
            if (!in_array($row['approver_role'], $approverRoles)) {
                $disposition['approvers'][] = [
                    'approver_id' => $row['approver_id'],
                    'approver_role' => $row['approver_role'],
                    'fname' => $row['fname'], // Added fname
                    'lname' => $row['lname']  // Added lname
                ];

                // Add the role to the approverRoles array to avoid duplicates
                $approverRoles[] = $row['approver_role'];
                $approverCount++; // Increment counter to ensure no more than 3 approvers are added
            }
        }
    }

    echo json_encode($disposition);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
