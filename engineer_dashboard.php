<?php
require "config.php";
$user_role = $_SESSION['role'];
require "conn.php";
$ncpr_count = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM ncpr_table");
if ($row = $result->fetch_assoc()) {
    $ncpr_count = $row['total'];
}
$open_ncpr_count = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM ncpr_table WHERE status = 'open'");
if ($row = $result->fetch_assoc()) {
    $open_ncpr_count = $row['total'];
}
$closed_ncpr_count = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM ncpr_table WHERE status = 'Close'");
if ($row = $result->fetch_assoc()) {
    $closed_ncpr_count = $row['total'];
}
$urgent_ncpr_count = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM ncpr_table WHERE urgent = 'on' AND status = 'open'");
if ($row = $result->fetch_assoc()) {
    $urgent_ncpr_count = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <style>
        .action-container {
            position: relative;
            /* Ensure floating indicator stays positioned correctly */
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 5px;
            /* Adjust spacing */
        }

        .urgent-indicator {
            position: absolute;
            top: -5px;
            right: 0;
            /* Move above the buttons */
            background: red;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
            animation: blink 1s infinite alternate;
            /* Optional blinking effect */
        }

        /* Optional Blinking Effect */
        @keyframes blink {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
            }
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
                    <a href="#">MENU</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item active">
                    <a href="engineer_dashboard.php" class="sidebar-link">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="ncprlist_engineer.php" class="sidebar-link">
                        <i class="fa-regular fa-address-card"></i>
                        <span>NCPR List</span>
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
                <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <!-- Logout Confirmation Modal -->
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

        <div class="main p-3">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <a href="ncprlist_engineer.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>NCPR Files</h5>
                                                <p class="mb-0 fw-bold"><?= $ncpr_count ?></p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <span class="fa-stack fa-2x">
                                                <i class="fa-solid fa-folder fa-stack-1x"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="ncprlist_engineer.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>Open Files</h5>
                                                <p class="mb-0 fw-bold"><?= $open_ncpr_count ?></p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <span class="fa-stack fa-2x">
                                                <i class="fa-solid fa-file fa-stack-1x"></i>
                                                <i class="fa-solid fa-question fa-stack-2x" style="font-size: 1.5em; color: red; position: relative; top: -10px; left: 10px; z-index: 2;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="ncprlist_engineer.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>Closed NCPR</h5>
                                                <p class="mb-0 fw-bold"><?= $closed_ncpr_count ?></p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <span class="fa-stack fa-2x">
                                                <i class="fa-solid fa-file fa-stack-1x"></i>
                                                <i class="fa-solid fa-flag-checkered fa-stack-2x" style="font-size: 1.1em; color: green; position: relative; top: -10px; left: 10px; z-index: 2;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="ncprlist_engineer.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>Urgent NCPR</h5>
                                                <p class="mb-0 fw-bold"><?= $urgent_ncpr_count ?></p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <span class="fa-stack fa-2x">
                                                <i class="fa-solid fa-file fa-stack-1x"></i>
                                                <i class="fa-solid fa-exclamation fa-stack-2x" style="font-size: 1.5em; color: red; position: relative; top: -10px; left: 10px; z-index: 2;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h5 class="mb-3">NCPR Recently Filed Table</h5>
                    </div>
                    <div class="table-container table-responsive mt-3">
                        <table id="ncprTable" class="table table-bordered table-hover text-center" style="width:100%">
                            <thead class="table-secondary">
                                <tr>
                                    <th hidden>ID</th>
                                    <th class="text-center">NCPR Number</th>
                                    <th class="text-center">Initiator</th>
                                    <th class="text-center">Status</th>
                                    <th hidden>Status</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel">
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
                    <!-- Content will be loaded here via AJAX -->
                </div>
                <!-- Modal Footer (For Buttons) -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispo-ing Modal Structure -->
    <div class="modal fade" id="dispoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dispositioning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here  -->
                    <?php include "disposition.php"; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT Dispo-ing Modal Structure -->
    <div class="modal fade" id="editDispoModal" tabindex="-1" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dispositioning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- edit Dispo Place here  -->
                    <?php include "edit_disposition.php"; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="username" data-user="<?php echo $_SESSION['user']; ?>" style="display: none;"></div>
    <div id="notification-box" style="
        position: fixed;
        top: 10px;
        right: 10px;
        background: green;
        color: white;
        padding: 10px;
        display: none;
        border-radius: 5px;
        font-weight: bold;">
    </div>
    <div id="warning-box" style="
        position: fixed;
        top: 60px; /* Positioned below the notification box */
        right: 10px;
        background: orange;
        color: white;
        padding: 15px;
        display: none;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
    </div>

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <script src="assets/js/approval.js"></script>

    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function() {
            (function() {
                var username = $("#username").data("user"); // Get logged-in username
                var lastSeenId = parseInt(sessionStorage.getItem("lastSeenId_" + username)) || 0; // Retrieve last seen ID
                var notifiedNCPRs = JSON.parse(sessionStorage.getItem("notifiedNCPRs_" + username) || "[]"); // Retrieve notified NCPRs
                var firstLoad = true;

                var table = $('#ncprTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            className: 'btn btn-success'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            className: 'btn btn-info ms-2'
                        }
                    ],
                    "ajax": {
                        "url": "fetch_ncpr.php",
                        "type": "GET",
                        "dataSrc": function(json) {
                            if (json.ncprs.length > 0) {
                                let currentTime = new Date().getTime(); // Get current timestamp in milliseconds
                                let overdueNCPRs = [];
                                let urgentNCPRs = [];
                                let unseenNCPRs = [];

                                if (firstLoad) {
                                    // Set last seen ID from the server on first load
                                    lastSeenId = json.lastSeenId;
                                    sessionStorage.setItem("lastSeenId_" + username, lastSeenId);
                                } else {
                                    // Retrieve lastSeenId from sessionStorage
                                    lastSeenId = parseInt(sessionStorage.getItem("lastSeenId_" + username)) || 0;
                                }

                                // Retrieve previously notified NCPRs
                                notifiedNCPRs = JSON.parse(sessionStorage.getItem("notifiedNCPRs_" + username) || "[]");

                                json.ncprs.forEach(record => {
                                    let createdAt = new Date(record.created_at).getTime(); // Convert `created_at` to timestamp
                                    let diffHours = (currentTime - createdAt) / (1000 * 60 * 60); // Convert milliseconds to hours
                                    let ncprNum = record.ncpr_num;

                                    if (diffHours >= 72) {
                                        record.overdueLevel = "72";
                                    } else if (diffHours >= 48) {
                                        record.overdueLevel = "48";
                                    } else if (diffHours >= 24) {
                                        record.overdueLevel = "24";
                                    } else {
                                        record.overdueLevel = null;
                                    }
                                    if (record.overdueLevel !== null) overdueNCPRs.push(record.ncpr_num);

                                    // ✅ Check if Urgent (record.urgent === "on")
                                    if (record.urgent === "on") {
                                        urgentNCPRs.push(record.ncpr_num);
                                        record.isUrgent = true;
                                    } else {
                                        record.isUrgent = false;
                                    }

                                    // Identify unseen NCPRs
                                    if (parseInt(record.id) > lastSeenId && !notifiedNCPRs.includes(ncprNum)) {
                                        unseenNCPRs.push(ncprNum);
                                    }
                                });

                                // Show warning if there are overdue NCPRs
                                if (overdueNCPRs.length > 0) {
                                    showWarningNotification(overdueNCPRs); // ✅ Show overdue notification
                                }

                                // Show warning if there are urgent NCPRs
                                /*if (urgentNCPRs.length > 0) {
                                    showUrgentNotification(urgentNCPRs); // ✅ New urgent notification for urgent cases
                                }*/

                                // Show notification for unseen NCPRs
                                if (unseenNCPRs.length > 0) {
                                    showNotification(unseenNCPRs, username);
                                    notifiedNCPRs.push(...unseenNCPRs); // Mark all as notified
                                    sessionStorage.setItem("notifiedNCPRs_" + username, JSON.stringify(notifiedNCPRs));
                                }

                                // Update last seen ID in sessionStorage and database
                                if (unseenNCPRs.length > 0) {
                                    let latestId = Math.max(...json.ncprs.map(item => parseInt(item.id)));
                                    sessionStorage.setItem("lastSeenId_" + username, latestId);
                                    updateLastSeenId(latestId);
                                }

                                firstLoad = false; // Ensure first load logic doesn't run again
                            }
                            return json.ncprs;
                        },

                        "cache": false
                    },
                    "columns": [{
                            "data": "id",
                            "visible": false
                        }, // Hide ID column
                        {
                            "data": "ncpr_num"
                        },
                        {
                            "data": "initiator"
                        },
                        {
                            "data": "status",
                            "className": "text-center",
                            "render": function(data, type, row) {
                                if (data === "open") {
                                    return '<span class="badge bg-success">open</span>';
                                } else if (data === "Close") {
                                    return '<span class="badge bg-danger">Close</span>';
                                } else {
                                    return '<span class="badge bg-secondary">' + data + '</span>';
                                }
                            }
                        },
                        {
                            "data": "statuses",
                            "visible": false
                        },
                        {
                            "data": "date"
                        },
                        {
                            "data": "id",
                            "render": function(data, type, row) {
                                let urgentIndicator = (row.isUrgent || row.overdueLevel) ?
                                    `<div class="urgent-indicator">URGENT</div>` :
                                    ""; // ✅ Conditional indicator for either urgent or overdue
                                // Show "Overdue/24hrs" indicator if row.isOverdue is true
                                let exceedIndicator = "";

                                if (row.overdueLevel === "24") {
                                    exceedIndicator = `<div class="urgent-indicator" style="top: 15px; background-color: orange;">Overdue/24hrs</div>`;
                                } else if (row.overdueLevel === "48") {
                                    exceedIndicator = `<div class="urgent-indicator" style="top: 15px; background-color: darkorange;">Overdue/48hrs</div>`;
                                } else if (row.overdueLevel === "72") {
                                    exceedIndicator = `<div class="urgent-indicator" style="top: 15px; background-color: red;">Overdue/72hrs</div>`;
                                }
                                let viewButton = `<button class="btn btn-primary btn-sm view-btn fw-bold" data-id="${row.ncpr_num}">
                                                    View
                                                </button>`;

                                let dispoButton = `<button class="btn btn-primary btn-sm dispo-btn fw-bold" 
                               data-id="${row.ncpr_num}" 
                               data-bs-toggle="modal" 
                               data-bs-target="#dispoModal">
                                    Disposition
                                </button>`;

                                let editButton = `<button class="btn btn-warning btn-sm edit-btn fw-bold" 
                              data-id="${row.ncpr_num}"> Edit
                          </button>`;

                                // Only show edit button if the NCPR is approved
                                let actionButtons = row.statuses === "Approved" ? editButton : dispoButton;

                                return `
                                <div class="action-container">
                                    ${urgentIndicator} <!-- Floating indicator -->
                                    ${exceedIndicator} <!-- Floating indicator -->
                                    ${viewButton}
                                    ${actionButtons}
                                </div>`;
                            }
                        }
                    ],
                    "order": [
                        [1, "desc"]
                    ],
                    "language": {
                        "emptyTable": "No Available NCPR Filing"
                    }
                });
                // 🔒 Expose only this secure refresh function
                window.refreshNcprTable = function() {
                    table.ajax.reload(null, false);
                };

                // Auto-refresh table every 5 seconds without resetting the table state
                setInterval(function() {
                    table.ajax.reload(null, false);
                }, 5000);

                $('#dispoModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget); // Button that triggered the modal
                    var ncprNum = button.data('id'); // Extract data-id

                    // Set the extracted value inside the modal
                    $('#modal-id').text(ncprNum); // Display in modal
                });

                // Function to update the last seen NCPR ID in the database
                function updateLastSeenId(newLastSeenId) {
                    $.post("update_last_seen.php", {
                        lastSeenId: newLastSeenId
                    }, function(response) {
                        console.log("Last Seen ID Updated: ", response);
                    });
                }

                function showNotification(ncprNums, user) {
                    let notificationBox = $("#notification-box");

                    let message;
                    if (ncprNums.length <= 5) {
                        // Show all NCPRs if the number is small
                        message = `Hello ${user}, new NCPR Numbers: ${ncprNums.join(", ")} have been added.`;
                    } else {
                        // Show a summary with the first few NCPRs
                        let previewNCPRs = ncprNums.slice(0, 3).join(", "); // Get the first 3 NCPRs
                        message = `Hello ${user}, ${ncprNums.length} new NCPRs have been added. (e.g., ${previewNCPRs}, ...)`;
                    }

                    // Display the notification
                    notificationBox.html(message).fadeIn().delay(5000).fadeOut();
                }

                function showWarningNotification(overdueNCPRs) {
                    let notificationBox = $("#warning-box"); // Assuming you have a separate warning box

                    // Check if the notification was already shown in this session
                    if (sessionStorage.getItem("warningShown")) {
                        return; // Exit function if already shown
                    }

                    let message = `⚠️ Warning: ${overdueNCPRs.length} NCPRs have exceeded 24 hours!`;

                    // If <= 5, list them; otherwise, show a summary
                    if (overdueNCPRs.length <= 5) {
                        message += ` Overdue NCPRs: ${overdueNCPRs.join(", ")}`;
                    }

                    notificationBox.html(message).fadeIn().delay(5000).fadeOut();

                    // Mark as shown in sessionStorage
                    sessionStorage.setItem("warningShown", "true");
                }
            })();
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

        $(document).ready(function() {
            $('#ncprTable tbody').on('click', '.edit-btn', function() {
                var ncprNum = $(this).data('id');
                var targetForm = $('#editDispoForm'); // Specify the form ID

                $("#modal-id").text(ncprNum); // Display ID inside modal

                $.ajax({
                    url: 'fetch_dispo_details.php', // New PHP script to fetch dispo_id
                    method: 'POST',
                    data: {
                        ncpr_num: ncprNum
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Encoded JSON response:", response);

                        targetForm.find('#modal-id').text(response.ncpr_num);
                        targetForm.find('#containment').text(response.containment); // Sets the text for textarea
                        targetForm.find('input[name="corrective_action"][value="' + response.corrective_action + '"]').prop('checked', true);
                        targetForm.find('input[name="potential_failure"][value="' + response.pff + '"]').prop('checked', true);

                        // Populate multiple checkboxes for cause of non-conformance
                        targetForm.find('input[name="cause[]"]').each(function() {
                            let isChecked = response.checkboxes.some(cb => cb.checkbox_name === $(this).val());
                            $(this).prop('checked', isChecked);
                        });

                        targetForm.find('#id_no').val(response.id_no);
                        targetForm.find('#name').val(response.name);
                        targetForm.find('input[name="car"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'CAR'));
                        targetForm.find('input[name="scar"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'SCAR'));
                        targetForm.find('input[name="car_no"]').val(response.car_no);
                        targetForm.find('input[name="scar_no').val(response.scar_no);

                        // Populate dispo checkboxes
                        targetForm.find('input[name="dispo_from[]"]').each(function() {
                            let isChecked = response.checkboxes.some(cb => cb.checkbox_name === $(this).val());
                            $(this).prop('checked', isChecked);
                        });

                        // Populate IARA checkboxes
                        targetForm.find('input[name="impact_analysis[]"]').each(function() {
                            let isChecked = response.checkboxes.some(cb => cb.checkbox_name === $(this).val());
                            $(this).prop('checked', isChecked);
                        });

                        targetForm.find('input[name="affected_business"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'CAR'));
                        targetForm.find('input[name="other_instructions"]').prop('checked', response.checkboxes.some(cb => cb.checkbox_name === 'SCAR'));

                        // Set BD report and MRB radio buttons
                        targetForm.find('input[name="bd_report"][value="' + response.bd_report + '"]').prop('checked', true);
                        targetForm.find('input[name="mrb"][value="' + response.mrb + '"]').prop('checked', true);
                        targetForm.find('input[name="customer_approval"][value="' + response.customer_approval + '"]').prop('checked', true);

                        // Populate product disposition checkboxes
                        targetForm.find('input[name="product_dispo[]"]').each(function() {
                            let isChecked = response.checkboxes.some(cb => cb.checkbox_name === $(this).val());
                            $(this).prop('checked', isChecked);
                        });

                        // Populate text fields
                        targetForm.find('#impact_analysis').val(response.notes || "");
                        targetForm.find('input[name="contact_person"').val(response.contact_person || "");
                        targetForm.find('input[name="other_specify"').val(response.other_specify || "");
                        targetForm.find('input[name="yield_off"').val(response.yield_off || "");
                        targetForm.find('input[name="da_no"').val(response.da_no || "");
                        targetForm.find('input[name="rework_da_no"').val(response.rework_da_no || "");
                        targetForm.find('input[name="repair_DA"').val(response.repair_DA || "");
                        targetForm.find('input[name="wis_no"').val(response.wis_no || "");
                        targetForm.find('input[name="scrap_amount"').val(response.scrap_amount || "");
                        targetForm.find('input[name="shipment_date"').val(response.shipment_date || "");
                        targetForm.find('input[name="document_alert"').val(response.document_alert || "");

                        if (Array.isArray(response.intervention_checkboxes) && response.intervention_checkboxes.length > 0) {
                            let intervention_cb = ['actions_taken', 'process_dispo', 'resumption_reason', 'instructions_detail', 'documents_revision']; // Add more names here if needed

                            targetForm.find('input[name="further_eval"]').prop('checked', response.intervention_checkboxes.some(cb => cb.checkbox_name === 'F1'));
                            intervention_cb.forEach(function(checkbox) {
                                targetForm.find('input[name="' + checkbox + '[]"]').each(function() {
                                    let checkboxValue = $(this).val();
                                    let isChecked = response.intervention_checkboxes.some(cb => cb.checkbox_name === checkboxValue);
                                    $(this).prop('checked', isChecked);
                                });
                            });
                        }

                        if (Array.isArray(response.intervention_inputs) && response.intervention_inputs.length > 0) {
                            let intervention_inp = ['affected_process', 'other_resumption', 'process_instruction', 'document_alert_s', 'other_specify_s', 'released_by'];

                            intervention_inp.forEach(function(input) {
                                let found = response.intervention_inputs.find(obj => obj.input_name === input);
                                if (found) {
                                    $(`[name="${input}"]`).val(found.inputted_data || "");
                                }
                            });
                        }

                        //filled the approvals
                        // Loop through the approvers and update the elements accordingly
                        if (response.approvers && response.approvers.length > 0) {
                            response.approvers.forEach(function(approver) {
                                if (approver.approver_role) {
                                    switch (approver.approver_role) {
                                        case "QA ENGINEER":
                                            $("#approvd_by_engineer").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            break;
                                        case "QA MANAGER":
                                            $("#approvd_by_supv_mgr").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            break;
                                        case "QA SUPERVISOR":
                                            $("#approvd_by_supv_mgr").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            break;
                                        case "SHELDAHL REPRESENTATIVE":
                                            $("#approvd_by_SheldahlRep").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            break;
                                            // Add more cases for other roles as needed
                                        default:
                                            // Handle default case if needed (optional)
                                            console.log("Unknown role:", approver.approver_role);
                                            break;
                                    }
                                }
                            });
                        }

                        $('#editDispoModal').modal('show');
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