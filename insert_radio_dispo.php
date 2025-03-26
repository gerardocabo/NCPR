<?php
require 'conn.php';

$ncpr_num = !empty($_POST['ncpr_num']) ? $_POST['ncpr_num'] : NULL;
$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

// ✅ Expected radio fields
$radio_fields = ['corrective_action', 'potential_failure', 'bd_report', 'mrb', 'customer_approval'];

// ✅ Prepare SQL statement
$sql = "INSERT INTO dispo_radio_values (ncpr_num, field_name, field_value, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// ✅ Insert radio button values
foreach ($radio_fields as $field) {
    if (isset($_POST[$field])) {
        $field_value = $_POST[$field];  // YES, NO, or N/A
        $stmt->bind_param("sssss", $ncpr_num, $field, $field_value, $created_at, $updated_at);

        if (!$stmt->execute()) {
            error_log("Insert Error: " . $stmt->error);
            die("Insert Error: " . $stmt->error);
        }
    }
}

$stmt->close();
//echo "Insert successful!";
?>
