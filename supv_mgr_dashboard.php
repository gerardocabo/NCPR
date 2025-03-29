<?php
require "config.php";
$user_role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
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
                    <a href="#"><?php echo htmlspecialchars($user_role) ?></a>
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
                    <a href="productkey.php" class="sidebar-link">
                        <i class="fa-solid fa-helmet-safety"></i>
                        <span>Product Key</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="status.php" class="sidebar-link">
                        <i class="fa-solid fa-paperclip"></i>
                        <span>Engineer List</span>
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
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/folder.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
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
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/open.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
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
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/close.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
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
                                                <p class="mb-0">#</p>
                                            </div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-end">
                                            <img src="asset/eng.png" alt="Icon" class="img-fluid" style="width: 100px; height: 100px;">
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
                                    <th>Action</th>
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

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/bootstrap/js/all.min.js"></script>
    <script src="assets/vendor/bootstrap/js/fontawesome.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <script src="assets/js/approval.js"></script>

    <!-- DataTable Initialization -->
    <script>
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
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        className: 'btn btn-primary'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-warning'
                    }
                ],
                "ajax": {
                    "url": "fetch_ncpr.php",
                    "type": "GET",
                    "dataSrc": function(json) {
                        if (json.ncprs.length > 0) {
                            if (firstLoad) {
                                // Set last seen ID from the server on first load
                                lastSeenId = json.lastSeenId;
                                sessionStorage.setItem("lastSeenId_" + username, lastSeenId);
                            } else {
                                // Retrieve lastSeenId from sessionStorage
                                lastSeenId = parseInt(sessionStorage.getItem("lastSeenId_" + username)) || 0;
                            }

                            // Filter new records based on last seen ID
                            let newRecords = json.ncprs.filter(item => parseInt(item.id) > lastSeenId);

                            // Retrieve previously notified NCPRs
                            notifiedNCPRs = JSON.parse(sessionStorage.getItem("notifiedNCPRs_" + username) || "[]");

                            // Collect unseen, unique NCPRs
                            let unseenNCPRs = [];

                            newRecords.forEach(ncprNum => {
                                if (!notifiedNCPRs.includes(ncprNum)) {
                                    unseenNCPRs.push(ncprNum); // Add to unseen list
                                }
                            });

                            // If there are unseen NCPRs, notify user
                            if (unseenNCPRs.length > 0) {
                                showNotification(unseenNCPRs, username); // ✅ Updated function
                                notifiedNCPRs.push(...unseenNCPRs); // ✅ Mark all as notified
                            }

                            // Persist updated notifiedNCPRs list
                            sessionStorage.setItem("notifiedNCPRs_" + username, JSON.stringify(notifiedNCPRs));

                            // ✅ Now update last seen ID ONLY IF new unseen records exist
                            if (newRecords.length > 0) {
                                let latestId = Math.max(...newRecords.map(item => parseInt(item.id)));
                                sessionStorage.setItem("lastSeenId_" + username, latestId);
                                updateLastSeenId(latestId); // Update in the database
                            }

                            // ✅ Debugging logs (remove after testing)
                            console.log("Last Seen ID:", lastSeenId);
                            console.log("New Records:", newRecords.map(r => r.id));
                            console.log("Unseen NCPRs Notified:", unseenNCPRs);
                        }

                        firstLoad = false; // Ensure first load logic doesn't run again
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
                        "data": "status"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "id",
                        "render": function(data, type, row) {
                            return `
                        <button class="btn btn-primary btn-sm view-btn" data-id="${row.ncpr_num}">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-success btn-sm dispo-btn" 
                                data-id="${row.ncpr_num}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#dispoModal"><i class="fas fa-check"></i>
                            Dispo
                        </button>`;
                        }
                    }
                ],
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "emptyTable": "No Available NCPR Filing"
                }
            });
            
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

            // Auto-refresh table every 5 seconds without resetting the table state
            /*setInterval(function() {
                table.ajax.reload(null, false);
            }, 5000);*/

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
                    success: function(response) {
                        // Log the full response for debugging
                        console.log("Encoded JSON response:", response);

                        console.log("Dispo ID found. Disabling inputs.", response);

                        // Populate fields with existing data
                        $('#modal-id').text(response.ncpr_num);

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

            const modalElement = document.getElementById("dispoModal");
            const modal = new bootstrap.Modal(modalElement);
            const closeModalButtons = document.querySelectorAll("#closeModal, #closeModalFooter");

            closeModalButtons.forEach(button => {
                button.addEventListener("click", function() {
                    modal.hide(); // Close the modal only when close button is clicked
                });
            });
        });
    </script>
</body>

</html>