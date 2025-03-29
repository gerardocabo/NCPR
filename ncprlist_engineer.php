<?php
// Include your database connection file
include 'conn.php'; // Make sure you have a proper database connection here
require "config.php";
// Fetch data from ncpr_table
$query = "SELECT id, initiator, ncpr_num, date, part_number, part_name, status, urgent FROM ncpr_table";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>NCPR List</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
</head>
<style>
    ::after,
    ::before {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    a {
        text-decoration: none;
    }

    li {
        list-style: none;
    }

    h1 {
        font-weight: 600;
        font-size: 1.5rem;
    }

    body {
        font-family: 'Poppins', sans-serif;
    }

    .wrapper {
        display: flex;
    }

    .main {
        min-height: 100vh;
        width: 100%;
        overflow: hidden;
        transition: all 0.35s ease-in-out;
        background-color: #fafbfe;
    }

    #sidebar {
        width: 70px;
        min-width: 70px;
        z-index: 1000;
        transition: all .25s ease-in-out;
        background-color: #0e2238;
        display: flex;
        flex-direction: column;
        height: 100vh;
        /* Full viewport height */
        position: sticky;
        /* ✅ Keeps sidebar sticky */
        top: 0;
        /* ✅ Ensures it stays at the top when scrolling */
    }

    #sidebar.expand {
        width: 260px;
        min-width: 260px;
    }

    .toggle-btn {
        background-color: transparent;
        cursor: pointer;
        border: 0;
        padding: 1rem 1.5rem;
    }

    .toggle-btn i {
        font-size: 1.5rem;
        color: #FFF;
    }

    .sidebar-logo {
        margin: auto 0;
    }

    .sidebar-logo a {
        color: #FFF;
        font-size: 1.15rem;
        font-weight: 600;
    }

    #sidebar:not(.expand) .sidebar-logo,
    #sidebar:not(.expand) a.sidebar-link span {
        display: none;
    }

    .sidebar-nav {
        padding: 2rem 0;
        flex-grow: 1;
        /* ✅ Allows it to take available space and push footer down */
    }

    a.sidebar-link {
        padding: .625rem 1.5rem;
        color: #FFF;
        display: block;
        font-size: 0.9rem;
        white-space: nowrap;
        border-left: 3px solid transparent;
    }

    .sidebar-item,
    .sidebar-footer {
        position: relative;
    }

    .sidebar-link i {
        font-size: 1.2rem;
        color: white;
        margin-right: 10px;
    }

    a.sidebar-link:hover {
        background-color: rgba(255, 255, 255, .075);
        border-left: 3px solid #3b7ddd;
    }

    .sidebar-item {
        position: relative;
    }

    #sidebar:not(.expand) .sidebar-link span {
        display: none;
        position: absolute;
        left: 80px;
        top: 50%;
        transform: translateY(-50%);
        background: #0e2238;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 0.85rem;
        white-space: nowrap;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
    }

    #sidebar:not(.expand) .sidebar-item:hover .sidebar-link span,
    #sidebar:not(.expand) .sidebar-footer:hover .sidebar-link span {
        display: block;
    }

    .sidebar-item,
    .sidebar-footer {
        position: relative;
    }

    .sidebar-item.active a {
        background-color: rgba(255, 255, 255, 0.1);
        border-left: 3px solid #3b7ddd;
        color: #3b7ddd;
    }
