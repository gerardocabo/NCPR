<?php

include 'conn.php'; // Make sure you have a proper database connection here
require "config.php";
if (isset($_SESSION['page'])) {
    $page = $_SESSION['page'];
} else {
    $page = 'default.php'; // fallback
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>NCPR System - List</title>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">

</head>
<style>
    .locked {
        pointer-events: none;
        /* Prevent clicking */
    }

    .signature-line {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 10px;
        text-align: center;
    }

    .signature-line span {
        display: inline-block;
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
        margin-bottom: 4px;
    }

    .signature-line p {
        margin: 0;
        font-size: 0.7em;
        color: #333;
    }
</style>

<body class="bg-white">
    <div class="wrapper bg-white">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">LOGO</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="<?php echo htmlspecialchars($page) ?>" class="sidebar-link">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item active">
                    <a href="ncprlist_engineer.php" class="sidebar-link">
                        <i class="fa-regular fa-address-card"></i>
                        <span>NCPR List</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="setting.php" class="sidebar-link">
                        <i class="fa-solid fa-gear"></i>
                        <span>Setting</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="logoutModalLabel">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirm Logout
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to log out?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-danger">Yes, Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="main">
            <div class="page-wrapper p-2">
                <div class="card p-3">
                    <div class="card-title">
                        <h4 class="mb-3">NCPR Table</h4>
                    </div>
                    <table id="ncprTable" class="table table-bordered table-hover table-striped text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th hidden>ID</th>
                                <th class="text-center">NCPR Number</th>
                                <th class="text-center">Initiator</th>
                                <th class="text-center">Process</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Part Number</th>
                                <th class="text-center">Part Name</th>
                                <th class="text-center">Call Out</th>
                                <th class="text-center">Urgent</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch data from ncpr_table
                            $query = "SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, 
                                        ncpr.part_name, ncpr.date, ncpr.issue, ncpr.dispo_id, ncprstatus.status AS status,  
                                        ncpr.urgent
                                    FROM ncpr_table AS ncpr
                                    LEFT JOIN ncpr_status_table AS ncprstatus
                                            ON ncpr.ncpr_num = ncprstatus.ncpr_num
                                    ORDER BY
                                        CASE ncprstatus.status
                                            WHEN 'Open' THEN 1
                                            ELSE 2
                                        END,
                                        ncpr_num DESC";
                            $result = $conn->query($query);

                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td hidden><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['ncpr_num']; ?></td>
                                    <td><?php echo $row['initiator']; ?></td>
                                    <td><?php echo $row['process']; ?></td>
                                    <td><?php echo $row['date']; ?></td>
                                    <td class="text-center"><?php echo $row['part_number']; ?></td>
                                    <td><?php echo $row['part_name']; ?></td>
                                    <td><?php echo $row['issue']; ?></td>
                                    <td>
                                        <?php
                                        if ($row['urgent'] === 'on') {
                                            echo '<i class="fas fa-exclamation-circle text-danger" title="Urgent"></i>';
                                        } else {
                                            echo '<i class="fas fa-minus-circle text-muted" title="Not Urgent"></i>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $row['status'];
                                        $badgeClass = '';

                                        switch ($status) {
                                            case 'Open':
                                                $badgeClass = 'badge bg-success';
                                                break;

                                            case 'Closed':
                                            case 'Rejected':
                                            case 'Canceled':
                                                $badgeClass = 'badge bg-danger';
                                                break;

                                            // Optional: handle other statuses
                                            default:
                                                $badgeClass = 'badge bg-secondary'; // fallback class
                                                break;
                                        }

                                        echo "<span class='$badgeClass'>$status</span>";
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            <button class="btn btn-primary btn-sm view-btn fw-bold" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#viewModal">
                                                NCPR Form
                                            </button>
                                            <button class="btn btn-primary btn-sm dispo-btn fw-bold" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#dispoModal">
                                                Disposition
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; position: relative;">
                    <!-- First Image (Left Corner) -->
                    <img src="assets/img/Picture1.png" alt="Logo" style="height: 50px; object-fit: contain;">

                    <!-- Second Image (Right Corner) -->
                    <div style="position: relative;">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="position: absolute; top: -10px; right: -10px;" class="m-5">
                        </button>
                        <img src="assets/img/Picture2.png" alt="Logo" style="height: 50px; object-fit: contain;">
                    </div>
                </div>

                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit NCPR Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="position-relative">
                            <div class="row g-0">
                                <div class="col-md-9">
                                    <div class="d-flex flex-wrap gap-3 mb-1 g-0 m-0 p-0">
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="hidden" id="edit-id" name="id">
                                            <input type="text" class="form-control" id="edit-initiator" name="initiator">
                                            <label class="form-label">Initiator</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-ncpr-num" name="ncpr_num">
                                            <label class="form-label">NCPR Number</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="date" class="form-control" id="edit-date" name="date">
                                            <label class="form-label">Date</label>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 mb-1 g-0 m-0 p-0">
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-part-number" name="part_number">
                                            <label class="form-label">Part Number</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-part-name" name="part_name">
                                            <label class="form-label">Part Name</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-process" name="process">
                                            <label class="form-label">Process</label>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 mb-1 g-0 m-0 p-0">
                                        <div class="col-md-4 border p-2">
                                            <span class="d-block" style="font-size: 15px"><strong>FOR ON HOLD MATERIAL ONLY</strong></span>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-supplier-part-name" name="supplier_part_name">
                                            <label class="form-label">Supplier Part Name</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-supplier-part-number" name="supplier_part_number">
                                            <label class="form-label">Supplier Part Number</label>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 mb-1 g-0 m-0 p-0">
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-supplier" name="supplier">
                                            <label class="form-label">Supplier</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-invoice-num" name="invoice_num">
                                            <label class="form-label">Invoice Number</label>
                                        </div>
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="edit-purchase-order" name="purchase_order">
                                            <label class="form-label">Purchase Order</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Box (Aligned to the right and same height) -->
                                <div class="col-md-3 d-flex flex-column align-self-stretch">
                                    <div class="right-box p-4 border text-center h-100 d-flex flex-column justify-content-center">
                                        <h3 style="color: red; font-weight: bold;">URGENT!</h3>
                                        <span>Check the checkbox if the held parts is a potential OTD Miss Shipment.</span>
                                        <div class="mt-2">
                                            <input type="checkbox" id="edit-urgent-checkbox" name="urgent" class="form-check-input" style="transform: scale(1.8);">
                                            <label for="view-urgent-checkbox" class="ms-2 fw-bold">Mark as Urgent</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered" id="edit-material-table">
                                <thead>
                                    <tr>
                                        <th>Material ID</th>
                                        <th>NTDJ Number</th>
                                        <th>MNS Number</th>
                                        <th>Lot/Sublot Quantity</th>
                                        <th>Quantity Affected</th>
                                        <th>Defect Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Material data will be inserted here dynamically -->
                                </tbody>
                            </table>
                            <div class="row mt-2 me-0 ms-0 mb-0">
                                <div class="col-md-6 border p-1">
                                    <span style="font-size: 12px">
                                        Immidiate containment action/s or countermeaseure/s taken (tick as many as appropriate)
                                    </span>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-one" name="one" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="one" style="font-size: 12px;">1: Segregate affected part/s - write custodian of the segregated parts</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-one_one" name="one_one" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-4">
                                        <label for="one_one" style="font-size: 12px;">1.1: At Hotpress: Put on hold inventory of affected lay-up materials together with the parts</label><br>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-two" name="two" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="two" style="font-size: 12px;">2: Yield off/ 100% inspection. INSPECTION RESULTS:</label>
                                        <input type="text" id="edit-two_one" name="two_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 227px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-three" name="three" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="three" style="font-size: 12px;">3: Call the attention of QAE/PE/EE/TECH/CHIEF:</label>
                                        <input type="text" id="edit-three_one" name="three_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 250px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-four" name="four" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="four" style="font-size: 12px;">4: Attach On-hold Tag and put in On-Hold cage/area</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-five" name="five" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="five" style="font-size: 12px;">5: Check MCS stock for similar Lot Number/AWPI/DC and request to file NCPR</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-six" name="six" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="six" style="font-size: 12px;">6: Attach copy of OCAP if available, and/or other log forms as part of the containment action</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label for="seven" style="font-size: 12px; margin-right: 10px" class="ms-5">7: File Shutdown Record</label>

                                        <input type="checkbox" id="seven-yes" name="seven" value="yes" style="transform: scale(1); margin-right: 5px;" onclick="toggleCheckbox(this)">
                                        <label for="seven-yes" style="font-size: 12px; margin-right: 10px">Yes</label>

                                        <input type="checkbox" id="seven-no" name="seven" value="no" style="transform: scale(1); margin-right: 5px;" onclick="toggleCheckbox(this)">
                                        <label for="seven-no" style="font-size: 12px; margin-right: 10px">No</label>

                                        <label for="seven_one" style="font-size: 12px;">WHO:</label>
                                        <input type="text" id="edit-seven_one" name="seven_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 70px;">

                                        <label for="seven_two" style="font-size: 12px;" class="ms-3">TIME/SHIFT:</label>
                                        <input type="text" id="edit-seven_two" name="seven_two" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 70px;">
                                    </div>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const yesCheckbox = document.getElementById("seven-yes");
                                            const noCheckbox = document.getElementById("seven-no");

                                            yesCheckbox.addEventListener("change", function() {
                                                if (this.checked) {
                                                    noCheckbox.checked = false;
                                                }
                                            });

                                            noCheckbox.addEventListener("change", function() {
                                                if (this.checked) {
                                                    yesCheckbox.checked = false;
                                                }
                                            });
                                        });
                                    </script>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-eight" name="eight" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="eight" style="font-size: 12px;">8: Others (please specify):</label>
                                        <input type="text" id="edit-eight_one" name="eight_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 330px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-nine" name="nine" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="nine" style="font-size: 12px;">9: Find affected WIP, FG & raw materials - specify DJ/s and LN/s</label>
                                        <input type="text" id="edit-nine_one" name="nine_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 180px;">
                                    </div>
                                </div>
                                <div class="col-md-6 border p-2">
                                    <div class="d-flex align-items-center">
                                        <label style="font-size: 15px;">Product Recall:</label>
                                        <input type="checkbox" id="recall_yes" name="recall" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-5">
                                        <label for="recall_yes" style="font-size: 15px;" class="me-2">Yes</label>
                                        <input type="checkbox" id="recall_no" name="recall" value="no" style="transform: scale(1); margin-right: 5px;">
                                        <label for="recall_no" style="font-size: 15px;">No</label><br>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-fgparts" name="fgparts" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="fgparts" style="font-size: 15px;">FG Parts:</label>
                                        <label style="font-size: 15px; margin-left:200px">Cancel Shipment:</label>
                                        <input type="checkbox" id="shipment_yes" name="shipment" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-5">
                                        <label for="shipment_yes" style="font-size: 15px;">Yes</label>
                                        <input type="checkbox" id="shipment_no" name="shipment" value="no" style="transform: scale(1); margin-right: 5px;" class="ms-2">
                                        <label for="shipment_no" style="font-size: 15px;">No</label><br>
                                    </div>
                                    <div class="d-flex flex-column mb-1">
                                        <label for="ship_sched" style="font-size: 15px;">SHIPMENT SCHEDULE's / Quantity:</label>
                                        <input type="text" id="edit-ship_sched" name="ship_sched"
                                            style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; 
                                            padding: 5px; height: auto; font-size: 12px; width: 100%; max-width: 600px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-wip" name="wip" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="wip" style="font-size: 15px;">WIP:</label>
                                        <label style="font-size: 15px; margin-left:230px">Stop Process:</label>
                                        <input type="checkbox" id="stop_proc_yes" name="stop_proc" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-5">
                                        <label for="stop_proc_yes" style="font-size: 15px;">Yes</label>
                                        <input type="checkbox" id="stop_proc_no" name="stop_proc" value="no" style="transform: scale(1); margin-right: 5px;" class="ms-2">
                                        <label for="stop_proc_no" style="font-size: 15px;">No</label><br>
                                    </div>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            function setupExclusiveCheckboxes(yesId, noId) {
                                                const yesCheckbox = document.getElementById(yesId);
                                                const noCheckbox = document.getElementById(noId);

                                                yesCheckbox.addEventListener("change", function() {
                                                    if (this.checked) {
                                                        noCheckbox.checked = false;
                                                    }
                                                });

                                                noCheckbox.addEventListener("change", function() {
                                                    if (this.checked) {
                                                        yesCheckbox.checked = false;
                                                    }
                                                });
                                            }

                                            // Apply to each section
                                            setupExclusiveCheckboxes("recall_yes", "recall_no");
                                            setupExclusiveCheckboxes("shipment_yes", "shipment_no");
                                            setupExclusiveCheckboxes("stop_proc_yes", "stop_proc_no");
                                            setupExclusiveCheckboxes("seven-yes", "seven-no"); // For File Shutdown Record
                                        });
                                    </script>
                                    <div class="d-flex align-items-center">
                                        <label for="location" style="font-size: 15px;">Locations:</label>
                                        <input type="text" id="edit-location" name="location" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; 
                                            padding: 5px; height: auto; font-size: 12px; width: 100%; max-width: 550px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-mcs" name="mcs" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="mcs" style="font-size: 15px;">MCS:</label>


                                        <label for="mcs_details" style="font-size: 15px;">MCS Details:</label>
                                        <input type="text" id="edit-mcs_details" name="mcs_details" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; 
                                            padding: 5px; height: auto; font-size: 12px; width: 180%; max-width: 390px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="edit-customer_notif" name="customer_notif" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="customer_notif" style="font-size: 15px;">Customer notification if non-conforming products have been shipped.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3 p-3">
                                <div class="card-title text-center">
                                    <span class="fs-3">Optional attachment</span><br>
                                    <span class="fs-8">Evidence documents</span>
                                </div>
                                <div class="card-body">
                                    <h5>Attachment</h5>
                                    <div id="edit-file-list" class="d-block flex-wrap">
                                        <!-- Files will be dynamically inserted here -->
                                    </div>
                                    <!-- Image Preview Box -->
                                    <div class="d-flex flex-column">
                                        <div class="d-flex justify-content-center">
                                            <div id="imagePreviewContainer"
                                                style="display: none; width: 800px; height: 220px; border: 2px dashed #ccc; 
                                                            padding: 10px; display: flex; justify-content: center; align-items: center; margin-top: 10px;">
                                                <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 100%; max-height: 100%; display: none;">
                                            </div>
                                        </div>


                                        <div class="d-flex align-items-center gap-2 border p-2 mt-2 mb-2">
                                            <label for="image" class="mb-0">Choose an Image to Upload from Your Device:</label>
                                            <input type="file" name="image_name" id="image" accept="image/*" onchange="previewImage(event)">
                                        </div>
                                        <div id="file-container" class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-center gap-2 mb-2 mt-2 p-2 border">
                                                <label for="excel">Upload Excel File:</label>
                                                <input type="file" name="excel_name[]" id="excel" accept=".xls,.xlsx" multiple>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addExcelInput()">Add Another Excel File</button>
                                    <script>
                                        function addExcelInput() {
                                            var fileContainer = document.getElementById("file-container");

                                            // Create a new div for the file input
                                            var newFileDiv = document.createElement("div");
                                            newFileDiv.classList.add("d-flex", "align-items-center", "gap-2", "mb-2", "mt-2", "p-2", "border");

                                            // Create the label
                                            var newLabel = document.createElement("label");
                                            newLabel.textContent = "Upload Excel File:";

                                            // Create the input field
                                            var newInput = document.createElement("input");
                                            newInput.type = "file";
                                            newInput.name = "excel_name[]";
                                            newInput.accept = ".xls,.xlsx";

                                            // Create a remove button
                                            var removeButton = document.createElement("button");
                                            removeButton.type = "button";
                                            removeButton.classList.add("btn", "btn-sm", "btn-danger");
                                            removeButton.textContent = "Remove";
                                            removeButton.onclick = function() {
                                                fileContainer.removeChild(newFileDiv);
                                            };

                                            // Append elements
                                            newFileDiv.appendChild(newLabel);
                                            newFileDiv.appendChild(newInput);
                                            newFileDiv.appendChild(removeButton);

                                            fileContainer.appendChild(newFileDiv);
                                        }
                                    </script>
                                </div>
                                <button type="submit" class="btn btn-success  w-50 mx-auto d-block">
                                    <i class="fas fa-paper-plane"></i> Save Change</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispo viewonly Modal -->
    <div class="modal fade" id="dispoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Disposition Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here  -->
                    <?php include "viewonlydisposition.php"; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <script src="assets/js/aViewOnlyDispo.js"></script>
    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            $('#ncprTable').DataTable({
                "columnDefs": [{
                        "targets": [0],
                        "visible": false,
                    },
                    {
                        "targets": [3], // Example: center visible columns
                        "className": 'text-center'
                    }
                ],
                "order": [],
            }); // Initialize DataTable for sorting, searching, and pagination
        });

        function fetchNcprDetails(ncprNum, viewOnly) {
            $.ajax({
                url: 'fetch_ncpr_details2.php',
                method: 'GET', // Use GET to match PHP script
                data: {
                    ncpr_num: ncprNum,
                    viewOnly: viewOnly
                },
                success: function(response) {
                    $('#viewModal .modal-body').html(response); // Insert HTML response into modal
                    $('#viewModal').modal('show'); // Show modal
                },
                error: function() {
                    alert('Error fetching data.');
                }
            });
        }

        // Attach event listener to button
        $(document).on('click', '.view-btn', function() {
            var ncprNum = $(this).data('id');
            fetchNcprDetails(ncprNum, true);
        });
    </script>

    <script>
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

</body>

</html>