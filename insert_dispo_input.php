<?php
require 'conn.php';

$dispo_id = !empty($_POST['dispo_id']) ? $_POST['dispo_id'] : NULL;
$containment = !empty($_POST['containment']) ? $_POST['containment'] : NULL;
$non_conformance = !empty($_POST['non_conformance']) ? $_POST['non_conformance'] : NULL;
$id_no = !empty($_POST['id_no']) ? $_POST['id_no'] : NULL;
$name = !empty($_POST['name']) ? $_POST['name'] : NULL;
$car_no = !empty($_POST['car_no']) ? $_POST['car_no'] : NULL;
$scar_no = !empty($_POST['scar_no']) ? $_POST['scar_no'] : NULL;
$document_alert = !empty($_POST['document_alert']) ? $_POST['document_alert'] : NULL;
$contact_person = !empty($_POST['contact_person']) ? $_POST['contact_person'] : NULL;
$other_specify = !empty($_POST['other_specify']) ? $_POST['other_specify'] : NULL;
$yield_off = !empty($_POST['yield_off']) ? $_POST['yield_off'] : NULL;
$da_no = !empty($_POST['da_no']) ? $_POST['da_no'] : NULL;
$rework_da_no = !empty($_POST['rework_da_no']) ? $_POST['rework_da_no'] : NULL;
$wis_no = !empty($_POST['wis_no']) ? $_POST['wis_no'] : NULL;
$repair_DA = !empty($_POST['repair_DA']) ? $_POST['repair_DA'] : NULL;
$scrap_amount = !empty($_POST['scrap_amount']) ? $_POST['scrap_amount'] : NULL;
$shipment_date = !empty($_POST['shipment_date']) ? $_POST['shipment_date'] : NULL;

$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

// ✅ Insert data into `disposition_tbl`
$sql = "INSERT INTO disposition_tbl 
        (ncpr_num, containment, non_conformance, id_no, name, car_no, scar_no, document_alert, contact_person, other_specify, yield_off, da_no, rework_da_no, wis_no, scrap_amount, shipment_date, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssssssssss", $ncpr_num, $containment, $non_conformance, $id_no, $name, $car_no, $scar_no, $document_alert, $contact_person, $other_specify, $yield_off, $da_no, $rework_da_no, $wis_no, $scrap_amount, $shipment_date, $created_at, $updated_at);

if (!$stmt->execute()) {
    error_log("Insert Error: " . $stmt->error);
    die("Insert Error: " . $stmt->error);
}

$stmt->close();
//echo "Insert successful!";
?>