</style>
<style>
    .locked {
        pointer-events: none;
        /* Prevent clicking */
    }

    .fortyle {
        margin-right: auto;
        padding: 0 10;
        text-decoration: underline;
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
                    <a href="engineer_dashboard.php" class="sidebar-link">
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
                    <a href="" class="sidebar-link">
                        <i class="fa-solid fa-helmet-safety"></i>
                        <span>Product Key</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class="fa-solid fa-paperclip"></i>
                        <span>Status</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
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
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td hidden><?php echo $row['id']; ?></td>
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
    <script src="assets/vendor/bootstrap/js/all.min.js"></script>
    <script src="assets/vendor/bootstrap/js/fontawesome.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            $('#ncprTable').DataTable({
                "columnDefs": [{
                    "targets": [0],
                    "visible": false
                }]
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

    <script>
        //viewonly dispo modal script
        $(document).ready(function() {
            $('#ncprTable tbody').on('click', '.dispo-btn', function() {
                var ncprNum = $(this).data('id');
                $("#modal-id").text(ncprNum); // Display ID inside modal

                $.ajax({
                    url: 'fetch_dispo_details.php', // New PHP script to fetch dispo_id
                    method: 'POST',
                    data: {
                        ncpr_num: ncprNum
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Log the full response for debugging
                        console.log("Encoded JSON response:", response);
                        if (response.error === "No matching records found") {
                            Swal.fire({
                                icon: "info", // Soft message icon
                                title: "No Records Found",
                                text: "There are no matching records. Please check your input and try again.",
                                confirmButtonColor: "#3085d6"
                            }).then(() => {
                                $('#dispoModal').modal('hide'); // Close modal after user clicks "OK"
                            });;
                            return;
                        } else {
                            console.log("Dispo ID found. Disabling inputs.", response);

                            // Populate fields with existing data
                            $('#modal-id').text(response.ncpr_num);

                            //$('#containment').val(response.containment);
                            $('#containment').text(response.containment); // Sets the text content
                            $('#non-conformance').text(response.non_conformance);
                            $('input[name="corrective_action"][value="' + response.corrective_action + '"]').prop('checked', true);
                            $('input[name="potential_failure"][value="' + response.pff + '"]').prop('checked', true);

                            // Populate multiple checkboxes for cause of non-conformance
                            $('input[name="cause[]"]').each(function() {
                                let checkboxValue = $(this).val(); // Get the value of each checkbox
                                let isChecked = response.checkboxes.some(cb => cb.checkbox_name === checkboxValue);
                                $(this).prop('checked', isChecked);
                            });


                            // Populate ID, name, CAR, SCAR fields
                            $('#id_no').text(response.id_no);
                            $('#name').text(response.name);
                            // Check CAR and SCAR based on the checkboxes array from the response
                            $('input[name="car"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'CAR'));
                            $('input[name="scar"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'SCAR'));
                            $('#car_no').text(response.car_no);
                            $('#scar_no').text(response.scar_no);

                            // sets checked for Dispo Required from
                            // Populate dispo checkboxes
                            $('input[name="dispo_from[]"]').each(function() {
                                let checkboxValue = $(this).val(); // Get the value of each checkbox
                                let isChecked = response.checkboxes.some(cb => cb.checkbox_name === checkboxValue);
                                $(this).prop('checked', isChecked);
                            });

                            // Populate IARA checkboxes
                            $('input[name="impact_analysis[]"]').each(function() {
                                let checkboxValue = $(this).val(); // Get the value of each checkbox
                                let isChecked = response.checkboxes.some(cb => cb.checkbox_name === checkboxValue);
                                $(this).prop('checked', isChecked);
                            });

                            $('input[name="affected_business"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'CAR'));
                            $('input[name="other_instructions"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'SCAR'));

                            // Set BD report and MRB radio buttons
                            $('input[name="bd_report"][value="' + response.bd_report + '"]').prop('checked', true);
                            $('input[name="mrb"][value="' + response.mrb + '"]').prop('checked', true);
                            $('input[name="customer_approval"][value="' + response.customer_approval + '"]').prop('checked', true); // Added this

                            // Populate product disposition checkboxes
                            $('input[name="product_dispo[]"]').each(function() {
                                let checkboxValue = $(this).val(); // Get the value of each checkbox
                                let isChecked = response.checkboxes.some(cb => cb.checkbox_name === checkboxValue);
                                $(this).prop('checked', isChecked);
                            });

                            // Populate text fields
                            $('#yield_off').text(response.yield_off || "");
                            $('#da_no').text(response.da_no || "");
                            $('#rework_da_no').text(response.rework_da_no || "");
                            $('#wis_no').text(response.wis_no || "");
                            $('#scrap_amount').text(response.scrap_amount || "");
                            $('#shipment_date').text(response.shipment_date || "");
                            $('#document_alert').text(response.document_alert || "");

                            // Disable all form elements to prevent modification
                            //$('.lock, .locked').prop('disabled', true);
                            $('#dispoModal').modal('show');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Error fetching disposition data:", {
                            status: jqXHR.status,
                            statusText: jqXHR.statusText,
                            responseText: jqXHR.responseText,
                            textStatus: textStatus,
                            errorThrown: errorThrown
                        });

                        alert(`Failed to fetch disposition data.`);
                    }
                });
            });
        });
    </script>
</body>

</html>