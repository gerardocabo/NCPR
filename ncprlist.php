<?php
// Include your database connection file
include 'conn.php'; // Make sure you have a proper database connection here
require "config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>admin Dashboard</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <style>
        .locked {
            pointer-events: none;
            /* Prevent clicking */
        }

        .signature-line {
            display: flex;
            justify-content: center;
            /* Center the inner content */
            margin-top: 5px;
        }

        .signature-line span {
            display: inline-block;
            border-bottom: 1px solid #000;
            /* Underline just the name */
            padding-bottom: 2px;
            /* Space between text and line */
        }
    </style>
</head>


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
                    <a href="admin_dashboard.php" class="sidebar-link">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="ncprfiling.php" class="sidebar-link">
                        <i class="fa-regular fa-folder-open"></i>
                        <span>NCPR Filing</span>
                    </a>
                </li>
                <li class="sidebar-item active">
                    <a href="ncprlist.php" class="sidebar-link">
                        <i class="fa-regular fa-address-card"></i>
                        <span>NCPR List</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="productkey.php" class="sidebar-link">
                        <i class="fa-solid fa-helmet-safety"></i>
                        <span>Product Key</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="status.php" class="sidebar-link">
                        <i class="fa-solid fa-paperclip"></i>
                        <span>Status</span>
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
                <a href="logout.php" class="sidebar-link">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="main">
            <div class="page-wrapper">
                <h2 class="mb-3">NCPR Table</h2>
                <table id="ncprTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th hidden>ID</th>
                            <th hidden>ID</th>
                            <th>NCPR Number</th>
                            <th>Initiator</th>
                            <th>Date</th>
                            <th>Part Number</th>
                            <th>Part Name</th>
                            <th>Urgent</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ncpr-table-body">
                        <?php
                        // Fetch data from ncpr_table
                        $query = "SELECT id, initiator, ncpr_num, date, part_number, part_name, status, urgent, dispo_id FROM ncpr_table";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td hidden><?php echo $row['id']; ?></td>
                                <td hidden><?php echo $row['dispo_id']; ?></td>
                                <td><?php echo $row['ncpr_num']; ?></td>
                                <td><?php echo $row['initiator']; ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['part_number']; ?></td>
                                <td><?php echo $row['part_name']; ?></td>
                                <td><?php echo $row['urgent'] ? 'Yes' : 'No'; ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm view-btn" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#viewModal">
                                        <i class="fas fa-eye"></i> NCPR
                                    </button>
                                    <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="fas fa-eye"></i> EDIT
                                    </button>
                                    <button class="btn btn-info btn-sm dispo-btn" data-id="<?php echo $row['ncpr_num']; ?>" data-bs-toggle="modal" data-bs-target="#dispoModal">
                                        <i class="fas fa-eye"></i> DISPO
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; position: relative;">
                    <!-- First Image (Left Corner) -->
                    <img src="asset/Picture1.png" alt="Logo" style="height: 50px; object-fit: contain;">

                    <!-- Second Image (Right Corner) -->
                    <div style="position: relative;">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="position: absolute; top: -10px; right: -10px;" class="m-5">
                        </button>
                        <img src="asset/Picture2.png" alt="Logo" style="height: 50px; object-fit: contain;">
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row d-flex">
                        <div class="border mb-3 align-items-center p-2">
                            <h5 class="text-center">NON-CONFORMING PRODUCT RECORD</h5>
                        </div>
                        <!-- Left Side Content (Takes up 9 columns) -->
                        <div class="col-md-9 m-0">
                            <div class="row">
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Initiator:</strong></span>
                                    <span id="view-initiator" style="font-size: 12px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>NCPR Number:</strong></span>
                                    <span id="view-ncpr-num" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Date:</strong></span>
                                    <span id="view-date" style="font-size: 12px"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Part Number:</strong></span>
                                    <span id="view-part-number" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Part Name:</strong></span>
                                    <span id="view-part-name" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Process:</strong></span>
                                    <span id="view-process" style="font-size: 14px"></span>
                                </div>
                            </div>
                            <div class="row supplier-details">
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 15px"><strong>FOR ON HOLD MATERIAL ONLY</strong></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Supplier Part Name:</strong></span>
                                    <span id="view-supplier-part-name" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Supplier Part Number:</strong></span>
                                    <span id="view-supplier-part-number" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Supplier:</strong></span>
                                    <span id="view-supplier" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Invoice Number:</strong></span>
                                    <span id="view-invoice-num" style="font-size: 14px"></span>
                                </div>
                                <div class="col-md-4 border p-2">
                                    <span class="d-block" style="font-size: 12px"><strong>Purchase Order:</strong></span>
                                    <span id="view-purchase-order" style="font-size: 14px"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Box (Aligned to the right) -->
                        <div class="col-md-3 d-flex align-items-stretch g-0">
                            <div class="right-box p-4 border w-100 text-center">
                                <h3 style="color: red; font-weight: bold;">URGENT!</h3>
                                <span>Check the checkbox if the held parts is a potential OTD Miss Shipment.</span>
                                <!-- Large Checkbox -->
                                <div class="mt-2">
                                    <input type="checkbox" id="view-urgent-checkbox" name="urgent" class="form-check-input" style="transform: scale(1.8);">
                                    <label for="view-urgent-checkbox" class="ms-2 fw-bold">Mark as Urgent</label>
                                </div>
                            </div>
                        </div>
                        <div class="row table-responsive m-0 g-0 p-0">
                            <table class="table table-bordered text-center" id="material-table">
                                <thead>
                                    <tr>
                                        <th style="font-size: 15px">NT DJ Number</th>
                                        <th style="font-size: 15px">Material / NFLD Lob / Sublot Number</th>
                                        <th style="font-size: 15px">Lot / Sublot Quantity</th>
                                        <th style="font-size: 15px">Quantity Affected</th>
                                        <th style="font-size: 15px">Defect Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Material data will be inserted here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <span style="font-size: 12px" class="fw-bold">Problem Description (specefic column/s where appropriate)</span>
                    <div class="row">
                        <div class="col-md-3 border p-2">
                            <span class="d-block" style="font-size: 12px"><strong>Issue call out</strong></span><span id="view-issue" style="font-size: 15px"></span>
                        </div>
                        <div class="col-md-3 border p-2">
                            <div class="d-flex">
                                <p style="font-size: 10px;" class="me-5">
                                    <strong>AWPI:</strong>
                                    <span id="view-awpi" class="border-bottom border-dark d-inline-block text-center" style="min-width: 50px;"></span>
                                </p>
                                <p style="font-size: 10px;">
                                    <strong>DC:</strong>
                                    <span id="view-dc" class="border-bottom border-dark d-inline-block text-center" style="min-width: 50px;"></span>
                                </p>
                            </div>

                            <div class="d-flex">
                                <p style="font-size: 12px;"><strong>Deviation?</strong></p>
                                <div class="form-check form-check-inline ms-5">
                                    <input class="form-check-input form-check-input-sm" type="checkbox" id="deviation-yes">
                                    <label class="form-check-label" for="deviation-yes" style="font-size: 10px;">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input form-check-input-sm" type="checkbox" id="deviation-no">
                                    <label class="form-check-label" for="deviation-no" style="font-size: 10px;">No</label>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <p class="m-0" style="font-size: 12px;"><strong>Issue Repeating?</strong></p>
                                <div class="form-check form-check-inline" style="margin-left: 12px;">
                                    <input class="form-check-input form-check-input-sm" type="checkbox" id="repeating-yes">
                                    <label class="form-check-label" for="repeating-yes" style="font-size: 10px;">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input form-check-input-sm" type="checkbox" id="repeating-no">
                                    <label class="form-check-label" for="repeating-no" style="font-size: 10px;">No</label>
                                </div>
                            </div>
                            <div style="display: d-block; align-items: center;">
                                <span class="d-inline-block" style="font-size: 12px;"><strong>Cavity Affected:</strong></span>
                                <span id="view-cavity" class="border-bottom border-dark d-inline-block text-center" style="min-width: 100px; font-size: 12px"></span>
                            </div>

                            <div style="display: d-block; align-items: center;">
                                <span class="d-inline-block" style="font-size: 12px;"><strong>Machine:</strong></span>
                                <span id="view-machine" class="border-bottom border-dark d-inline-block text-center" style="min-width: 100px; font-size: 12px"></span>
                            </div>
                        </div>
                        <div class="col-md-3 border p-2">
                            <span class="d-block" style="font-size: 12px"><strong>Criteria / Doc Reference</strong></span><span id="view-ref" style="font-size: 12px"></span>
                        </div>
                        <div class="col-md-3 border p-2">
                            <span class="d-block" style="font-size: 12px"><strong>Issue background or information relevant in determining this root cause of the problem</strong></span><span id="view-bg" style="font-size: 12px"></span>
                        </div>
                    </div>
                    <div class="row mt-3 border m-0">
                        <div class="col-md-6 border p-2">
                            <span style="font-size: 12px" class="fw-bold">Immediate containment action/s or countermeasure/s taken (tick as many as appropriate):</span>
                            <div style="display: block; margin-bottom: 5px;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-one" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span style="font-size: 12px;">1. Segregate affected part/s - write custodian of the segregated parts</span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-one-one" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span style="font-size: 12px;"><strong>1.1. At Hotpress:</strong>Put on hold inventory of affected lay-up materials together with the parts</span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-two" style="width: 12px; height: 12px; margin-right: 5px;">

                                    <span style="font-size: 12px;" class="me-2">2. Yield off/ 100% inspection. <strong>INSPECTION RESULTS:</strong></span>
                                    <span id="view-two-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 100px;"></span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-three" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span style="font-size: 12px;" class="me-2">3. Call the attention of QAE/PE/EE/TECH/CHIEF:</span>
                                    <span id="view-three-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 200px;"></span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-four" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span id="view-four" style="font-size: 12px;" class="me-2">4. Attach On-hold Tag and put in On-Hold cage/area</span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-five" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span id="view-five" style="font-size: 12px;">5. Check MCS stock for similar Lot Number/AWPI/DC and request to file NCPR</span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-six" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span id="view-six" style="font-size: 12px;">6. Attach copy of OCAP if available, and/or other log forms as part of the containment action</span>
                                </div>
                            </div>
                            <div style="display: inline-flex; align-items: center; gap: 10px;">
                                <span style="font-size: 12px;">7. File Shutdown Record</span>

                                <label style="font-size: 12px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-seven-yes" style="width: 12px; height: 12px; margin-right: 5px;"> Yes
                                </label>

                                <label style="font-size: 12px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-seven-no" style="width: 12px; height: 12px; margin-right: 5px;"> No
                                </label>
                                <span style="font-size: 12px;">WHO:</span>
                                <span id="view-seven-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 80px;"></span>
                                <span style="font-size: 12px;">TIME/SHIFT:</span>
                                <span id="view-seven-two" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 80px;"></span>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-eight" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span style="font-size: 12px;" class="me-2">8. Others (please specify):</span>
                                    <span id="view-eight-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 300px;"></span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-nine" style="width: 12px; height: 12px; margin-right: 5px;">
                                    <span style="font-size: 12px;" class="me-2">9. Find affected WIP, FG & raw materials - specify DJ/s and LN/s</span>
                                    <span id="view-nine-one" style="font-size: 12px; display: inline-block; border-bottom: 1px solid black; min-width: 150px;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 border p-2">
                            <div style="display: inline-flex; align-items: center; gap: 100px;" class="mb-3">
                                <span style="font-size: 15px;">Product Recall</span>

                                <label style="font-size: 15px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-recall-yes" style="margin-right: 5px;"> Yes
                                </label>

                                <label style="font-size: 15px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-recall-no" style="margin-right: 5px;"> No
                                </label>
                            </div>
                            <div style="display: inline-flex; align-items: center; gap: 50px;" class="mb-3">
                                <div style="display: block;">
                                    <div style="display: inline-flex; align-items: center;">
                                        <input type="checkbox" class="form-check-input" id="view-fgparts" style="margin-right: 5px;">
                                        <span id="view-fgparts" style="font-size: 15px;">FG PARTS</span>
                                    </div>
                                </div>

                                <span style="font-size: 15px;">Cancel Shipment</span>

                                <label style="font-size: 15px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-shipment-yes" style="margin-right: 5px;"> Yes
                                </label>

                                <label style="font-size: 15px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-shipment-no" style="margin-right: 5px;"> No
                                </label>
                            </div>

                            <span class="d-inline-block" style="font-size: 15px;"><strong>Shipment SCHEDULE:</strong></span>
                            <span id="view-ship_sched" class="border-bottom border-dark d-inline-block text-center" style="min-width: 500px; font-size: 15px"></span>
                            <div style="display: inline-flex; align-items: center; gap: 50px;" class="mt-3">
                                <div style="display: block;">
                                    <div style="display: inline-flex; align-items: center;">
                                        <input type="checkbox" class="form-check-input" id="view-wip" style="margin-right: 5px;">
                                        <span id="view-wip" style="font-size: 15px;">WIP</span>
                                    </div>
                                </div>

                                <span style="font-size: 15px;">Stop Process</span>

                                <label style="font-size: 15px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-stop_proc-yes" style="margin-right: 5px;"> Yes
                                </label>

                                <label style="font-size: 15px; display: flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-stop_proc-no" style="margin-right: 5px;"> No
                                </label>
                            </div>
                            <div style="display: block;">
                                <div style="display: d-block; align-items: center;">
                                    <span class="d-inline-block" style="font-size: 15px;"><strong>LOCATIONS:</strong></span>
                                    <span id="view-location" class="border-bottom border-dark d-inline-block text-center" style="min-width: 500px; font-size: 15px"></span>
                                </div>
                            </div>
                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-mcs" style="margin-right: 5px;">
                                    <div style="display: d-block; align-items: center;">
                                        <span id="view-mcs" style="font-size: 15px;">MCS</span>
                                        <span id="view-mcs_details" class="border-bottom border-dark d-inline-block text-center" style="min-width: 300px; font-size: 15px"></span>
                                    </div>
                                </div>
                            </div>

                            <div style="display: block;">
                                <div style="display: inline-flex; align-items: center;">
                                    <input type="checkbox" class="form-check-input" id="view-customer_notif" style="margin-right: 5px;">
                                    <span id="view-customer_notif" style="font-size: 15px;">Customer notification if non-conforming products have been shipped.</span>
                                </div>
                            </div>
                        </div>
                    </div>



                    <h5 class="text-center mb-5 mt-5">File Attachments</h5>
                    <div id="file-list" class="d-block flex-wrap">
                        <!-- Files will be dynamically inserted here -->
                    </div>
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

    <script>
        $(document).ready(function() {
            $('.view-btn').click(function() {
                var ncprNum = $(this).data('id');
                // AJAX call to fetch full details
                $.ajax({
                    url: 'fetch_ncpr_details.php', // Your PHP file to fetch full data
                    method: 'POST',
                    data: {
                        ncpr_num: ncprNum
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#view-id').text(response.id);
                        $('#view-initiator').text(response.initiator);
                        $('#view-ncpr-num').text(response.ncpr_num);
                        $('#view-date').text(response.date);
                        $('#view-part-number').text(response.part_number);
                        $('#view-part-name').text(response.part_name);
                        $('#view-process').text(response.process);
                        $('#view-issue').text(response.issue);
                        // Check if urgent is "on"
                        if (response.urgent === "on") {
                            $('#view-urgent-checkbox').prop('checked', true); // Check the checkbox
                        } else {
                            $('#view-urgent-checkbox').prop('checked', false); // Uncheck the checkbox
                        }
                        if (response.repeating === "Yes") {
                            $('#repeating-yes').prop('checked', true);
                        }

                        $('#view-awpi').text(response.awpi);
                        $('#view-dc').text(response.dc);
                        if (response.deviation === "Yes") {
                            $('#deviation-yes').prop('checked', true);
                            $('#deviation-no').prop('checked', false);
                        }
                        $('#view-cavity').text(response.cavity);
                        $('#view-machine').text(response.machine);
                        $('#view-ref').text(response.ref);
                        $('#view-bg').text(response.bg);
                        $('#view-mcs').prop('checked', response.mcs === "yes");
                        $('#view-mcs_details').text(response.mcs_details);
                        $('#view-customer_notif').prop('checked', response.customer_notif === "yes");
                        if (response.recall === "yes") {
                            $('#view-recall-yes').prop('checked', true);
                            $('#view-recall-no').prop('checked', false);
                        } else if (response.recall === "no") {
                            $('#view-recall-yes').prop('checked', false);
                            $('#view-recall-no').prop('checked', true);
                        } else {
                            $('#view-recall-yes').prop('checked', false);
                            $('#view-recall-no').prop('checked', false);
                        }
                        $('#view-fgparts').prop('checked', response.fgparts === "yes");
                        if (response.shipment === "yes") {
                            $('#view-shipment-yes').prop('checked', true);
                            $('#view-shipment-no').prop('checked', false);
                        } else if (response.shipment === "no") {
                            $('#view-shipment-yes').prop('checked', false);
                            $('#view-shipment-no').prop('checked', true);
                        } else {
                            $('#view-shipment-yes').prop('checked', false);
                            $('#view-shipment-no').prop('checked', false);
                        }
                        $('#view-location').text(response.location);
                        $('#view-ship_proc').text(response.ship_proc);
                        $('#view-ship_sched').text(response.ship_sched);
                        $('#view-wip').prop('checked', response.wip === "yes");
                        if (response.stop_proc === "yes") {
                            $('#view-stop_proc-yes').prop('checked', true);
                            $('#view-stop_proc-no').prop('checked', false);
                        } else if (response.stop_proc === "no") {
                            $('#view-stop_proc-yes').prop('checked', false);
                            $('#view-stop_proc-no').prop('checked', true);
                        } else {
                            $('#view-stop_proc-yes').prop('checked', false);
                            $('#view-stop_proc-no').prop('checked', false);
                        }

                        // Adding FOMO data
                        $('#view-supplier').text(response.supplier);
                        $('#view-supplier-part-name').text(response.supplier_part_name);
                        $('#view-supplier-part-number').text(response.supplier_part_number);
                        $('#view-invoice-num').text(response.invoice_num);
                        $('#view-purchase-order').text(response.purchase_order);

                        // New fields
                        $('#view-one').prop('checked', response.one === "yes");
                        $('#view-one-one').prop('checked', response.one_one === "yes");
                        $('#view-two').prop('checked', response.two === "yes");
                        $('#view-two-one').text(response.two_one);
                        $('#view-three').prop('checked', response.three === "yes");
                        $('#view-three-one').text(response.three_one);
                        $('#view-four').prop('checked', response.four === "yes");
                        $('#view-five').prop('checked', response.five === "yes");
                        $('#view-six').prop('checked', response.six === "yes");
                        if (response.seven === "yes") {
                            $('#view-seven-yes').prop('checked', true);
                            $('#view-seven-no').prop('checked', false);
                        } else if (response.seven === "no") {
                            $('#view-seven-yes').prop('checked', false);
                            $('#view-seven-no').prop('checked', true);
                        } else {
                            $('#view-seven-yes').prop('checked', false);
                            $('#view-seven-no').prop('checked', false);
                        }

                        $('#view-seven-one').text(response.seven_one);
                        $('#view-seven-two').text(response.seven_two);
                        $('#view-eight').prop('checked', response.eight === "yes")
                        $('#view-eight-one').text(response.eight_one);
                        $('#view-nine').prop('checked', response.nine === "yes")
                        $('#view-nine-one').text(response.nine_one);

                        if (
                            !response.supplier &&
                            !response.supplier_part_name &&
                            !response.supplier_part_number &&
                            !response.invoice_num &&
                            !response.purchase_order
                        ) {
                            $('.supplier-details').hide(); // This hides the entire section

                        } else {
                            $('.supplier-details').show(); // Show the supplier section
                        }

                        // Handling multiple material records
                        var materialTable = $('#material-table tbody');
                        materialTable.empty();

                        if (response.materials.length > 0) {
                            response.materials.forEach(function(material) {
                                materialTable.append(`
                            <tr>
                                <td style="font-size: 15px">${material.ntdj_num}</td>
                                <td style="font-size: 15px">${material.mns_num}</td>
                                <td style="font-size: 15px">${material.lot_sublot_qty}</td>
                                <td style="font-size: 15px">${material.qty_affected} - ${material.qty_affected_text}</td>
                                <td style="font-size: 15px">${material.defect_rate}%</td>
                            </tr>
                        `);
                            });
                        } else {
                            materialTable.append(`<tr><td colspan="7">No material records found</td></tr>`);
                        }

                        // Handling file attachments
                        var filesContainer = $('#file-list');
                        filesContainer.empty();

                        if (response.files.length > 0) {
                            response.files.forEach(function(file) {
                                let fileLink;
                                let fileType = file.file_type.toLowerCase();

                                if (fileType === "jpg" || fileType === "png" || fileType === "jpeg" || fileType === "gif") {
                                    // Image preview
                                    fileLink = `<img src="${file.file_path}" class="img-thumbnail" style="max-width: 150px; margin: 5px; margin-bottom: 10px;" />`;
                                } else {
                                    // Download link
                                    fileLink = `<a href="${file.file_path}" download="${file.file_name}" class="btn btn-primary btn-sm" 
                style="margin-bottom: 10px;">
                    <i class="fa fa-download"></i> Download ${file.file_name}
                </a>`;
                                }


                                filesContainer.append(`<div>${fileLink}</div>`);
                            });
                        } else {
                            filesContainer.append(`<p>No files uploaded</p>`);
                        }
                    },
                    error: function() {
                        alert('Failed to fetch data.');
                    }
                });
            });
        });

        // Function to set checkbox based on response value
        function setCheckboxValue(selector, value) {
            if (value === "yes") {
                $(selector).prop("checked", true);
            } else {
                $(selector).prop("checked", false);
            }
        }

        $(document).on("click", ".edit-btn", function() {
            var ncprId = $(this).data("id");

            $.ajax({
                url: "fetch_ncpr_details.php",
                type: "POST",
                data: {
                    ncpr_num: ncprId
                },
                dataType: "json",
                success: function(response) {
                    $("#edit-id").val(response.id);
                    $("#edit-initiator").val(response.initiator);
                    $("#edit-ncpr-num").val(response.ncpr_num);
                    $("#edit-date").val(response.date);
                    $("#edit-part-number").val(response.part_number);
                    $("#edit-part-name").val(response.part_name);
                    $("#edit-process").val(response.process);
                    if (response.urgent === "on") {
                        $("#edit-urgent-checkbox").prop("checked", true);
                    } else {
                        $("#edit-urgent-checkbox").prop("checked", false);
                    }

                    $("#edit-issue").val(response.issue);
                    $("#edit-repeating").val(response.repeating);
                    $("#edit-machine").val(response.machine);
                    $("#edit-ref").val(response.ref);
                    $("#edit-location").val(response.location);
                    $("#edit-supplier").val(response.supplier);
                    $("#edit-supplier-part-name").val(response.supplier_part_name);
                    $("#edit-supplier-part-number").val(response.supplier_part_number);
                    // New fields added
                    $("#edit-invoice-num").val(response.invoice_num);
                    $("#edit-purchase-order").val(response.purchase_order);
                    // Set checkboxes
                    setCheckboxValue("#edit-one", response.one);
                    setCheckboxValue("#edit-one_one", response.one_one);
                    setCheckboxValue("#edit-two", response.two);
                    $("#edit-two_one").val(response.two_one);
                    setCheckboxValue("#edit-three", response.three);
                    $("#edit-three_one").val(response.three_one);
                    setCheckboxValue("#edit-four", response.four);
                    setCheckboxValue("#edit-five", response.five);
                    setCheckboxValue("#edit-six", response.six);
                    // Set the value of the text inputs
                    $("#edit-seven_one").val(response.seven_one);
                    $("#edit-seven_two").val(response.seven_two);
                    // Check the correct checkbox based on response.seven value
                    if (response.seven === "yes") {
                        $("#seven-yes").prop("checked", true);
                        $("#seven-no").prop("checked", false);
                    } else if (response.seven === "no") {
                        $("#seven-no").prop("checked", true);
                        $("#seven-yes").prop("checked", false);
                    } else {
                        $("#seven-yes").prop("checked", false);
                        $("#seven-no").prop("checked", false);
                    }
                    setCheckboxValue("#edit-eight", response.eight);
                    $("#edit-eight_one").val(response.eight_one);
                    setCheckboxValue("#edit-nine", response.nine);
                    $("#edit-nine_one").val(response.nine_one);

                    // Product Recall and Shipment
                    if (response.recall === "yes") {
                        $("#recall_yes").prop("checked", true);
                        $("#recall_no").prop("checked", false);
                    } else if (response.recall === "no") {
                        $("#recall_yes").prop("checked", false);
                        $("#recall_no").prop("checked", true);
                    } else {
                        $("#recall_yes").prop("checked", false);
                        $("#recall_no").prop("checked", false);
                    }
                    setCheckboxValue("#edit-fgparts", response.fgparts);
                    if (response.shipment === "yes") {
                        $("#shipment_yes").prop("checked", true);
                        $("#shipment_no").prop("checked", false);
                    } else if (response.shipment === "no") {
                        $("#shipment_yes").prop("checked", false);
                        $("#shipment_no").prop("checked", true);
                    } else {
                        $("#shipment_yes").prop("checked", false);
                        $("#shipment_no").prop("checked", false);
                    }
                    $("#edit-ship_sched").val(response.ship_sched);

                    // WIP and Stop Process
                    setCheckboxValue("#edit-wip", response.wip);
                    if (response.stop_proc === "yes") {
                        $("#stop_proc_yes").prop("checked", true);
                        $("#stop_proc_no").prop("checked", false);
                    } else if (response.stop_proc === "no") {
                        $("#stop_proc_yes").prop("checked", false);
                        $("#stop_proc_no").prop("checked", true);
                    } else {
                        $("#stop_proc_yes").prop("checked", false);
                        $("#stop_proc_no").prop("checked", false);
                    }

                    // Locations and MCS
                    $("#edit-location").val(response.location);
                    setCheckboxValue("#edit-mcs", response.mcs);
                    $("#edit-mcs_details").val(response.mcs_details);

                    // Customer Notification
                    setCheckboxValue("#edit-customer_notif", response.customer_notif);

                    // Load Material Details into Edit Modal Table
                    var materialTable = $('#edit-material-table tbody');
                    materialTable.empty();

                    if (response.materials.length > 0) {
                        response.materials.forEach(function(material) {
                            materialTable.append(`
                        <tr>
                            <td><input type="text" class="form-control" name="material_id[]" value="${material.material_id}"></td>
                            <td><input type="text" class="form-control" name="ntdj_num[]" value="${material.ntdj_num}"></td>
                            <td><input type="text" class="form-control" name="mns_num[]" value="${material.mns_num}"></td>
                            <td><input type="text" class="form-control" name="lot_sublot_qty[]" value="${material.lot_sublot_qty}"></td>
                            <td class="d-flex gap-2">
                                <input type="number" class="form-control" name="qty_affected[]" value="${material.qty_affected}" required>
                                <input type="text" class="form-control" name="qty_affected_text[]" value="${material.qty_affected_text}" placeholder="Enter text">
                            </td>

                            <td><input type="text" class="form-control" name="defect_rate[]" value="${material.defect_rate}"></td>
                        </tr>
                    `);
                        });
                    } else {
                        materialTable.append(`<tr><td colspan="7">No material records found</td></tr>`);
                    }

                    // Handling file attachments
                    var filesContainer = $('#edit-file-list'); // Ensure this matches the ID in your HTML
                    filesContainer.empty();

                    if (response.files.length > 0) {
                        response.files.forEach(function(file) {
                            let fileLink;
                            let fileType = file.file_type.toLowerCase();

                            if (fileType === "jpg" || fileType === "png" || fileType === "jpeg" || fileType === "gif") {
                                // Image preview
                                fileLink = `<img src="${file.file_path}" class="img-thumbnail" style="max-width: 150px; margin: 5px; margin-bottom: 10px;" />`;
                            } else {
                                // Download link
                                fileLink = `<a href="${file.file_path}" download="${file.file_name}" class="btn btn-primary btn-sm" 
                style="margin-bottom: 10px;">
                    <i class="fa fa-download"></i> Download ${file.file_name}
                </a>`;
                            }

                            // Delete button
                            let deleteButton = `<button class="btn btn-danger btn-sm delete-file" 
                            data-id="${file.id}" style="margin-left: 10px;">
                                <i class="fa fa-trash"></i> Delete
                            </button>`;

                            filesContainer.append(`<div class="file-item d-flex align-items-center">${fileLink}${deleteButton}</div>`);
                        });
                    } else {
                        filesContainer.append(`<p>No files uploaded</p>`);
                    }

                    $("#editModal").modal("show");
                }
            });
        });

        // Handle form submission
        $("#editForm").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: "update_ncpr.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert("NCPR updated successfully!");
                    $("#editModal").modal("hide");
                    location.reload();
                }
            });
        });

        // Remove file functionality
        $(document).on("click", ".remove-file", function() {
            var fileId = $(this).data("id");
            $(this).parent().remove();

            $.post("delete_file.php", {
                file_id: fileId
            }, function(response) {
                console.log("File removed:", response);
            });
        });
    </script>

    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            // Initialize the DataTable
            var table = $('#ncprTable').DataTable({
                "columnDefs": [{
                    "targets": [0, 1],
                    "visible": false
                }],
                "order": [8, 'desc'],
            });

            // Function to refresh the table content via AJAX
            function refreshTable() {
                $.ajax({
                    url: 'ncprlistFetch.php', // The PHP file to fetch the new table content
                    type: 'GET',
                    success: function(data) {
                        // Temporarily clear the existing table data without clearing the table state
                        var tableBody = $('#ncpr-table-body');
                        var currentPage = table.page.info().page; // Get the current page
                        var pageLength = table.page.len(); // Get the page length

                        // Append the new data into the table body
                        tableBody.html(data);

                        // Clear and reload the table rows while keeping pagination and sorting intact
                        table.clear();
                        table.rows.add(tableBody.find('tr')); // Add the new rows
                        table.draw(); // Redraw the table

                        // Restore pagination to the previous page
                        table.page(currentPage).draw(false); // Set the current page without resetting the table state
                    },
                    error: function(xhr, status, error) {
                        console.error('Error refreshing table:', error);
                    }
                });
            }

            // Trigger refresh every 5 seconds (5000 milliseconds)
            setInterval(refreshTable, 5000);
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
            // Select the modal element
            let dispoModal = document.getElementById("dispoModal");

            // Listen for the modal close event
            dispoModal.addEventListener("hidden.bs.modal", function() {
                // Select all checkboxes and radio buttons inside the modal
                let inputs = dispoModal.querySelectorAll("input[type='checkbox'], input[type='radio']");

                // Loop through each input and uncheck it
                inputs.forEach(input => {
                    input.checked = false;
                });
            });



        });
    </script>
</body>

</html>