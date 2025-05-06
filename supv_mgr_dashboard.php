<?php
require "config.php";
require "conn.php";
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
    <title>NCPR - Dashboard</title>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <style>
        .locked {
            pointer-events: none;
            /* Prevent clicking */
        }

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
                    <a href="supv_mgr_dashboard.php" class="sidebar-link">
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
                        <h5 class="mb-3">NCPR Table</h5>
                    </div>
                    <div class="table-container table-responsive mt-3">
                        <table id="ncprTable" class="table table-bordered table-hover" style="width:100% text-center">
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
                                    <th class="text-center">Status</th>
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

    <!-- Dispo Approval Modal -->
    <div class="modal fade" id="dispoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Disposition Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here  -->
                    <?php include "viewdisposition.php"; ?>
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
                                    lastSeenId = json.lastSeenId;
                                    sessionStorage.setItem("lastSeenId_" + username, lastSeenId);
                                } else {
                                    let storedLastSeen = sessionStorage.getItem("lastSeenId_" + username);
                                    lastSeenId = storedLastSeen ? parseInt(storedLastSeen) : 0;
                                }

                                notifiedNCPRs = JSON.parse(sessionStorage.getItem("notifiedNCPRs_" + username) || "[]");

                                let newRecords = json.ncprs.filter(item => parseInt(item.id) > lastSeenId);

                                json.ncprs.forEach(record => {
                                    let createdAt = new Date(record.created_at).getTime();
                                    let diffHours = (currentTime - createdAt) / (1000 * 60 * 60); // Convert milliseconds to hours

                                    // ✅ Check if Overdue (Older than 24 hours)
                                    /*if (diffHours > 24) {
                                        overdueNCPRs.push(record.ncpr_num);
                                        record.isOverdue = true;
                                    } else {
                                        record.isOverdue = false;
                                    }*/

                                    // ✅ Check if Urgent (record.urgent === "on")
                                    if (record.urgent === "on") {
                                        urgentNCPRs.push(record.ncpr_num);
                                        record.isUrgent = true;
                                    } else {
                                        record.isUrgent = false;
                                    }

                                    if (parseInt(record.id) > lastSeenId && !notifiedNCPRs.includes(record.ncpr_num)) {
                                        unseenNCPRs.push(record.ncpr_num);
                                        notifiedNCPRs.push(record.ncpr_num);
                                    }
                                });

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
                            }

                            firstLoad = false;
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
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "initiator",
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "process",
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "date",
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "part_number",
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "part_name",
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "issue",
                            "className": "text-center" // Center the initiator column
                        },
                        {
                            "data": "status",
                            "className": "text-center",
                            "render": function(data, type, row) {
                                if (data === "Open") {
                                    return '<span class="badge bg-warning text-dark">Open</span>';
                                } else if (data === "Canceled" || data === "Cancel") {
                                    return '<span class="badge bg-danger">Canceled</span>';
                                } else if (data === "Rejected") {
                                    return '<span class="badge bg-warning text-black">Pending</span>';
                                } else {
                                    return '<span class="badge bg-secondary">' + data + '</span>';
                                }
                            }
                        },
                        {
                            "data": "id",
                            "render": function(data, type, row) {
                                let urgentIndicator = row.isUrgent ? `<div class="urgent-indicator">URGENT</div>` : ""; // ✅ Conditional indicator
                                let viewButton = `<button class="btn btn-primary btn-sm view-btn fw-bold text-center" data-id="${row.ncpr_num}">
                            View
                          </button>`;
                                let dispoButton = `<button class="btn btn-primary btn-sm dispo-btn fw-bold text-center" 
                               data-id="${row.ncpr_num}" 
                               data-bs-toggle="modal" 
                               data-bs-target="#dispoModal">
                               Disposition
                           </button>`;
                                return `
                                 <div class="action-container d-flex justify-content-center">
                                    ${urgentIndicator} <!-- Floating indicator -->
                                    ${viewButton}
                                    ${dispoButton}
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
                    beforeSend: function() {
                        Swal.fire({
                            title: "Loading...",
                            text: "Fetching disposition details...",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        // Populate fields with existing data
                        let $dispoStatus = response.dispo_status;
                        if ($dispoStatus === "Cancel") {
                            document.querySelectorAll('.cancel_removed').forEach(el => {
                                el.style.display = "none";
                            })
                            let el = document.querySelector('li.cancel-approved a[data-action="cancel"][data-role="QA Manager"]');
                            if (el) el.textContent = "Approve";
                        }
                        if ($dispoStatus === "Rejected") {
                            document.querySelectorAll('.cancel_removed').forEach(elreject => {
                                elreject.style.display = "none";
                            })
                            const el = document.querySelector('li.re-approve');
                            if (el) {
                                el.classList.remove('d-none'); // Show the element
                                // or el.classList.add('d-none'); // Hide the element
                            } else {
                                console.warn('Element not found');
                            }
                        }

                        $('#modal-id').text(response.ncpr_num);

                        if ($dispoStatus === "Rejected") {
                            document.getElementById('RR_display').classList.remove("d-none"); // Corrected DOM manipulation
                            $('#reject_reason_display').text(response.RR_display); // jQuery for text update
                        }

                        //$('#containment').val(response.containment);
                        $('#containment').text(response.containment); // Sets the text content
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
                        $('#notes').text(response.impact_analysis || "");
                        $('#contact_person').text(response.contact_person || "");
                        $('#other_specify').text(response.other_specify || "");
                        $('#yield_off').text(response.yield_off || "");
                        $('#da_no').text(response.da_no || "");
                        $('#rework_da_no').text(response.rework_da_no || "");
                        $('#wis_no').text(response.wis_no || "");
                        $('#scrap_amount').text(response.scrap_amount || "");
                        $('#shipment_date').text(response.shipment_date || "");
                        $('#document_alert').text(response.document_alert || "");

                        if (Array.isArray(response.intervention_checkboxes) && response.intervention_checkboxes.length > 0) {
                            let intervention_cb = ['actions_taken', 'process_dispo', 'resumption_reason', 'instructions_detail', 'documents_revision']; // Add more names here if needed

                            $('input[name="further_eval"]').prop('checked', response.intervention_checkboxes.some(cb => cb.checkbox_name === 'F1'));
                            intervention_cb.forEach(function(checkbox) {
                                $('input[name="' + checkbox + '[]"]').each(function() {
                                    let checkboxValue = $(this).val();
                                    let isChecked = response.intervention_checkboxes.some(cb => cb.checkbox_name === checkboxValue);
                                    $(this).prop('checked', isChecked);
                                });
                            });
                        }

                        if (Array.isArray(response.intervention_inputs) && response.intervention_inputs.length > 0) {
                            let intervention_inp = ['affected_process', 'other_resumption', 'process_instruction',
                                'document_alert_s', 'other_specify_s', 'released_by',
                                'acknowledgment_signature', 'head_signature', 'prod_manager_signature'
                            ];

                            intervention_inp.forEach(function(input) {
                                let found = response.intervention_inputs.find(obj => obj.input_name === input);

                                if (found) {
                                    let $el = $('#' + input);

                                    if ($el.length && ($el.is('div') || $el.is('span'))) {
                                        $el.text(found.inputted_data || "");
                                    } else {
                                        $(`[name="${input}"]`).val(found.inputted_data || "");
                                    }
                                }
                            });

                            // use this if it change into not input tag or view only .
                            /* 
                            intervention_inp.forEach(function(input) {
                                let found = response.intervention_inputs.find(obj => obj.input_name === input);
                                if (found) {
                                    $('#' + input).text(found.inputted_data || "");
                                }
                            });*/
                        }

                        //filled the approvals
                        // Loop through the approvers and update the elements accordingly
                        if (response.approvers && response.approvers.length > 0) {
                            response.approvers.forEach(function(approver) {
                                if (approver.approver_role) {
                                    switch (approver.approver_role) {
                                        case "PCO":
                                            $("#approvd_by_engineer").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            $("#dt_engineer").text(approver.approval_date);
                                            break;
                                        case "QA ENGINEER":
                                            $("#approvd_by_engineer").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            $("#dt_engineer").text(approver.approval_date);
                                            break;
                                        case "QA MANAGER":
                                            $("#approvd_by_supv_mgr").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            $("#dt_supv_mgr").text(approver.approval_date);
                                            break;
                                        case "QA SUPERVISOR":
                                            $("#approvd_by_supv_mgr").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            $("#dt_supv_mgr").text(approver.approval_date);
                                            break;
                                        case "SHELDAHL REPRESENTATIVE":
                                            $("#approvd_by_SheldahlRep").text(
                                                approver.fname + " " + approver.lname
                                            );
                                            $("#dt_SheldahlRep").text(approver.approval_date);
                                            break;
                                            // Add more cases for other roles as needed
                                        default:
                                            // Handle default case if needed (optional)
                                            // here
                                            break;
                                    }
                                }
                            });
                        }

                        //field for file query
                        const fileList = $("#fileList");
                        fileList.empty(); // Clear old stuff

                        if (
                            Array.isArray(response.files_attach) &&
                            response.files_attach.length > 0
                        ) {
                            response.files_attach.forEach((file) => {
                                const fileBox = $("<div>").addClass("mb-3 p-2 border rounded");

                                const button = $("<button>")
                                    .addClass("btn btn-primary btn-sm")
                                    .text(file.name)
                                    .on("click", function(e) {
                                        e.preventDefault();

                                        const fileUrl = file.path;

                                        // Try to fetch headers and check size
                                        fetch(fileUrl, {
                                                method: "HEAD"
                                            })
                                            .then((res) => {
                                                const size = parseInt(
                                                    res.headers.get("Content-Length"),
                                                    10
                                                );

                                                // If under 3MB, open in new tab
                                                if (size && size < 3 * 1024 * 1024) {
                                                    window.open(fileUrl, "_blank");
                                                } else {
                                                    // Otherwise, force download
                                                    const a = document.createElement("a");
                                                    a.href = fileUrl;
                                                    a.download = file.name;
                                                    document.body.appendChild(a);
                                                    a.click();
                                                    document.body.removeChild(a);
                                                }
                                            })
                                            .catch((err) => {
                                                alert("Failed to fetch file info. Opening normally...");
                                                window.open(fileUrl, "_blank");
                                            });
                                    });

                                fileBox.append(button);
                                fileList.append(fileBox);
                            });
                        } else {
                            fileList.append(
                                $("<p>").addClass("text-muted").text("No file attachments found.")
                            );
                        }

                        $('#dispoModal').modal('show');
                    },
                    error: function() {
                        try {
                            // Only minimal info for the console
                            console.error("Failed to fetch disposition data.");

                            // Friendly alert to users
                            alert("Oops! Something went wrong. Please try again later or contact support.");
                        } catch (err) {
                            console.error("Unexpected error during error handling!");
                            alert("A critical error occurred. Please try refreshing the page.");
                        }
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

            const modalElement = document.getElementById("dispoModal");
            const modal = new bootstrap.Modal(modalElement);
            const closeModalButtons = document.querySelectorAll("#closeModal, #closeModalFooter");

            closeModalButtons.forEach(button => {
                button.addEventListener("click", function() {
                    modal.hide(); // Close the modal only when close button is clicked
                });
            });

            // Add event listeners for submenu toggling
            var submenuItems = document.querySelectorAll('.dropdown-submenu');
            submenuItems.forEach(function(submenu) {
                submenu.addEventListener('click', function(e) {
                    var dropdownMenu = submenu.querySelector('.dropdown-menu');
                    if (dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                    } else {
                        dropdownMenu.classList.add('show');
                    }
                    e.stopPropagation();
                });
            });
        });
    </script>
</body>

</html>