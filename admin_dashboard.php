<?php
require "conn.php";
require "config.php";
$name = $_SESSION["user"];

$user_role = $_SESSION['role'];

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
    <title>admin Dashboard</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
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

    #loader {
        display: none;
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
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
        top: -10px;
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
</style>

<body class="bg-white">
    <div id="loader"></div>
    <div class="wrapper bg-white">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#"><?php echo $name ?></a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item active">
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
                <li class="sidebar-item">
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
                    <a href="ncprlist.php" class="text-decoration-none">
                        <div class="card text-white mb-3 shadow-sm border-0 hover-shadow">
                            <div class="card border-0 shadow-sm flex-fill hover-shadow">
                                <div class="card-body p-0 d-flex flex-fill">
                                    <div class="row g-5 align-items-center">
                                        <div class="col-6">
                                            <div class="p-3 m-1">
                                                <h5>NCPR Files</h5>
                                                <p class="mb-0 fw-bold"><?= $open_ncpr_count ?></p>
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
                    <a href="ncprlist.php" class="text-decoration-none">
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
                    <a href="productkey.php" class="text-decoration-none">
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
                    <a href="status.php" class="text-decoration-none">
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
                        <h5 class="mb-3">NCPR Table</h5>
                    </div>
                    <div class="table-container table-responsive mt-3">
                        <table id="ncprTable" class="table table-bordered table-hover" style="width:100%">
                            <thead class="table-secondary">
                                <tr>
                                    <th hidden>ID</th>
                                    <th>NCPR Number</th>
                                    <th>Initiator</th>
                                    <th>Status</th>
                                    <th>Date</th>
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
                    <!-- Content will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Dispo-ing Modal Structure -->
    <div class="modal fade" id="dispoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dispositioning</h5>
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


    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>

    <!-- DataTable Initialization -->
    <script>
        $("#loader").show(); // Show loader
        $(document).ready(function() {

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
                "processing": false, // Show loading message while processing
                "ajax": {
                    "url": "fetch_ncpr.php",
                    "type": "GET",
                    "dataSrc": function(json) {
                        if (firstLoad) {
                            $('#ncprTable').DataTable().processing(true); // show "Processing..."
                        }

                        if (json.ncprs.length > 0) {
                            let currentTime = new Date().getTime(); // Get current timestamp in milliseconds
                            let overdueNCPRs = [];
                            let urgentNCPRs = [];
                            let unseenNCPRs = [];

                            if (firstLoad) {
                                lastSeenId = json.lastSeenId;
                                sessionStorage.setItem("lastSeenId_" + username, lastSeenId);
                            } else {
                                let storedLastSeen = sessionStorage.getItem("lastSeenId_" + username);
                                lastSeenId = storedLastSeen ? parseInt(storedLastSeen) : 0;
                            }

                            notifiedNCPRs = JSON.parse(sessionStorage.getItem("notifiedNCPRs_" + username) || "[]");

                            json.ncprs.forEach(record => {
                                let createdAt = new Date(record.created_at).getTime();
                                let diffHours = (currentTime - createdAt) / (1000 * 60 * 60); // Convert ms to hours

                                // ✅ Check if Overdue (Older than 24 hours)
                                if (diffHours >= 72) {
                                    record.overdueLevel = "72";
                                } else if (diffHours >= 48) {
                                    record.overdueLevel = "48";
                                } else if (diffHours >= 24) {
                                    record.overdueLevel = "24";
                                } else {
                                    record.overdueLevel = null;
                                }

                                // Set urgent flag
                                record.isUrgent = record.urgent === "on";

                                // Push to appropriate arrays
                                if (record.isUrgent) urgentNCPRs.push(record.ncpr_num);
                                if (record.isOverdue) overdueNCPRs.push(record.ncpr_num);

                                // Check if unseen
                                if (!notifiedNCPRs.includes(record.ncpr_num)) {
                                    unseenNCPRs.push(record.ncpr_num);
                                    notifiedNCPRs.push(record.ncpr_num);
                                }
                            });


                            // ✅ Show overdue warning if there are overdue NCPRs
                            if (overdueNCPRs.length > 0) {
                                showWarningNotification(overdueNCPRs, username);
                            }

                            // ✅ Show urgent warning if there are urgent NCPRs
                            if (urgentNCPRs.length > 0) {
                                showWarningNotification(urgentNCPRs, "Urgent");
                            }

                            // ✅ Show notification for new unseen NCPRs
                            if (unseenNCPRs.length > 0) {
                                showNotification(unseenNCPRs, username);
                            }

                            sessionStorage.setItem("notifiedNCPRs_" + username, JSON.stringify(notifiedNCPRs));

                            console.log("Last Seen ID:", lastSeenId);
                            console.log("Unseen NCPRs Notified:", unseenNCPRs);
                            console.log("Overdue NCPRs:", overdueNCPRs);
                            console.log("Urgent NCPRs:", urgentNCPRs);
                        }

                        firstLoad = false;
                        $('#ncprTable').DataTable().processing(false); // hide "Processing..."
                        return json.ncprs;

                    },

                    "cache": false
                },
                "columns": [{
                        "data": "id",
                        "visible": false
                    }, // Hide ID column
                    {
                        "data": "ncpr_num",
                        "className": "text-center"
                    },
                    {
                        "data": "initiator",
                        "className": "text-center"
                    },
                    {
                        "data": "status",
                        "className": "text-center",
                        "render": function(data, type, row) {
                            if (data === "open") {
                                return '<span class="badge bg-success">Open</span>';
                            } else if (data === "Close") {
                                return '<span class="badge bg-danger">Close</span>';
                            } else {
                                return '<span class="badge bg-secondary">' + data + '</span>';
                            }
                        }
                    },
                    {
                        "data": "date",
                        "className": "text-center"
                    },
                    {
                        "data": "id",
                        "render": function(data, type, row) {
                            // Show "URGENT" indicator if row.urgent is true
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

                            return `
                                <div class="action-container">
                                    ${urgentIndicator}
                                    ${exceedIndicator}
                                    ${viewButton}
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

            // Auto-refresh table every 5 seconds without resetting the table state
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 5000);

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