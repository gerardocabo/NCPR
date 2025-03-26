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
        d.id, d.ncpr_num, d.containment, d.non_conformance, d.id_no, d.name, d.car_no, d.scar_no, 
        d.document_alert, d.contact_person, d.other_specify, d.yield_off, d.da_no, 
        d.rework_da_no, d.wis_no, d.scrap_amount, d.shipment_date, d.created_at, d.updated_at,
        r.field_name, r.field_value,
        p.id AS checkbox_id, p.checkbox_name
    FROM disposition_tbl d
    LEFT JOIN dispo_radio_values r ON d.ncpr_num = r.ncpr_num
    LEFT JOIN dispo_table dt ON d.ncpr_num = dt.ncpr_num
    LEFT JOIN predefined_checkboxes p ON dt.checkbox_id = p.id
    WHERE d.ncpr_num = :ncpr_num
";


    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':ncpr_num', $ncpr_num, is_numeric($ncpr_num) ? PDO::PARAM_INT : PDO::PARAM_STR);
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
        'non_conformance' => $results[0]['non_conformance'],
        'id_no' => $results[0]['id_no'],
        'name' => $results[0]['name'],
        'car_no' => $results[0]['car_no'],
        'scar_no' => $results[0]['scar_no'],
        'document_alert' => $results[0]['document_alert'],
        'contact_person' => $results[0]['contact_person'],
        'other_specify' => $results[0]['other_specify'],
        'yield_off' => $results[0]['yield_off'],
        'da_no' => $results[0]['da_no'],
        'rework_da_no' => $results[0]['rework_da_no'],
        'wis_no' => $results[0]['wis_no'],
        'scrap_amount' => $results[0]['scrap_amount'],
        'shipment_date' => $results[0]['shipment_date'],
        'created_at' => $results[0]['created_at'],
        'updated_at' => $results[0]['updated_at'],
        'corrective_action' => null,
        'pff' => null,
        'bd_report' => null,
        'mrb' => null,
        'customer_approval' => null, // Added customer approval
        'checkboxes' => [] // Array for checkboxes
    ];
    $checkboxNames = []; // Array to track unique checkboxes

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
    }

    echo json_encode($disposition);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
