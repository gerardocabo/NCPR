<?php
include 'conn.php'; // Ensure this connects to your database

if (isset($_GET['ncpr_num'])) {
    $ncpr_num = $_GET['ncpr_num'];

    // Query to fetch details from ncpr_table
    $stmt = $conn->prepare("SELECT * FROM ncpr_table WHERE ncpr_num = ?");
    $stmt->bind_param("s", $ncpr_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $ncpr_id = $row['id']; // Primary key for relationships

        // Fetch intervention inputs from disposition_tbl_intervention
        $stmt_intervention = $conn->prepare("
    SELECT input_name, inputted_data 
    FROM disposition_tbl_intervention 
    WHERE ncpr_num = ?
");
        $stmt_intervention->bind_param("s", $ncpr_num);
        $stmt_intervention->execute();
        $result_intervention = $stmt_intervention->get_result();

        $intervention_inputs = [];
        while ($intervention_row = $result_intervention->fetch_assoc()) {
            $intervention_inputs[$intervention_row['input_name']] = $intervention_row['inputted_data'];
        }
        $stmt_intervention->close();


        // ✅ Fetch approvers and their full names
        $approvers = [];
        $stmt_approvers = $conn->prepare("
    SELECT a.approver_id, a.approver_role, k.fname, k.lname
    FROM dispo_approval a
    LEFT JOIN users u ON a.approver_id = u.id
    LEFT JOIN key_person k ON u.person_id = k.id
    WHERE a.ncpr_num = ?
");
        $stmt_approvers->bind_param("s", $ncpr_num);
        $stmt_approvers->execute();
        $result_approvers = $stmt_approvers->get_result();



        while ($appr = $result_approvers->fetch_assoc()) {
            if (!empty($appr['approver_role'])) {
                $fullName = trim($appr['fname'] . ' ' . $appr['lname']);
                $approvers[$appr['approver_role']] = $fullName ?: 'Pending Approval';
            }
        }
        $stmt_approvers->close();

        // Fetch corresponding fomo_table data (single row)
        $stmt_fomo = $conn->prepare("SELECT * FROM fomo WHERE ncpr_id = ?");
        $stmt_fomo->bind_param("i", $ncpr_id);
        $stmt_fomo->execute();
        $result_fomo = $stmt_fomo->get_result();
        $fomo = $result_fomo->fetch_assoc();

        // Fetch corresponding material_table data (multiple rows)
        $stmt_material = $conn->prepare("SELECT * FROM material WHERE ncpr_id = ?");
        $stmt_material->bind_param("i", $ncpr_id);
        $stmt_material->execute();
        $result_material = $stmt_material->get_result();

        $materials = [];
        while ($material = $result_material->fetch_assoc()) {
            $materials[] = $material;
        }

        // Fetch disposition_tbl data
        $stmt_dispo = $conn->prepare("SELECT * FROM disposition_tbl WHERE ncpr_num = ?");
        $stmt_dispo->bind_param("s", $ncpr_num);
        $stmt_dispo->execute();
        $result_dispo = $stmt_dispo->get_result();

        $dispositions = [];
        while ($dispo = $result_dispo->fetch_assoc()) {
            $dispositions[] = $dispo;
        }

        // Fetch dispo_radio_values
        $stmt_radio = $conn->prepare("SELECT * FROM dispo_radio_values WHERE ncpr_num = ?");
        $stmt_radio->bind_param("s", $ncpr_num);
        $stmt_radio->execute();
        $result_radio = $stmt_radio->get_result();

        $radio_values = [];
        while ($radio = $result_radio->fetch_assoc()) {
            $radio_values[$radio['field_name']] = $radio['field_value'];
        }

        // Fetch checkboxes from dispo_table and predefined_checkboxes
        $stmt_checkbox = $conn->prepare("
            SELECT p.checkbox_name 
            FROM dispo_table dt
            LEFT JOIN predefined_checkboxes p ON dt.checkbox_id = p.id
            WHERE dt.ncpr_num = ?
        ");
        $stmt_checkbox->bind_param("s", $ncpr_num);
        $stmt_checkbox->execute();
        $result_checkbox = $stmt_checkbox->get_result();

        $checkboxes = [];
        while ($cb = $result_checkbox->fetch_assoc()) {
            $checkboxes[] = $cb['checkbox_name'];
        }

        $stmt_intervention = $conn->prepare("
        SELECT p2.name 
        FROM dispo_table_intervention dti
        LEFT JOIN predefined_checkboxes_2 p2 ON dti.checkbox_id = p2.id
        WHERE dti.ncpr_num = ?
    ");

        if (!$stmt_intervention) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt_intervention->bind_param("s", $ncpr_num);
        $stmt_intervention->execute();
        $result_intervention = $stmt_intervention->get_result();

        $intervention_checkboxes = [];
        while ($icb = $result_intervention->fetch_assoc()) {
            $intervention_checkboxes[] = $icb['name'];
        }


        // Close all other statements
        $stmt->close();
        $stmt_fomo->close();
        $stmt_material->close();
        $stmt_dispo->close();
        $stmt_radio->close();
        $stmt_checkbox->close();

        $conn->close();
    } else {
        echo "<span class='text-danger'>No record found</span>";
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print NCPR</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .print-container {
            padding: 100px;
        }

        .table-border-none {
            border: none;
        }


        table {
            border-collapse: collapse;
            /* Ensures proper border rendering */
            width: 100%;
            /* Ensures full width in print mode */
        }

        td,
        th {
            border: 1px solid black;
            /* Defines border color */
            padding: 5px;
            /* Adds spacing for better readability */
        }

        .text-supplier {
            font-size: 12px;
        }

        #material-table {
            font-size: 12px;
            width: 100%;
            border-collapse: collapse;
        }

        #material-table th,
        #material-table td {
            text-align: center;
            vertical-align: middle;
            border: 1px solid black;
            padding: 5px;
        }

        span {
            font-size: 12px;
        }

        .print-container-for-page-2 {
            padding: 100px;

        }

        @media print {
            table {
                border-collapse: collapse;
                width: 100%;
            }

            .print-container {
                width: 99.9%;
                padding: 0;
            }


            td,
            th {
                border: 1px solid black;
                padding: 5px;
            }

            .text-supplier {
                font-size: 12px;
            }

            #material-table {
                font-size: 12px;
                width: 100%;
                border-collapse: collapse;
            }

            #material-table th,
            #material-table td {
                text-align: center;
                vertical-align: middle;

                border: 1px solid black;
                padding: 5px;
            }

            span {
                font-size: 12px;
            }

            /* Hide Print Button */
            .print-button {
                display: none !important;
            }

            .print-footer::after {
                display: block;
                text-align: center;
                position: fixed;
                bottom: 0;
                width: 100%;
                background: white;
            }

            .print-container-for-page-2 {
                page-break-inside: avoid;
                /* Prevent splitting */
                page-break-after: avoid;
                width: 99.9%;
                padding: 0;
                margin: 0;
            }

        }
    </style>
</head>

<body>
    <div class="print-container">
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative; margin-bottom: 2px;">
            <img src="assets/img/Picture1.png" alt="Logo" style="height: 40px; object-fit: contain;">

            <div style="position: relative;">
                <img src="assets/img/Picture2.png" alt="Logo" style="height: 40px; object-fit: contain;">
            </div>
        </div>
        <div style="text-align: center; border: 1px solid black; margin-bottom: 2px;">
            <span style="margin-top: 10px; font-size: 12px; font-weight: bold;">NON-CONFORMING PRODUCT RECORD</span>
        </div>
        <table class="container-body">
            <tr>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Initiator:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo $row['initiator']; ?></span>
                </td>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>NCPR Number:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo $row['ncpr_num']; ?></span>
                </td>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Date:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo $row['date']; ?></span>
                </td>
                <td rowspan="4" class="text-center" style="width: 150px; border-bottom: none;">
                    <span style="color:red; font-weight: bold; font-size: 20px;">URGENT!</span><br>
                    <span style="font-size: 8px">
                        Check the checkbox if the held parts is a potential OTD Miss Shipment.
                    </span><br>
                    <input type="checkbox" id="view-urgent-checkbox" name="urgent">
                    <label for="view-urgent-checkbox" class="fw-bold">Mark as Urgent</label>

                    <input type="hidden" id="urgent-value" value="<?php echo $row['urgent']; ?>">
                </td>
                <script>
                    // Get the urgent value from the hidden input
                    document.addEventListener("DOMContentLoaded", function() {
                        var urgentValue = document.getElementById("urgent-value").value;
                        var checkbox = document.getElementById("view-urgent-checkbox");

                        if (urgentValue === "on") {
                            checkbox.checked = true; // Check the checkbox
                        }
                    });
                </script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        setTimeout(() => {
                            var totalPages = document.querySelectorAll(".print-footer").length || 1;

                            document.querySelectorAll(".print-footer").forEach((footer, index) => {
                                footer.querySelector(".page-number").textContent = index + 1;
                                footer.querySelector(".total-pages").textContent = totalPages;
                            });
                        }, 500);
                    });
                </script>

            </tr>
            <tr>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Part Number:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo $row['part_number']; ?></span>
                </td>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Part Name:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo $row['part_name']; ?></span>
                </td>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Process:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo $row['process']; ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="text-supplier"><strong>FOR ON HOLD MATERIAL ONLY</strong></span>
                </td>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Supplier Part Name:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo isset($fomo['supplier_part_name']) ? $fomo['supplier_part_name'] : 'N/A'; ?></span>
                </td>
                <td>
                    <span class="d-block" style="font-size: 8px"><strong>Supplier Part Number:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo isset($fomo['supplier_part_number']) ? $fomo['supplier_part_number'] : 'N/A'; ?></span>
                </td>
            </tr>
            <tr>
                <td style="border-bottom: none;">
                    <span class="d-block" style="font-size: 8px"><strong>Supplier:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo isset($fomo['supplier']) ? $fomo['supplier'] : 'N/A'; ?></span>
                </td>
                <td style="border-bottom: none;">
                    <span class="d-block" style="font-size: 8px"><strong>Invoice Number:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo isset($fomo['invoice_num']) ? $fomo['invoice_num'] : 'N/A'; ?></span>
                </td>
                <td style="border-bottom: none;">
                    <span class="d-block" style="font-size: 8px"><strong>Purchase Order:</strong></span>
                    <span class="text-center" style="font-size: 10px"><?php echo isset($fomo['purchase_order']) ? $fomo['purchase_order'] : 'N/A'; ?></span>
                </td>
            </tr>
            <table id="material-table">
                <thead>
                    <tr>
                        <th style="font-size: 10px;">NT DJ Number</th>
                        <th style="font-size: 10px;">Material / NFLD Lob / Sublot Number</th>
                        <th style="font-size: 10px;">Lot / Sublot Quantity</th>
                        <th style="font-size: 10px;">Quantity Affected</th>
                        <th style="font-size: 10px;">Defect Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($materials)): ?>
                        <?php foreach ($materials as $material): ?>
                            <tr>
                                <td class="text-center" style="font-size: 10px;"><?php echo htmlspecialchars($material['ntdj_num']); ?></td>
                                <td class="text-center" style="font-size: 10px;"><?php echo htmlspecialchars($material['mns_num']); ?></td>
                                <td class="text-center" style="font-size: 10px;"><?php echo htmlspecialchars($material['lot_sublot_qty']); ?></td>
                                <td class="text-center" style="font-size: 10x;"><?php echo htmlspecialchars($material['qty_affected']); ?><span class="ms-3 me-3" style="font-size: 10px;">-</span><?php echo htmlspecialchars($material['qty_affected_text']); ?></td>
                                <td class="text-center" style="font-size: 10px;"><?php echo htmlspecialchars($material['defect_rate']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-danger">No material data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <span style="font-size: 8px" class="fw-bold">Problem Description (specefic column/s where appropriate)</span>
            <table style="border-top: none; border-bottom: none;">
                <tr>
                    <td style="width: 200px; height: 50px;vertical-align: middle; padding: 2px;">
                        <span class="d-block" style="font-size: 10px;"><strong>Issue call out</strong></span>
                        <span class="text-center"><?php echo $row['issue']; ?></span>
                    </td>

                    <td>
                        <table style="width: 100%; font-size: 10px; border: none;">
                            <tr>
                                <th colspan="2" style="text-align: center;" class="table-border-none">Issue in Detail</th>
                            </tr>
                            <tr>
                                <td style="padding: 2px; font-size: 8px;" class="table-border-none"><span class="me-2" style="font-size: 8px"><strong>AWPI:</strong></span><span class="text-center" style="text-decoration: underline; font-size: 8px"><?php echo $row['awpi']; ?></span></td>
                                <td style="padding: 2px; font-size: 8px;" class="table-border-none"><span class="me-2" style="font-size: 8px"><strong>DC:</strong></span><span class="text-center" style="text-decoration: underline; font-size: 8px"><?php echo $row['dc']; ?></span></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 2px;" class="table-border-none">
                                    <span style="margin-right: 5px; font-size: 8px; vertical-align: middle;"><strong>Deviation?</strong></span>

                                    <input type="checkbox"
                                        style="transform: scale(0.8); vertical-align: middle; margin-right: 3px;"
                                        <?php echo ($row['deviation'] == 'Yes') ? 'checked' : ''; ?>>
                                    <span style="font-size: 8px; vertical-align: middle;">Yes</span>

                                    <input type="checkbox"
                                        style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 3px;"
                                        <?php echo ($row['deviation'] == 'No') ? 'checked' : ''; ?>>
                                    <span style="font-size: 8px; vertical-align: middle;">No</span>

                                    <span class="text-center" hidden><?php echo $row['deviation']; ?></span>
                                </td>

                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 2px;" class="table-border-none">
                                    <span style="margin-right: 5px; font-size: 8px; vertical-align: middle; font-weight: bold;">Issue Repeating?</span>

                                    <input type="checkbox"
                                        style="transform: scale(0.8); vertical-align: middle; margin-right: 3px;"
                                        <?php echo ($row['repeating'] == 'Yes') ? 'checked' : ''; ?>>
                                    <span style="font-size: 8px; vertical-align: middle;">Yes</span>

                                    <input type="checkbox"
                                        style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php echo ($row['repeating'] == 'No') ? 'checked' : ''; ?>>
                                    <span style="font-size: 8px; vertical-align: middle;">No</span>

                                    <span class="text-center" hidden><?php echo $row['repeating']; ?></span>
                                </td>

                            </tr>
                            <tr>
                                <td colspan="2" class="table-border-none">
                                    <span style="font-size: 8px; font-weight: bold; margin-right: 5px;">Cavity Affected:</span><span class="text-center" style="text-decoration: underline; font-size: 8px"><?php echo $row['cavity']; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="table-border-none">
                                    <span style="font-size: 8px; font-weight: bold; margin-right: 5px;">Machine Used:</span><span class="text-center" style="text-decoration: underline; font-size: 8px"><?php echo $row['machine']; ?></span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 150px; height: 20px;vertical-align: middle; padding: 2px;">
                        <span class="d-block" style="font-size: 10px"><strong>Criteria / Doc Reference</strong></span>
                        <span class="text-center"><?php echo $row['ref']; ?>
                    </td>
                    <td style="width: 200px; height: 20px;vertical-align: middle; padding: 2px;">
                        <span class="d-block" style="font-size: 10px">
                            <strong>Issue background or information relevant in determining the root cause of the problem</strong>
                        </span>
                        <span class="text-center"><?php echo $row['bg']; ?>
                    </td>
                </tr>
            </table>
            <table style="border-top: none;">
                <tr>
                    <td style="width: 55%; max-width: 55%; white-space: normal; border-top: none;">
                        <span style="font-size: 8px" class="fw-bold">Immediate containment action/s or countermeasure/s taken (tick as many as appropriate):</span>
                        <br>
                        <input type="checkbox" id="view-one" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['one'] == 'yes') echo 'checked'; ?> readonly>
                        <span style="font-size: 8px;">1. Segregate affected part/s - write custodian of the segregated parts</span>
                        <br>

                        <input type="checkbox" id="view-one-one" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['one_one'] == 'yes') echo 'checked'; ?>>
                        <span style="font-size: 8px;"><strong>1.1. At Hotpress:</strong> Put on hold inventory of affected lay-up materials together with the parts</span>
                        <br>

                        <input type="checkbox" id="view-two" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['two'] == 'yes') echo 'checked'; ?>>
                        <span style="font-size: 8px;" class="me-2">2. Yield off/ 100% inspection. <strong>INSPECTION RESULTS:</strong></span>
                        <span class="text-center" style="text-decoration: underline; font-size: 8px"><?php echo $row['two_one']; ?></span>
                        <br>

                        <input type="checkbox" id="view-three" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['three'] == 'yes') echo 'checked'; ?>>
                        <span style="font-size: 8px;" class="me-2">3. Call the attention of QAE/PE/EE/TECH/CHIEF:</span>
                        <span class="text-center" style="text-decoration: underline; font-size: 8px"><?php echo $row['three_one']; ?></span>
                        <br>

                        <input type="checkbox" id="view-four" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['four'] == 'yes') echo 'checked'; ?>>
                        <span id="view-four" style="font-size: 8px;" class="me-2">4. Attach On-hold Tag and put in On-Hold cage/area</span>
                        <br>

                        <input type="checkbox" id="view-five" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['five'] == 'yes') echo 'checked'; ?>>
                        <span id="view-five" style="font-size: 8px;">5. Check MCS stock for similar Lot Number/AWPI/DC and request to file NCPR</span>
                        <br>

                        <input type="checkbox" id="view-six" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['six'] == 'yes') echo 'checked'; ?>>
                        <span id="view-six" style="font-size: 8px;">6. Attach copy of OCAP if available, and/or other log forms as part of the containment action</span>
                        <br>

                        <div style="display: inline-flex; align-items: center; gap: 10px;">
                            <span style="font-size: 8px;">7. File Shutdown Record</span>

                            <label style="font-size: 8px; display: flex; align-items: center;">
                                <input type="checkbox" id="view-seven-yes" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                    <?php if ($row['seven'] == 'yes') echo 'checked'; ?>> Yes
                            </label>

                            <label style="font-size: 8px; display: flex; align-items: center;">
                                <input type="checkbox" id="view-seven-no" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                    <?php if ($row['seven'] == 'no') echo 'checked'; ?>> No
                            </label>

                            <span style="font-size: 8px;">WHO:</span>
                            <span class="text-center" style="text-decoration: underline; font-size: 8px;"><?php echo $row['seven_one']; ?></span>

                            <span style="font-size: 8px;">TIME/SHIFT:</span>
                            <span class="text-center" style="text-decoration: underline; font-size: 8px;"><?php echo $row['seven_two']; ?></span>
                        </div>
                        <br>

                        <input type="checkbox" id="view-eight" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['eight'] == 'yes') echo 'checked'; ?>>
                        <span style="font-size: 8px;" class="me-2">8. Others (please specify):</span>
                        <span class="text-center" style="text-decoration: underline; font-size: 8px;"><?php echo $row['eight_one']; ?></span>
                        <br>

                        <input type="checkbox" id="view-nine" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                            <?php if ($row['nine'] == 'yes') echo 'checked'; ?>>
                        <span style="font-size: 8px;" class="me-2">9. Find affected WIP, FG & raw materials - specify DJ/s and LN/s</span>
                        <span class="text-center" style="text-decoration: underline; font-size: 8px;"><?php echo $row['nine_one']; ?></span>
                    </td>

                    <td style="border-top: none;">
                        <table class="table-border-none">
                            <tr>
                                <td style="padding: 2px;" class="table-border-none"><span style="font-size: 8px"><strong>PRODUCT RECALL:</strong></span></td>
                                <td style="padding: 2px;" class="table-border-none">
                                    <input type="checkbox" id="view-recall-yes" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['recall'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middel;">Yes</span>
                                    <input type="checkbox" id="view-recall-no" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['recall'] == 'no') echo 'checked'; ?>><span style="font-size: 8px ; vertical-align: middel;">No</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;" class="table-border-none">
                                    <input type="checkbox" id="view-fgparts" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['fgparts'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middel;">FG PARTS</span>
                                </td>
                                <td style="padding: 2px;" class="table-border-none"><span style="font-size: 8px">Cancel Shipment:</span>
                                    <input type="checkbox" id="view-shipment-yes" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['shipment'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middel;">Yes</span>
                                    <input type="checkbox" id="view-shipment-yes" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['shipment'] == 'no') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middel;">No</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 2px;" class="table-border-none">
                                    <span style="font-size: 8px">SHIPMENT SCHEDULE's/ Quantity:</span>
                                    <span class="text-center" style="text-decoration: underline; font-size: 8px;"><?php echo $row['ship_sched']; ?></span>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 2px;" class="table-border-none">
                                    <input type="checkbox" id="view-wip" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['wip'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middle;">WIP</span>
                                </td>
                                <td style="padding: 2px;" class="table-border-none"><span style="font-size: 8px">Stop Process:</span>
                                    <input type="checkbox" id="view-shipment-yes" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['stop_proc'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middle">Yes</span>
                                    <input type="checkbox" id="view-shipment-yes" style="transform: scale(0.8); vertical-align: middle; margin-left: 3px; margin-right: 5px;"
                                        <?php if ($row['stop_proc'] == 'no') echo 'checked'; ?>><span style="font-size: 8px; vertical-align: middle"> No</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 2px;" class="table-border-none"><span style="font-size: 8px">LOCATIONS:</span>
                                    <span class="text-center" style="text-decoration: underline;"><?php echo $row['location']; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 2px;" class="table-border-none">
                                    <input type="checkbox" id="view-mcs" style="width: 12px; height: 12px; margin-right: 5px;"
                                        <?php if ($row['mcs'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px">MCS</span>
                                    <span class="text-center" style="text-decoration: underline;"><?php echo $row['mcs_details']; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 2px;" class="table-border-none">
                                    <input type="checkbox" id="view-customer_notif" style="width: 12px; height: 12px; margin-right: 5px;"
                                        <?php if ($row['customer_notif'] == 'yes') echo 'checked'; ?>><span style="font-size: 8px">Customer notification if non-conforming products have been shipped.</span>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
        </table>
        <!-- Footer that should appear on each page -->
        <div class="print-footer d-flex justify-content-between border-top py-2">
            <span>NTSH-XXFM 2040</span>
            <span>REV. W</span>
            <span>Page <span class="page-number"></span> of <span class="total-pages"></span></span>
        </div>

    </div>
    <div class="print-container-for-page-2">

        <div style="position: relative;">
            <textarea id="containment" style="width: 100%; height: 100px; padding-top: 20px; padding-left: 5px; font-size: 14px;" placeholder=" "><?php echo isset($dispositions[0]['containment']) ? $dispositions[0]['containment'] : ''; ?></textarea>
            <label for="containment" style="position: absolute; top: 10px; left: 10px; font-size: 14px; color: #6c757d; pointer-events: none;">
                This space is intended for QA verification, containment and investigation activities
            </label>
        </div>
        <table>
            <tr>
                <!-- Cause of Non-Conformance -->
                <td colspan="2" class="text-start" style="font-size: 10px">
                    <strong>Cause of Non-conformance:</strong>
                </td>
                <td style="font-size: 10px">
                    <span style="font-size: 10px"><strong>Potential Field Failure:</strong></span>
                </td>
                <td style="font-size: 10px" class="d-flex ">
                    <span><strong>Corrective Action Request:</strong><br></span>
                    <input type="radio" name="corrective_action" value="YES"> YES
                    <input type="radio" name="corrective_action" value="NO"> NO
                    <input type="radio" name="corrective_action" value="NA"> NA
                </td>
            </tr>

            <tr>
                <!-- Cause of Non-Conformance: Left Column -->
                <td class="text-start" style="font-size: 10px">
                    <input type="checkbox" name="cause[]" value="Man"> Man<br>
                    <span>ID No:</span><span class="text-center" style="text-decoration: underline;">
                        <?php echo isset($dispositions[0]['id_no']) ? $dispositions[0]['id_no'] : 'N/A'; ?>
                    </span><br>
                    <span>Name:</span><span class="text-center" style="text-decoration: underline;">
                        <?php echo isset($dispositions[0]['name']) ? $dispositions[0]['name'] : 'N/A'; ?>
                    </span><br>
                    <input type="checkbox" name="cause[]" value="Method"> Method<br>
                    <input type="checkbox" name="cause[]" value="Machine"> Machine
                </td>

                <!-- Cause of Non-Conformance: Right Column -->
                <td style="font-size: 10px">
                    <input type="checkbox" name="cause[]" value="Material"> Material<br>
                    <input type="checkbox" name="cause[]" value="NID Item"> N.I.D Item<br>
                    <input type="checkbox" name="cause[]" value="NID Purchased Item"> N.I.D Purchased Item<br>
                    <input type="checkbox" name="cause[]" value="For Expiry Expired"> For Expiry Expired<br>
                    <input type="checkbox" name="cause[]" value="Local Supplier"> Local Supplier<br>
                    <input type="checkbox" name="cause[]" value="Customer Furnish Material">
                    <span style="color: blue; text-decoration: underline;">Customer Furnish Material</span>
                </td>
                <!-- Potential Field Failure -->
                <td class="text-center" style="font-size: 10px">
                    <input type="radio" name="potential_failure" value="YES"> Yes <br>
                    <input type="radio" name="potential_failure" value="NO"> No
                </td>
                <!-- Corrective Action: CAR & SCAR -->
                <td class="text-start" style="font-size: 10px">
                    <input type="checkbox" name="car" value="CAR">
                    <span style="text-decoration: underline; color: blue; font-size: 10px">CAR</span>, CAR No:
                    <span class="text-center" style="text-decoration: underline;">
                        <?php echo isset($dispositions[0]['car_no']) ? $dispositions[0]['car_no'] : 'N/A'; ?>
                    </span>
                    &nbsp; 8D Report:
                    <input type="radio" name="bd_report" value="YES"> YES
                    <input type="radio" name="bd_report" value="NO"> NO
                    <br>
                    <input type="checkbox" name="scar" value="SCAR"> SCAR, SCAR No:
                    <span class="text-center" style="text-decoration: underline;">
                        <?php echo isset($dispositions[0]['scar_no']) ? $dispositions[0]['scar_no'] : 'N/A'; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="font-size: 10px;">Disposition Required From:</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 10px;">
                    <input type="checkbox" name="dispo_from[]" value="NTPI"> NTPI
                    <br>
                    <strong>MRB:</strong>
                    <input type="radio" name="mrb" value="YES"> YES
                    <input type="radio" name="mrb" value="NO"> NO
                </td>
                <td colspan="2" style="font-size: 10px;">
                    <input type="checkbox" name="dispo_from[]" value="NFLD"> NFLD (Attach reference e-mail)
                    <br>
                    <strong>Need Customer Approval?</strong>
                    <input type="radio" name="customer_approval" value="YES"> YES
                    <input type="radio" name="customer_approval" value="NO"> NO
                    <span>Document Alert No:</span>
                    <span class="text-center" style="text-decoration: underline;">
                        <?php echo isset($dispositions[0]['document_alert']) ? $dispositions[0]['document_alert'] : 'N/A'; ?>
                    </span>
                </td>
            </tr>
            <tr style="font-size: 10px;">
                <!-- Left Side: Impact Analysis / Risk Assessment -->
                <td colspan="3">
                    <strong>IMPACT ANALYSIS / RISK ASSESSMENT:</strong>
                    <div class="d-flex gap-3 align-items-center flex-wrap mt-2">
                        <input type="checkbox" name="impact_analysis[]" value="Review of NCP FMEA"> Review of NCP FMEA
                        <input type="checkbox" name="impact_analysis[]" value="Review of NCP Control Plan"> Review of NCP Control Plan
                    </div>
                    <div class="form-floating w-100 mt-2">
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['notes']) ? $dispositions[0]['notes'] : 'N/A'; ?>
                        </span>
                        <label for="impact_analysis">Notes:</label>
                    </div>
                </td>

                <!-- Right Section (Spanning Rows) -->
                <td style="width: 40%; vertical-align: top;" rowspan="2">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <div class="mb-2">
                            <input type="checkbox" name="affected_business" value="Affected business"> Affected business unit/ contact person <br>
                            <span class="text-center" style="text-decoration: underline;">
                                <?php echo isset($dispositions[0]['contact_person']) ? $dispositions[0]['contact_person'] : 'N/A'; ?>
                            </span>
                        </div>
                        <div>
                            <input type="checkbox" name="other_instructions" value="Other instructions"> Other instructions,
                            <span>pls specify;</span><br>
                            <span class="text-center" style="text-decoration: underline;">
                                <?php echo isset($dispositions[0]['other_specify']) ? $dispositions[0]['other_specify'] : 'N/A'; ?>
                            </span>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Product Disposition (Left Side) -->
            <tr>
                <td colspan="3" style="font-size: 10px">
                    <strong>PRODUCT DISPOSITION:</strong>
                    <div class="d-flex gap-3 align-items-center flex-wrap mt-2">
                        <input type="checkbox" name="product_dispo[]" value="Use as is"> Use as is
                        <input type="checkbox" name="product_dispo[]" value="Re-inspection"> Re-inspection
                        <input type="checkbox" name="product_dispo[]" value="Run under normal process"> Run under normal process
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        Yield-off $ <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['yield_off']) ? $dispositions[0]['yield_off'] : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input type="checkbox" name="product_dispo[]" value="Re-grade"> Re-grade, DA No:
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['da_no']) ? $dispositions[0]['da_no'] : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input type="checkbox" name="product_dispo[]" value="Rework"> Rework, DA No:
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['rework_da_no']) ? $dispositions[0]['rework_da_no'] : 'N/A'; ?>
                        </span>
                        WIS No:
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['wis_no']) ? $dispositions[0]['wis_no'] : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="d-flex gap-3 align-items-center flex-wrap mt-2 gap-2">
                        <input type="checkbox" name="product_dispo[]" value="Re-press"> Re-press
                        <input type="checkbox" name="product_dispo[]" value="Re-plate"> Re-plate
                        <input type="checkbox" name="product_dispo[]" value="Re-Etest"> <span>Re-Etest</span>
                        <input type="checkbox" name="product_dispo[]" value="Re-measure"> Re-measure
                        <input type="checkbox" name="product_dispo[]" value="Rework Traveler"> Rework Traveler
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input type="checkbox" name="product_dispo[]" value="Repair"> Repair, Document Alert #:
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['repair_DA']) ? $dispositions[0]['repair_DA'] : 'N/A'; ?>
                        </span>
                        <input type="checkbox" name="product_dispo[]" value="Rework Traveler"> Rework Traveler
                    </div>
                    <div class="d-flex align-items-center mt-2 gap-2">
                        <input type="checkbox" name="product_dispo[]" value="Scrap"> Scrap $
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['scrap_amount']) ? $dispositions[0]['scrap_amount'] : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <input type="checkbox" name="product_dispo[]" value="RTV"> RTV <span style="color: blue; text-decoration: underline; margin-left: 20px;">Shipment Date:</span>
                        <span class="text-center" style="text-decoration: underline;">
                            <?php echo isset($dispositions[0]['shipment_date']) ? $dispositions[0]['shipment_date'] : 'N/A'; ?>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <span class="fw-bold" style="font-size: 10px">
                        For non-conforming product disposition requiring PE/ EE intervention; Otherwise, not applicable.
                    </span>
                </td>
            </tr>
            <!-- Actions Taken & Reason for Resumption -->
            <tr>
                <td style="font-size: 10px">
                    <strong>Actions Taken:</strong><br>
                    <input type="checkbox" name="actions_taken[]" value="Problem solving/troubleshooting"> Problem solving/troubleshooting<br>
                    <input type="checkbox" name="actions_taken[]" value="Process Verification/Engg Eval"> Process Verification /
                    <span>Eng'g Eval.</span>

                    <strong>Process Disposition:</strong><br>
                    <input type="checkbox" name="process_dispo[]" value="Resume Production"> Resume Production<br>
                    <input type="checkbox" name="process_dispo[]" value="Stop Production"> Stop Production

                    <span>Affected process/es:</span>
                    <span><?= htmlspecialchars($intervention_inputs['affected_process'] ?? '') ?></span>
                    <input type="checkbox" name="further_eval" value="For further Eng'g Evaluation">
                    <span>For further Eng’g Evaluation</span>
                </td>
                <td colspan="2" style="font-size: 10px">
                    <strong>Reason for Resumption:</strong><br>
                    <input type="checkbox" name="resumption_reason[]" value="Criteria"> Criteria
                    <input type="checkbox" name="resumption_reason[]" value="Method"> Method
                    <input type="checkbox" name="resumption_reason[]" value="Materials"> Materials
                    <input type="checkbox" name="resumption_reason[]" value="Machine"> Machine

                    <input type="checkbox" name="resumption_reason[]" value="Machine/fixture repair"> Machine/fixture repair<br>
                    <input type="checkbox" name="resumption_reason[]" value="Others">
                    <span>Others, pls specify</span>

                    <span><?= htmlspecialchars($intervention_inputs['other_resumption'] ?? '') ?></span><br><br>

                    <strong>Process Instruction in general:</strong>
                    <span>(for <span>DJs</span> other than the affected)</span>
                    <span><?= htmlspecialchars($intervention_inputs['process_instruction'] ?? '') ?></span>
                </td>
                <td style="font-size: 10px">
                    <strong>Instructions in detail, see reference doc:</strong>
                    <input type="checkbox" name="instructions_detail[]" value="Document Alert #"> Document Alert #:
                    <span><?= htmlspecialchars($intervention_inputs['document_alert_s'] ?? '') ?></span>

                    <input type="checkbox" name="instructions_detail[]" value="Other">
                    <span>Other (pls specify)</span>
                    <span><?= htmlspecialchars($intervention_inputs['other_specify_s'] ?? '') ?></span>


                    <strong>Documents needing revision:</strong><br>
                    <div class="d-flex flex-wrap gap-0">

                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="N/A" id="doc_na">
                        <label class="form-check-label" for="doc_na" style="font-size: 12px;">N/A</label>


                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="WIS" id="doc_wis">
                        <label class="form-check-label" for="doc_wis" style="font-size: 12px;">WIS</label>


                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="DJ" id="doc_dj">
                        <label class="form-check-label" for="doc_dj" style="font-size: 12px;">DJ</label>

                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="PROC" id="doc_proc">
                        <label class="form-check-label" for="doc_proc" style="font-size: 12px;">PROC</label>

                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="CP" id="doc_cp">
                        <label class="form-check-label" for="doc_cp" style="font-size: 12px;">CP</label>

                        <input class="form-check-input" type="checkbox" name="documents_revision[]" value="PFMEA" id="doc_pfmea">
                        <label class="form-check-label" for="doc_pfmea" style="font-size: 12px;">PFMEA</label>

                    </div>

                    <strong>Process Released By:</strong>
                    <span><?= htmlspecialchars($intervention_inputs['released_by'] ?? '') ?></span>


                    <span>(Signature Above Printed Name/ Date)</span>

                </td>
            </tr>
            <tr>
                <th colspan="4" class="text-center" style="font-size: 10px;">Approving Authorities</th>
            </tr>
            <tr>
                <td style="font-size: 10px;">
                    <strong>QA Engineer / NT Representative:</strong><br>
                    (Signature & Date)
                    <br>
                    <?php echo isset($approvers['QA ENGINEER']) ? $approvers['QA ENGINEER'] : 'Pending Approval'; ?>
                </td>
                <td colspan="2" style="font-size: 10px;">
                    <strong>QA Manager or his/her appointee:</strong><br>
                    (Signature & Date)
                    <br>
                    <?php
                    // Display the name for either QA MANAGER or QA SUPERVISOR
                    if (isset($approvers['QA MANAGER'])) {
                        echo $approvers['QA MANAGER'];
                    } elseif (isset($approvers['QA SUPERVISOR'])) {
                        echo $approvers['QA SUPERVISOR'];
                    } else {
                        echo 'Pending Approval';
                    }
                    ?>
                </td>

                <td style="font-size: 10px;">
                    <strong>Sheilah / NT Representative:</strong><br>
                    (Signature & Date)
                    <br>
                    <?php echo isset($approvers['SHELDAHL REPRESENTATIVE']) ? $approvers['SHELDAHL REPRESENTATIVE'] : 'Pending Approval'; ?>
                </td>
            </tr>
            <tr>
                <td style="font-size: 10px;">
                    <strong>Acknowledgment</strong>
                    <small class="text-muted">(Applies only with CAR & COPQ issues)</small>
                </td>
                <td style="font-size: 10px;" colspan="2">
                    <strong>PE or EE Head or his/her appointee:</strong>
                </td>
                <td style="font-size: 10px;">
                    <strong>Production Manager or his/her appointee:</strong>
                </td>
            </tr>
        </table>

        <div class="print-footer d-flex justify-content-between border-top py-2">
            <span>NTSH-XXFM 2040</span>
            <span>REV. W</span>
            <span>Page <span class="page-number"></span> of <span class="total-pages"></span></span>
        </div>
    </div>
    <?php
    $radio_json = json_encode($radio_values);
    $checkbox_json = json_encode($checkboxes); // From original dispo_table
    $intervention_checkbox_json = json_encode($intervention_checkboxes); // New one
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radioValues = <?php echo $radio_json; ?>;
            const checkboxValues = <?php echo $checkbox_json; ?>;
            const interventionCheckboxValues = <?php echo $intervention_checkbox_json; ?>;

            // Set radio buttons
            for (const name in radioValues) {
                const radios = document.getElementsByName(name);
                radios.forEach(radio => {
                    if (radio.value === radioValues[name]) {
                        radio.checked = true;
                    }
                });
            }

            // Set original checkboxes
            checkboxValues.forEach(value => {
                const checkboxes = document.querySelectorAll(`input[type="checkbox"][value="${value}"]`);
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            });

            // Set intervention checkboxes
            interventionCheckboxValues.forEach(value => {
                const interventionBoxes = document.querySelectorAll(`input[type="checkbox"][value="${value}"]`);
                interventionBoxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            });
        });
    </script>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>