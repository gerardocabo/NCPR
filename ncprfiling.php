<?php
include "config.php"
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>admin Dashboard</title>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
</head>

<body>
    <div class="wrapper">
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
                <li class="sidebar-item active">
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
            <form id="ncprForm" method="POST" enctype="multipart/form-data">
                <div class="card border-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <img src="assets/img/Picture1.png" alt="Logo" style="height: 50px; object-fit: contain;">
                        <img src="assets/img/Picture2.png" alt="Logo" style="height: 50px; object-fit: contain;">
                    </div>

                    <div class="card border p-2 mb-3 text-center">
                        <h3 class="fw-bold mb-0">Non-Conforming Product Record</h3>
                    </div>
                    <div class="card">
                        <div class="card-body mt-2">
                            <div class="row g-0">
                                <div class="col-md-9">
                                    <div class="d-flex flex-wrap gap-3 mb-1 g-0 m-0 p-0">
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="initiator" name="initiator" placeholder="Initiator" required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                            <label for="initiator">Initiator:</label>
                                        </div>

                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="date" class="form-control" id="dateInput" name="date" placeholder="Enter a date" required>
                                            <label for="dateInput">Date/shift:</label>
                                        </div>

                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="ncpr_num" name="ncpr_num" placeholder="Enter NCPR Number" readonly required>
                                            <label for="ncpr_num">NCPR Number:</label>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-3 mb-1">
                                        <div class="form-floating g-0 position-relative" style="flex: 1; min-width: 250px;">
                                            <input type="text" id="part_number" name="part_number" class="form-control"
                                                style="padding-right: 40px;" placeholder="Part Number" onkeyup="liveSearch()" autocomplete="off" required>
                                            <label for="part_number">Part Number/Model Number:</label>
                                            <!-- Dropdown List -->
                                            <ul id="dropdownList" class="list-group position-absolute bg-white border rounded"
                                                style="display: none; top: 100%; left: 0; width: 100%; max-height: 150px; overflow-y: auto; z-index: 1000;">
                                                <?php
                                                include 'conn.php'; // Include your existing connection file

                                                // Fetch part numbers from the product_list table
                                                $sql = "SELECT part_number FROM product_list";
                                                $result = $conn->query($sql);

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<li class='list-group-item' style='cursor: pointer;' onclick='selectValue(this)'>" .
                                                            htmlspecialchars($row["part_number"]) .
                                                            "</li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <script>
                                            function liveSearch() {
                                                let input = document.getElementById("part_number").value;
                                                let dropdown = document.getElementById("dropdownList");

                                                // Clear old results
                                                dropdown.innerHTML = "";

                                                if (input.length === 0) {
                                                    dropdown.style.display = "none";
                                                    return;
                                                }

                                                let xhr = new XMLHttpRequest();
                                                xhr.onreadystatechange = function() {
                                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                                        dropdown.innerHTML = xhr.responseText;

                                                        // Only show dropdown if there are new results
                                                        dropdown.style.display = dropdown.innerHTML.trim() !== "" ? "block" : "none";
                                                    }
                                                };
                                                xhr.open("GET", "search.php?query=" + encodeURIComponent(input), true);
                                                xhr.send();
                                            }

                                            function selectValue(element) {
                                                document.getElementById("part_number").value = element.textContent;
                                                document.getElementById("dropdownList").style.display = "none";
                                            }

                                            // Hide dropdown when clicking outside
                                            document.addEventListener("click", function(event) {
                                                let dropdown = document.getElementById("dropdownList");
                                                let inputField = document.getElementById("part_number");

                                                if (!inputField.contains(event.target) && !dropdown.contains(event.target)) {
                                                    dropdown.style.display = "none";
                                                }
                                            });
                                        </script>

                                        <div class="form-floating g-0 position-relative" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" id="part_name" name="part_name" placeholder="Enter Part Description" oninput="fetchSuggestions(this.value)" autocomplete="off" required>
                                            <label>Part Description:</label>
                                            <ul id="suggestionsList" class="list-group position-absolute bg-white border rounded"
                                                style="display: none; top: 100%; left: 0; width: 100%; max-height: 150px; overflow-y: auto; z-index: 1000;">
                                            </ul>
                                        </div>
                                        <script>
                                            function fetchSuggestions(query) {
                                                let suggestionsList = document.getElementById("suggestionsList");

                                                // Clear previous results
                                                suggestionsList.innerHTML = "";

                                                if (query.length === 0) {
                                                    suggestionsList.style.display = "none";
                                                    return;
                                                }

                                                fetch("fetch_part_names.php?query=" + encodeURIComponent(query))
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.length > 0) {
                                                            data.forEach(item => {
                                                                let li = document.createElement("li");
                                                                li.classList.add("list-group-item");
                                                                li.style.cursor = "pointer";
                                                                li.textContent = item;
                                                                li.onclick = function() {
                                                                    document.getElementById("part_name").value = this.textContent;
                                                                    suggestionsList.style.display = "none";
                                                                };
                                                                suggestionsList.appendChild(li);
                                                            });
                                                            suggestionsList.style.display = "block";
                                                        } else {
                                                            // Clear the list and hide it when no results are found
                                                            suggestionsList.innerHTML = "";
                                                            suggestionsList.style.display = "none";
                                                        }
                                                    })
                                                    .catch(error => console.error("Error:", error));
                                            }

                                            // Hide suggestions when clicking outside
                                            document.addEventListener("click", function(event) {
                                                let suggestionsList = document.getElementById("suggestionsList");
                                                let inputField = document.getElementById("part_name");

                                                if (!inputField.contains(event.target) && !suggestionsList.contains(event.target)) {
                                                    suggestionsList.style.display = "none";
                                                }
                                            });
                                        </script>

                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                            <input type="text" class="form-control" name="process" placeholder="Enter Part Description" required>
                                            <label>Process:</label>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3 mb-1">
                                        <div class="form-floating g-0" style="flex: 1; min-width: 250px; position: relative;">
                                            <div class="form-control d-flex align-items-center" style="height: 58px; padding-left: 12px;">
                                                <label for="supplierCheckbox" class="form-check-label">Include Supplier Details</label>
                                                <input type="checkbox" id="supplierCheckbox" class="form-check-input ms-2" onclick="toggleSupplierFields()">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="supplierSection" style="display: none;">
                                        <div class="d-flex flex-wrap gap-3 mb-1">
                                            <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                                <input type="text" class="form-control" id="supplier_part_name" name="supplier_part_name" placeholder="Enter Supplier Part Name">
                                                <label>Supplier Part Name:</label>
                                            </div>

                                            <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                                <input type="text" class="form-control" name="invoice_num" placeholder="Enter Invoice Number">
                                                <label>Invoice Number:</label>
                                            </div>
                                        </div>

                                        <!-- Second Row: Supplier, Supplier Part Number, Purchase Order -->
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                                <input type="text" class="form-control" name="supplier" placeholder="Enter Supplier">
                                                <label>Supplier:</label>
                                            </div>

                                            <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                                <input type="text" class="form-control" id="supplier_part_number" name="supplier_part_number" placeholder="Enter Supplier Part Number">
                                                <label>Supplier Part Number:</label>
                                            </div>

                                            <div class="form-floating g-0" style="flex: 1; min-width: 250px;">
                                                <input type="text" class="form-control" name="purchase_order" placeholder="Enter Purchase Order">
                                                <label>Purchase Order:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Box -->
                                <div class="col-md-3">
                                    <div class="right-box p-5 border h-100 text-center">
                                        <h3 style="color: red; font-weight: bold;">URGENT!</h3>
                                        <span>Check the checkbox if the held parts is a potential OTD Miss Shipment.</span>

                                        <!-- Large Checkbox -->
                                        <div class="mt-2">
                                            <input type="hidden" name="urgent" value="off">
                                            <input type="checkbox" id="urgent" name="urgent" class="form-check-input" style="transform: scale(1.8);" value="on">
                                            <label for="urgent" class="ms-2 fw-bold">Mark as Urgent</label>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function toggleDropdown() {
                                    let dropdown = document.getElementById("dropdownList");
                                    dropdown.classList.toggle("d-block");
                                }

                                function selectValue(element) {
                                    let inputField = document.getElementById("part_number");
                                    inputField.value = element.textContent;
                                    document.getElementById("dropdownList").classList.remove("d-block");

                                    // Manually trigger the input event to activate autofill logic
                                    inputField.dispatchEvent(new Event("input"));
                                }

                                document.getElementById("part_number").addEventListener("input", function() {
                                    let partNumber = this.value;

                                    if (partNumber.length > 0) {
                                        fetch("check_part.php?part_number=" + partNumber)
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.exists) {
                                                    document.getElementById("part_name").value = data.part_name;
                                                    document.getElementById("part_name").readOnly = true; // Lock if found
                                                } else {
                                                    document.getElementById("part_name").value = "";
                                                    document.getElementById("part_name").readOnly = false; // Allow input for new entry
                                                }
                                            })
                                            .catch(error => console.error("Error:", error));
                                    } else {
                                        document.getElementById("part_name").value = "";
                                        document.getElementById("part_name").readOnly = false;
                                    }
                                });

                                // Close dropdown when clicking outside
                                document.addEventListener("click", function(event) {
                                    let dropdown = document.getElementById("dropdownList");
                                    let container = document.querySelector(".position-relative"); // Use Bootstrap-based container
                                    if (!container.contains(event.target)) {
                                        dropdown.classList.remove("d-block");
                                    }
                                });

                                document.addEventListener("DOMContentLoaded", function() {
                                    let today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
                                    document.getElementById("dateInput").value = today;

                                    const supplierCheckbox = document.getElementById("supplierCheckbox");
                                    const supplierSection = document.getElementById("supplierSection");
                                    const supplierFields = supplierSection.querySelectorAll("input");
                                    const submitButton = document.getElementById("submitButton"); // Ensure your submit button has an ID

                                    function toggleSupplierFields() {
                                        if (supplierCheckbox.checked) {
                                            supplierSection.style.display = "block";
                                            supplierFields.forEach(field => {
                                                field.setAttribute("required", "required");
                                                field.classList.add("is-invalid"); // Ensure Bootstrap validation is applied
                                            });
                                        } else {
                                            supplierSection.style.display = "none";
                                            supplierFields.forEach(field => {
                                                field.removeAttribute("required");
                                                field.classList.remove("is-invalid"); // Remove validation styles when hiding
                                            });
                                        }
                                    }

                                    // Attach event listener
                                    supplierCheckbox.addEventListener("change", function() {
                                        toggleSupplierFields();
                                        checkFields(); // Ensure validation updates when toggling the checkbox
                                    });

                                    function checkFields() {
                                        let allFilled = true;
                                        const requiredFields = document.querySelectorAll("input[required], textarea[required]");

                                        requiredFields.forEach(field => {
                                            if (!field.value.trim()) {
                                                field.classList.add("is-invalid"); // Bootstrap invalid class (red border)
                                                allFilled = false;
                                            } else {
                                                field.classList.remove("is-invalid");
                                            }
                                        });

                                        if (!allFilled) {
                                            submitButton.classList.add("btn-danger");
                                            submitButton.innerHTML = "Submit <span style='color: yellow;'>&#9888;</span>";
                                        } else {
                                            submitButton.classList.remove("btn-danger");
                                            submitButton.innerHTML = "Submit";
                                        }
                                    }

                                    // Re-run validation when user types in fields
                                    document.querySelectorAll("input, textarea").forEach(field => {
                                        field.addEventListener("input", checkFields);
                                    });

                                    submitButton.addEventListener("click", function(event) {
                                        checkFields();
                                        if (submitButton.classList.contains("btn-danger")) {
                                            event.preventDefault(); // Prevent submission if there are empty fields
                                        }
                                    });
                                });
                            </script>
                            <!-- JavaScript to Toggle Fields -->
                            <script>
                                function toggleSupplierFields() {
                                    let isChecked = document.getElementById("supplierCheckbox").checked;
                                    let supplierSection = document.getElementById("supplierSection");

                                    supplierSection.style.display = isChecked ? "block" : "none";
                                }
                            </script>
                            <table class="table table-bordered text-center align-middle" id="materialTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size: 12px;">NT DJ Number</th>
                                        <th style="font-size: 12px;">Material/NFLD Lot/Sublot No.</th>
                                        <th style="font-size: 12px;">Lot/ Sublot Quantity</th>
                                        <th style="font-size: 12px;">Quantity Affected</th>
                                        <th style="font-size: 12px;">Defect Rate</th>
                                        <th style="font-size: 12px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td><input type="text" class="form-control" name="ntdj_num[]" required></td>
                                        <td><input type="text" class="form-control" name="mns_num[]" required></td>
                                        <td><input type="number" class="form-control" name="lot_sublot_qty[]" required></td>
                                        <td class="d-flex"><input type="number" class="form-control" name="qty_affected[]" required><input type="text" class="form-control" name="qty_affected_text[]" placeholder="Enter text"></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" name="defect_rate[]" readonly required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">Remove</button></td>
                                    </tr>
                                </tbody>
                            </table>

                            <button type="button" id="addRowBtn" class="btn btn-primary btn-sm">Add Material Detail</button>

                            <div class="row mt-3 border m-0">
                                <div class="col-md-3 border p-0">
                                    <div class="form-floating">
                                        <textarea id="issue" name="issue" class="form-control form-control-lg" placeholder="Issue call-out" style="height: 120px; overflow-y: hidden; width: 100%;" required oninput="autoExpand(this)"></textarea>
                                        <label for="issue" style="font-size: 12px; display: block; word-wrap: break-word; white-space: normal;">Issue call-out:</label>
                                    </div>
                                </div>
                                <div class="col-md-4 border p-1">
                                    <div class="text-center p-0 m-0">
                                        <span style="font-size: 10px">Issue in Detail</span>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="d-flex" style="align-items: baseline; width: fit-content;">
                                                <label for="awpi" class="form-label me-2"
                                                    style="font-size: 10px; white-space: nowrap; margin-bottom: 0;">
                                                    AWPI:
                                                </label>
                                                <input type="text" id="awpi" name="awpi" class="form-control form-control"
                                                    style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 160px;">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="d-flex" style="align-items: baseline; width: fit-content;">
                                                <label for="dc" class="form-label me-2" style="font-size: 10px; white-space: nowrap; margin-bottom: 0;">DC:</label>
                                                <input type="text" id="dc" name="dc" class="form-control form-control" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 160px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-0 mb-1">
                                        <div class="d-flex align-items-center">
                                            <label style="font-size: 10px; margin-right: 29px;">Deviation:</label>

                                            <input type="checkbox" id="deviation_yes" name="deviation" value="Yes" class="me-1">
                                            <label for="deviation_yes" class="me-2" style="font-size: 10px;">Yes</label>

                                            <input type="checkbox" id="deviation_no" name="deviation" value="No" class="me-1">
                                            <label for="deviation_no" style="font-size: 10px;">No</label>
                                        </div>
                                    </div>

                                    <div class="row p-0">
                                        <div class="d-flex align-items-center">
                                            <label style="font-size: 10px; margin-right: 25px;">Repeating:</label>

                                            <input type="checkbox" id="repeating_yes" name="repeating" value="Yes" class="me-1">
                                            <label for="repeating_yes" class="me-2" style="font-size: 10px;">Yes</label>

                                            <input type="checkbox" id="repeating_no" name="repeating" value="No" class="me-1">
                                            <label for="repeating_no" style="font-size: 10px;">No</label>
                                        </div>
                                    </div>
                                    <div class="row p-0">
                                        <div class="d-flex" style="align-items: baseline; width: fit-content;">
                                            <label style="font-size: 10px; white-space: nowrap; margin-right: 30px;">Cavity:</label>
                                            <input type="text" name="cavity" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 350px;">
                                        </div>
                                    </div>
                                    <div class="row p-0">
                                        <div class="d-flex" style="align-items: baseline; width: fit-content;">
                                            <label style="font-size: 10px; white-space: nowrap; margin-right: 20px;">Machine:</label>
                                            <input type="text" name="machine" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 350px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 border p-0">
                                    <div class="form-floating">
                                        <textarea id="ref" name="ref" class="form-control form-control-lg" placeholder="Critical Doc Reference" style="height: 120px; overflow-y: hidden; width: 100%;" required oninput="autoExpand(this)"></textarea>
                                        <label for="ref" style="font-size: 12px; display: block; word-wrap: break-word; white-space: normal;">Critical Doc Reference</label>
                                    </div>
                                </div>
                                <div class="col-md-3 border p-0">
                                    <div class="form-floating">
                                        <textarea id="bg" name="bg" class="form-control form-control-lg" placeholder="Critical Doc Reference" style="height: 120px; overflow-y: hidden; width: 100%;" required oninput="autoExpand(this)"></textarea>
                                        <label for="bg" style="font-size: 12px; display: block; word-wrap: break-word; white-space: normal;">Issue background or information relevant in determining the root cause of the problem</label>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Ensure only one checkbox is selected at a time
                                document.getElementById("deviation_yes").addEventListener("change", function() {
                                    if (this.checked) {
                                        document.getElementById("deviation_no").checked = false;
                                    }
                                });

                                document.getElementById("deviation_no").addEventListener("change", function() {
                                    if (this.checked) {
                                        document.getElementById("deviation_yes").checked = false;
                                    }
                                });
                            </script>

                            <script>
                                // Ensure only one checkbox is selected at a time
                                document.getElementById("repeating_yes").addEventListener("change", function() {
                                    if (this.checked) {
                                        document.getElementById("repeating_no").checked = false;
                                    }
                                });

                                document.getElementById("repeating_no").addEventListener("change", function() {
                                    if (this.checked) {
                                        document.getElementById("repeating_yes").checked = false;
                                    }
                                });
                            </script>
                            <div class="row mt-2 me-0 ms-0 mb-0">
                                <div class="col-md-6 border p-1">
                                    <span style="font-size: 12px">
                                        Immidiate containment action/s or countermeaseure/s taken (tick as many as appropriate)
                                    </span>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="one" name="one" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="one" style="font-size: 12px;">1: Segregate affected part/s - write custodian of the segregated parts</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="one_one" name="one_one" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-4">
                                        <label for="one_one" style="font-size: 12px;">1.1: At Hotpress: Put on hold inventory of affected lay-up materials together with the parts</label><br>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="two" name="two" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="two" style="font-size: 12px;">2: Yield off/ 100% inspection. INSPECTION RESULTS:</label>
                                        <input type="text" id="two_one" name="two_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 283px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="three" name="three" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="three" style="font-size: 12px;">3: Call the attention of QAE/PE/EE/TECH/CHIEF:</label>
                                        <input type="text" id="three_one" name="three_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 313px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="four" name="four" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="four" style="font-size: 12px;">4: Attach On-hold Tag and put in On-Hold cage/area</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="five" name="five" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="five" style="font-size: 12px;">5: Check MCS stock for similar Lot Number/AWPI/DC and request to file NCPR</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="six" name="six" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="six" style="font-size: 12px;">6: Attach copy of OCAP if available, and/or other log forms as part of the containment action</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label for="seven" style="font-size: 12px; margin-right: 10px" class="ms-5">7: File Shutdown Record</label>

                                        <input type="checkbox" id="seven-yes" name="seven" value="yes" style="transform: scale(1); margin-right: 5px;" onclick="toggleCheckbox(this)">
                                        <label for="seven-yes" style="font-size: 12px; margin-right: 10px">Yes</label>

                                        <input type="checkbox" id="seven-no" name="seven" value="no" style="transform: scale(1); margin-right: 5px;" onclick="toggleCheckbox(this)">
                                        <label for="seven-no" style="font-size: 12px; margin-right: 10px">No</label>

                                        <script>
                                            function toggleCheckbox(selected) {
                                                document.querySelectorAll('input[name="seven"]').forEach(checkbox => {
                                                    if (checkbox !== selected) {
                                                        checkbox.checked = false;
                                                    }
                                                });
                                            }
                                        </script>


                                        <label for="seven_one" style="font-size: 12px;">WHO:</label>
                                        <input type="text" id="seven_one" name="seven_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 98px;">

                                        <label for="seven_two" style="font-size: 12px;" class="ms-3">TIME/SHIFT:</label>
                                        <input type="text" id="seven_two" name="seven_two" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 98px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="eight" name="eight" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="eight" style="font-size: 12px;">8: Others (please specify):</label>
                                        <input type="text" id="eight_one" name="eight_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 435px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="nine" name="nine" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="nine" style="font-size: 12px;">9: Find affected WIP, FG & raw materials - specify DJ/s and LN/s</label>
                                        <input type="text" id="nine_one" name="nine_one" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; padding: 0; height: auto; font-size: 10px; width: 230px;"><br>
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
                                        <input type="checkbox" id="fgparts" name="fgparts" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="fgparts" style="font-size: 15px;">FG Parts:</label>
                                        <label style="font-size: 15px; margin-left:200px">Cancel Shipment:</label>
                                        <input type="checkbox" id="shipment_yes" name="shipment" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-5">
                                        <label for="shipment_yes" style="font-size: 15px;">Yes</label>
                                        <input type="checkbox" id="shipment_no" name="shipment" value="no" style="transform: scale(1); margin-right: 5px;" class="ms-2">
                                        <label for="shipment_no" style="font-size: 15px;">No</label><br>
                                    </div>
                                    <div class="d-flex flex-column mb-1">
                                        <label for="ship_sched" style="font-size: 15px;">SHIPMENT SCHEDULE's / Quantity:</label>
                                        <input type="text" id="ship_sched" name="ship_sched"
                                            style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; 
                                            padding: 5px; height: auto; font-size: 12px; width: 100%; max-width: 600px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="wip" name="wip" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="wip" style="font-size: 15px;">WIP:</label>
                                        <label style="font-size: 15px; margin-left:230px">Stop Process:</label>
                                        <input type="checkbox" id="stop_proc_yes" name="stop_proc" value="yes" style="transform: scale(1); margin-right: 5px;" class="ms-5">
                                        <label for="stop_proc_yes" style="font-size: 15px;">Yes</label>
                                        <input type="checkbox" id="stop_proc_no" name="stop_proc" value="no" style="transform: scale(1); margin-right: 5px;" class="ms-2">
                                        <label for="stop_proc_no" style="font-size: 15px;">No</label><br>
                                    </div>

                                    <script>
                                        function enforceSingleCheckbox(groupName) {
                                            document.querySelectorAll(`input[name="${groupName}"]`).forEach(checkbox => {
                                                checkbox.addEventListener("change", function() {
                                                    if (this.checked) {
                                                        document.querySelectorAll(`input[name="${groupName}"]`).forEach(cb => {
                                                            if (cb !== this) cb.checked = false;
                                                        });
                                                    }
                                                });
                                            });
                                        }

                                        enforceSingleCheckbox("recall");
                                        enforceSingleCheckbox("shipment");
                                        enforceSingleCheckbox("stop_proc");
                                    </script>
                                    <div class="d-flex align-items-center">
                                        <label for="location" style="font-size: 15px;">Locations:</label>
                                        <input type="text" id="location" name="location" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; 
                                            padding: 5px; height: auto; font-size: 12px; width: 100%; max-width: 550px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="mcs" name="mcs" value="yes" style="transform: scale(1); margin-right: 5px;">
                                        <label for="mcs" style="font-size: 15px;">MCS:</label>


                                        <label for="mcs_details" style="font-size: 15px;">MCS Details:</label>
                                        <input type="text" id="mcs_details" name="mcs_details" style="border: none; border-bottom: 1px solid #ced4da; border-radius: 0; outline: none; 
                                            padding: 5px; height: auto; font-size: 12px; width: 100%; max-width: 438px;">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="customer_notif" name="customer_notif" value="yes" style="transform: scale(1); margin-right: 5px;">
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

                                        <script>
                                            function previewImage(event) {
                                                var image = document.getElementById('imagePreview');
                                                var container = document.getElementById('imagePreviewContainer');
                                                var file = event.target.files[0];

                                                if (file) {
                                                    var reader = new FileReader();
                                                    reader.onload = function(e) {
                                                        image.src = e.target.result;
                                                        image.style.display = "block"; // Show the image
                                                        container.style.display = "flex"; // Show the preview box
                                                    }
                                                    reader.readAsDataURL(file);
                                                }
                                            }
                                        </script>
                                        <div id="file-container" class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-center gap-2 mb-2 mt-2 p-2 border">
                                                <label for="excel">Upload Excel File:</label>
                                                <input type="file" name="excel_name[]" id="excel" accept=".xls,.xlsx" multiple>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        function addExcelInput() {
                                            let container = document.getElementById("file-container");

                                            let newDiv = document.createElement("div");
                                            newDiv.classList.add("d-flex", "align-items-center", "gap-2", "mb-2", "mt-2", "p-2", "border");

                                            let newLabel = document.createElement("label");
                                            newLabel.textContent = "Upload Excel File:";

                                            let newInput = document.createElement("input");
                                            newInput.type = "file";
                                            newInput.name = "excel_name[]"; // Use array notation
                                            newInput.accept = ".xls,.xlsx";

                                            let removeButton = document.createElement("button");
                                            removeButton.type = "button";
                                            removeButton.classList.add("btn", "btn-danger");
                                            removeButton.textContent = "Remove";
                                            removeButton.onclick = function() {
                                                removeInput(removeButton);
                                            };

                                            newDiv.appendChild(newLabel);
                                            newDiv.appendChild(newInput);
                                            newDiv.appendChild(removeButton);
                                            container.appendChild(newDiv);
                                        }


                                        function removeInput(button) {
                                            button.parentElement.remove();
                                        }
                                    </script>

                                    <button type="button" class="btn btn-sm btn-primary" onclick="addExcelInput()">Add Another Excel File</button>
                                </div>
                                <button type="submit" class="btn btn-success  w-50 mx-auto d-block" id="submitButton">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const requiredFields = document.querySelectorAll("input[required], textarea[required]");
            const submitButton = document.getElementById("submitButton"); // Ensure your submit button has an ID

            function checkFields() {
                let allFilled = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add("is-invalid"); // Bootstrap invalid class (red border)
                        allFilled = false;
                    } else {
                        field.classList.remove("is-invalid");
                    }
                });

                if (!allFilled) {
                    submitButton.classList.add("btn-danger");
                    submitButton.innerHTML = "Submit <span style='color: yellow;'>&#9888;</span>";
                } else {
                    submitButton.classList.remove("btn-danger");
                    submitButton.innerHTML = "Submit";
                }
            }

            requiredFields.forEach(field => {
                field.addEventListener("input", checkFields);
            });

            submitButton.addEventListener("click", function(event) {
                checkFields();
                if (submitButton.classList.contains("btn-danger")) {
                    event.preventDefault(); // Prevent submission if there are empty fields
                }
            });
        });

        document.getElementById("addRowBtn").addEventListener("click", function() {
            var table = document.getElementById("materialTable").getElementsByTagName("tbody")[0];

            // Count current rows
            var rowCount = table.getElementsByTagName("tr").length;

            // Check if the limit is reached
            if (rowCount >= 12) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached',
                    text: 'You can only add up to 12 rows.',
                    confirmButtonColor: '#d33'
                });
                return; // Stop execution if limit is reached
            }

            var newRow = document.createElement("tr");

            // Get values from the first row if available
            var firstRow = table.querySelector("tr");
            var ntdjValue = firstRow ? firstRow.querySelector('[name="ntdj_num[]"]').value : "";
            var mnsValue = firstRow ? firstRow.querySelector('[name="mns_num[]"]').value : "";
            var lotSublotValue = firstRow ? firstRow.querySelector('[name="lot_sublot_qty[]"]').value : "";

            newRow.innerHTML = `
        <td><input type="text" class="form-control" name="ntdj_num[]" value="${ntdjValue}"></td>
        <td><input type="text" class="form-control" name="mns_num[]" value="${mnsValue}"></td>
        <td><input type="number" class="form-control" name="lot_sublot_qty[]" value="${lotSublotValue}" required></td>
        <td class="d-flex">
            <input type="number" class="form-control" name="qty_affected[]" required> 
            <input type="text" class="form-control" name="qty_affected_text[]" placeholder="Enter text">
        </td>
        <td>
            <div class="input-group">
                <input type="number" step="0.01" class="form-control" name="defect_rate[]" readonly required>
                <span class="input-group-text">%</span>
            </div>
        </td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
        `;

            table.appendChild(newRow);

            // Call function to enable defect rate calculation for this row
            calculateDefectRate(newRow);

            // Attach event listener to remove button
            newRow.querySelector(".remove-row").addEventListener("click", function() {
                newRow.remove();
            });
        });


        // Remove row functionality
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-row")) {
                event.target.closest("tr").remove();
            }
        });

        // Function to calculate defect rate for a row
        function calculateDefectRate(row) {
            let lotQty = row.querySelector('[name="lot_sublot_qty[]"]');
            let qtyAffected = row.querySelector('[name="qty_affected[]"]');
            let defectRate = row.querySelector('[name="defect_rate[]"]');

            function updateDefectRate() {
                let lotValue = parseFloat(lotQty.value) || 0;
                let affectedValue = parseFloat(qtyAffected.value) || 0;

                if (lotValue > 0) {
                    let calculatedRate = (affectedValue / lotValue) * 100;
                    defectRate.value = calculatedRate.toFixed(2);
                } else {
                    defectRate.value = "";
                }
            }

            function validateDefectRate() {
                let lotValue = parseFloat(lotQty.value) || 0;
                let affectedValue = parseFloat(qtyAffected.value) || 0;
                let calculatedRate = (affectedValue / lotValue) * 100;

                if (calculatedRate > 100) {
                    Swal.fire({
                        icon: "warning",
                        title: "Invalid Input",
                        text: "Defect rate cannot exceed 100%!",
                        confirmButtonColor: "#d33",
                    });
                    qtyAffected.value = ""; // Clear invalid input
                    defectRate.value = "";
                }
            }

            lotQty.addEventListener("input", updateDefectRate);
            qtyAffected.addEventListener("input", updateDefectRate);
            qtyAffected.addEventListener("blur", validateDefectRate); // Validate when user leaves the input field
        }


        // Apply defect rate calculation for existing rows on page load
        document.querySelectorAll("#materialTable tbody tr").forEach(row => {
            calculateDefectRate(row);
        });


        document.addEventListener("DOMContentLoaded", function() {
            fetch('insert.php?get_ncpr=1') // Now, this requests the next ncpr_num
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        document.getElementById("ncpr_num").value = data.ncpr_num;
                    } else {
                        console.error("Error fetching ncpr_num:", data.message);
                    }
                })
                .catch(error => console.error("Fetch error:", error));
        });

        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
        });

        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        document.getElementById("ncprForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission

            let formData = new FormData(this); // Get form data
            let originalNcprNum = document.getElementById("ncpr_num").value; // Store original ncpr_num

            fetch("insert.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        if (data.new_ncpr_num && data.new_ncpr_num !== originalNcprNum) {
                            // Notify user that the NCPR number has changed
                            Swal.fire({
                                title: "Notice!",
                                text: "Your NCPR number has been updated to " + data.new_ncpr_num + " because the previous number was taken.",
                                icon: "info",
                                confirmButtonText: "OK"
                            }).then(() => {
                                document.getElementById("ncpr_num").value = data.new_ncpr_num; // Update field
                                window.location.href = "ncprfiling.php"; // Redirect after acknowledging
                            });
                        } else {
                            Swal.fire({
                                title: "Success!",
                                text: "Data successfully inserted!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "ncprfiling.php"; // Redirect after clicking OK
                            });
                        }
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
        });


        function autoExpand(textarea) {
            textarea.style.height = "auto"; // Reset height
            textarea.style.height = textarea.scrollHeight + "px"; // Set new height
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>