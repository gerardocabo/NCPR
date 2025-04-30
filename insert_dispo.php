<?php
require 'conn.php';

$dispo_id = !empty($_POST['dispo_id']) ? $_POST['dispo_id'] : NULL;
$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

// ✅ Fetch predefined checkboxes
$sql_predefined = "SELECT id, checkbox_name FROM predefined_checkboxes";
$result = $conn->query($sql_predefined);

$checkbox_map = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $checkbox_map[$row['checkbox_name']] = $row['id'];
    }
}

$checkbox_groups = ['cause', 'independents', 'dispo_from', 'IARA', 'product_dispo'];

$sql = "INSERT INTO dispo_table (ncpr_num, checkbox_id, created_at, updated_at) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

foreach ($checkbox_groups as $group) {
    if (isset($_POST[$group]) && is_array($_POST[$group])) {
        foreach ($_POST[$group] as $selected_checkbox) {
            if (isset($checkbox_map[$selected_checkbox])) {
                $checkbox_id = $checkbox_map[$selected_checkbox];

                $stmt->bind_param("siss", $ncpr_num, $checkbox_id, $created_at, $updated_at);

                if (!$stmt->execute()) {
                    error_log("Insert Error ($group): " . $stmt->error);
                    die("Insert Error: " . $stmt->error);
                }
            }
        }
    }
}

$stmt->close();
