<?php
include 'conn.php'; // Ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ncpr_num'])) {
    $ncpr_num = $_POST['ncpr_num'];

    // Query to fetch details from ncpr_table
    $stmt = $conn->prepare("SELECT * FROM ncpr_table WHERE ncpr_num = ?");
    $stmt->bind_param("s", $ncpr_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($ncpr = $result->fetch_assoc()) {
        // Fetch corresponding fomo_table data
        $stmt_fomo = $conn->prepare("SELECT * FROM fomo WHERE ncpr_id = ?");
        $stmt_fomo->bind_param("i", $ncpr['id']);
        $stmt_fomo->execute();
        $result_fomo = $stmt_fomo->get_result();

        if ($fomo = $result_fomo->fetch_assoc()) {
            $ncpr['supplier'] = $fomo['supplier'];
            $ncpr['supplier_part_name'] = $fomo['supplier_part_name'];
            $ncpr['supplier_part_number'] = $fomo['supplier_part_number'];
            $ncpr['invoice_num'] = $fomo['invoice_num'];
            $ncpr['purchase_order'] = $fomo['purchase_order'];
        } else {
            // If no matching record found in fomo_table, set default values
            $ncpr['supplier'] = null;
            $ncpr['supplier_part_name'] = null;
            $ncpr['supplier_part_number'] = null;
            $ncpr['invoice_num'] = null;
            $ncpr['purchase_order'] = null;
        }

        // Fetch corresponding material_table data (multiple rows)
        $stmt_material = $conn->prepare("SELECT * FROM material WHERE ncpr_id = ?");
        $stmt_material->bind_param("i", $ncpr['id']);
        $stmt_material->execute();
        $result_material = $stmt_material->get_result();

        $materials = [];
        while ($material = $result_material->fetch_assoc()) {
            $materials[] = $material;
        }
        $ncpr['materials'] = $materials;

        // Fetch corresponding files_table data (multiple rows)
        $stmt_files = $conn->prepare("SELECT * FROM uploaded_file WHERE ncpr_id = ?");
        $stmt_files->bind_param("i", $ncpr['id']);
        $stmt_files->execute();
        $result_files = $stmt_files->get_result();

        $files = [];
        while ($file = $result_files->fetch_assoc()) {
            $files[] = $file;
        }
        $ncpr['files'] = $files; // Adding files as an array

        echo json_encode($ncpr);
    } else {
        echo json_encode(['error' => 'No record found']);
    }

    $stmt->close();
    $stmt_fomo->close();
    $stmt_material->close();
    $stmt_files->close();
    $conn->close();
}
?>