<?php
require 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $initiator = $_POST['initiator'] ?? null;
    $ncpr_num = $_POST['ncpr_num'] ?? null;
    $date = $_POST['date'] ?? null;
    $part_number = $_POST['part_number'] ?? null;
    $part_name = $_POST['part_name'] ?? null;
    $process = $_POST['process'] ?? null;
    $urgent = $_POST['urgent'] ?? null;
    $issue = $_POST['issue'] ?? null;
    $awpi = $_POST['awpi'] ?? null;
    $dc = $_POST['dc'] ?? null;
    $deviation = $_POST['deviation'] ?? null;
    $repeating = $_POST['repeating'] ?? null;
    $cavity = $_POST['cavity'] ?? null;
    $machine = $_POST['machine'] ?? null;
    $ref = $_POST['ref'] ?? null;
    $bg = $_POST['bg'] ?? null;
    $one = $_POST['one'] ?? null;
    $one_one = $_POST['one_one'] ?? null;
    $two = $_POST['two'] ?? null;
    $two_one = $_POST['two_one'] ?? null;
    $three = $_POST['three'] ?? null;
    $three_one = $_POST['three_one'] ?? null;
    $four = $_POST['four'] ?? null;
    $five = $_POST['five'] ?? null;
    $six = $_POST['six'] ?? null;
    $seven = $_POST['seven'] ?? null;
    $seven_one = $_POST['seven_one'] ?? null;
    $seven_two = $_POST['seven_two'] ?? null;
    $eight = $_POST['eight'] ?? null;
    $eight_one = $_POST['eight_one'] ?? null;
    $nine = $_POST['nine'] ?? null;
    $nine_one = $_POST['nine_one'] ?? null;
    $recall = $_POST['recall'] ?? null;
    $fgparts = $_POST['fgparts'] ?? null;
    $shipment = $_POST['shipment'] ?? null;
    $ship_sched = $_POST['ship_sched'] ?? null;
    $wip = $_POST['wip'] ?? null;
    $stop_proc = $_POST['stop_proc'] ?? null;
    $location = $_POST['location'] ?? null;
    $mcs = $_POST['mcs'] ?? null;
    $mcs_details = $_POST['mcs_details'] ?? null;
    $customer_notif = $_POST['customer_notif'] ?? null;

    // Fetching new data for fomo table
    $supplier_part_name = $_POST['supplier_part_name'] ?? null;
    $supplier_part_number = $_POST['supplier_part_number'] ?? null;
    $supplier = $_POST['supplier'] ?? null;
    $invoice_num = $_POST['invoice_num'] ?? null;
    $purchase_order = $_POST['purchase_order'] ?? null;

    if ($id === null) {
        echo json_encode(["success" => false, "message" => "Error: Missing required ID parameter."]);
        exit;
    }

    // Update ncpr_table
    $sql = "UPDATE ncpr_table 
            SET initiator = ?, ncpr_num = ?, date = ?, part_number = ?, part_name = ?, 
                process = ?, urgent = ?, issue = ?, awpi = ?, dc = ?, 
                deviation = ?, repeating = ?, cavity = ?, machine = ?, ref = ?, 
                bg = ?, one = ?, one_one = ?, two = ?, two_one = ?, 
                three = ?, three_one = ?, four = ?, five = ?, six = ?, 
                seven = ?, seven_one = ?, seven_two = ?, eight = ?, eight_one = ?, 
                nine = ?, nine_one = ?, recall = ?, fgparts = ?, shipment = ?, 
                ship_sched = ?, wip = ?, stop_proc = ?, location = ?, 
                mcs = ?, mcs_details = ?, customer_notif = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "ssssssssssssssssssssssssssssssssssssssssssi",
            $initiator,
            $ncpr_num,
            $date,
            $part_number,
            $part_name,
            $process,
            $urgent,
            $issue,
            $awpi,
            $dc,
            $deviation,
            $repeating,
            $cavity,
            $machine,
            $ref,
            $bg,
            $one,
            $one_one,
            $two,
            $two_one,
            $three,
            $three_one,
            $four,
            $five,
            $six,
            $seven,
            $seven_one,
            $seven_two,
            $eight,
            $eight_one,
            $nine,
            $nine_one,
            $recall,
            $fgparts,
            $shipment,
            $ship_sched,
            $wip,
            $stop_proc,
            $location,
            $mcs,
            $mcs_details,
            $customer_notif,
            $id
        );
        $stmt->execute();
        $stmt->close();
    }

    // Update fomo table
    $sql_fomo = "UPDATE fomo 
                 SET supplier_part_name = ?, supplier_part_number = ?, supplier = ?, 
                     invoice_num = ?, purchase_order = ? 
                 WHERE ncpr_id = ?";
    $stmt_fomo = $conn->prepare($sql_fomo);

    if ($stmt_fomo) {
        $stmt_fomo->bind_param(
            "sssssi",
            $supplier_part_name,
            $supplier_part_number,
            $supplier,
            $invoice_num,
            $purchase_order,
            $id
        );
        $stmt_fomo->execute();
        $stmt_fomo->close();
    }

    if (!empty($_POST['ntdj_num'])) {
        $ntdj_nums = $_POST['ntdj_num'];
        $mns_nums = $_POST['mns_num'];
        $lot_sublot_qtys = $_POST['lot_sublot_qty'];
        $qty_affected = $_POST['qty_affected'];
        $qty_affected_text = $_POST['qty_affected_text'];
        $defect_rates = $_POST['defect_rate'];
        $material_ids = $_POST['material_id'] ?? []; // Ensure this exists

        foreach ($ntdj_nums as $index => $ntdj) {
            $mns = $mns_nums[$index];
            $lot_sublot_qty = $lot_sublot_qtys[$index];
            $qty_aff = $qty_affected[$index];
            $qty_aff_text = $qty_affected_text[$index];
            $defect_rate = $defect_rates[$index];
            $mat_id = !empty($material_ids[$index]) ? $material_ids[$index] : null;

            // Debugging: Check what is received
            error_log("Processing Material ID: " . ($mat_id ?? "NULL"));

            if ($mat_id && $mat_id > 0) {
                // Check if material exists before updating
                $check_query = "SELECT material_id FROM material WHERE material_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("i", $mat_id);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    // Update existing material
                    $query = "UPDATE material 
                              SET ntdj_num = ?, mns_num = ?, lot_sublot_qty = ?, qty_affected = ?, qty_affected_text = ?, defect_rate = ?
                              WHERE material_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssisssi", $ntdj, $mns, $lot_sublot_qty, $qty_aff, $qty_aff_text, $defect_rate, $mat_id);
                    error_log("Updating Material ID: " . $mat_id);
                } else {
                    // Insert if the material ID doesn't exist in DB
                    $query = "INSERT INTO material (ncpr_id, ntdj_num, mns_num, lot_sublot_qty, qty_affected, qty_affected_text, defect_rate)
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ississs", $id, $ntdj, $mns, $lot_sublot_qty, $qty_aff, $qty_aff_text, $defect_rate);
                    error_log("Material ID not found, inserting new material.");
                }
                $check_stmt->close();
            } else {
                // If no valid ID, insert new material
                $query = "INSERT INTO material (ncpr_id, ntdj_num, mns_num, lot_sublot_qty, qty_affected, qty_affected_text, defect_rate)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ississs", $id, $ntdj, $mns, $lot_sublot_qty, $qty_aff, $qty_aff_text, $defect_rate);
                error_log("No Material ID received, inserting as new.");
            }

            $stmt->execute();
            $stmt->close();
        }
    }

    if (!empty($_POST['deleted_files'])) {
        $remove_files = $_POST['deleted_files'];

        foreach ($remove_files as $file_id) {
            // Get file path
            $file_query = "SELECT file_path FROM uploaded_file WHERE id = ?";
            $stmt = $conn->prepare($file_query);
            $stmt->bind_param("i", $file_id);
            $stmt->execute();
            $stmt->bind_result($file_path);
            $stmt->fetch();
            $stmt->close();

            if ($file_path) {
                if (file_exists($file_path)) {
                    if (unlink($file_path)) {
                        error_log("Deleted file: " . $file_path);
                    } else {
                        error_log("Failed to delete: " . $file_path);
                    }
                } else {
                    error_log("File not found: " . $file_path);
                }

                // Remove file from database
                $delete_query = "DELETE FROM uploaded_file WHERE id = ?";
                $stmt = $conn->prepare($delete_query);
                $stmt->bind_param("i", $file_id);
                if ($stmt->execute()) {
                    error_log("Deleted file record from database: " . $file_id);
                } else {
                    error_log("Failed to delete file record: " . $file_id);
                }
                $stmt->close();
            }
        }
    }

    if (isset($_FILES['image_name']) && $_FILES['image_name']['error'] == 0) {
        $image_name = $_FILES['image_name']['name'];
        $image_tmp = $_FILES['image_name']['tmp_name'];
        $image_path = 'assets/img/' . time() . '_' . $image_name; // Unique file name

        if (move_uploaded_file($image_tmp, $image_path)) {
            $img_sql = "INSERT INTO uploaded_file (ncpr_id, file_name, file_path, file_type, uploaded_at) 
                        VALUES (?, ?, ?, 'image', NOW())";
            $stmt = $conn->prepare($img_sql);
            $stmt->bind_param("iss", $id, $image_name, $image_path);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("Image upload failed for file: " . $image_name);
        }
    }

    if (!empty($_FILES['excel_name']['name'][0])) {
        foreach ($_FILES['excel_name']['name'] as $key => $excel_name) {
            if ($_FILES['excel_name']['error'][$key] == 0) {
                $excel_tmp = $_FILES['excel_name']['tmp_name'][$key];
                $excel_path = 'assets/excel/' . time() . '_' . $excel_name; // Unique file name

                if (move_uploaded_file($excel_tmp, $excel_path)) {
                    $excel_sql = "INSERT INTO uploaded_file (ncpr_id, file_name, file_path, file_type, uploaded_at) 
                                  VALUES (?, ?, ?, 'excel', NOW())";
                    $stmt = $conn->prepare($excel_sql);
                    $stmt->bind_param("iss", $id, $excel_name, $excel_path);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    error_log("Excel file upload failed for file: " . $excel_name);
                }
            }
        }
    }

    echo json_encode(["success" => true, "message" => "NCPR and FOMO updated successfully."]);
}

$conn->close();
