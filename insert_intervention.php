<?php
require "conn.php";

// ✅ Fetch predefined checkboxes
$sql_predefined2 = "SELECT id, `key` FROM predefined_checkboxes_2";
$result = $conn->query($sql_predefined2);

$checkbox_map2 = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $checkbox_map2[$row['key']] = $row['id'];
    }
}

$checkbox_groups2 = ['action_taken', 'process_dispo', 'resumption', 'instru_details', 'docu_rev'];

$sql = "INSERT INTO dispo_table_intervention (ncpr_num, checkbox_id, created_at, updated_at) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

foreach ($checkbox_groups2 as $group_2) {
    if (isset($_POST[$group_2]) && is_array($_POST[$group_2])) {
        foreach ($_POST[$group_2] as $selected_checkbox) {
            if (isset($checkbox_map2[$selected_checkbox])) {
                $checkbox_id = $checkbox_map2[$selected_checkbox];

                $stmt->bind_param("siss", $ncpr_num, $checkbox_id, $created_at, $updated_at);

                if (!$stmt->execute()) {
                    error_log("Insert Error ($group_2): " . $stmt->error);
                    die("Insert Error: " . $stmt->error);
                }
            }
        }
    }
}

$stmt->close();

$inputs = ['affected_process', 'other_resumption', 'process_instruction', 'document_alert_s', 'other_specify_s', 'released_by', 'acknowledgment_signature', 'head_signature', 'prod_manager_signature'];

$inputteds = "INSERT INTO disposition_tbl_intervention (ncpr_num, input_name, inputted_data) VALUES (?, ?, ?)";
$stmt_inputteds = $conn->prepare($inputteds);

if (!$stmt_inputteds) {
    die("Prepare failed: " . $conn->error);
}

foreach ($inputs as $inputs) {
    if (isset($_POST[$inputs])) {
        $values = is_array($_POST[$inputs]) ? $_POST[$inputs] : [$_POST[$inputs]];
        foreach ($values as $inputted) {
            $stmt_inputteds->bind_param("sss", $ncpr_num, $inputs, $inputted);
            if (!$stmt_inputteds->execute()) {
                error_log("Insert Error ($inputted): " . $stmt_inputteds->error);
                die("Insert Error: " . $stmt_inputteds->error);
            }
        }
    }
}

$stmt_inputteds->close();
