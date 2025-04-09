<?php
require "config.php";
include 'conn.php'; // Ensure your database connection is included

// Fetch data from the database
$sql = "SELECT part_number, part_name FROM product_list LIMIT 100";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>admin Dashboard</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/all.min.css">
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
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
                <li class="sidebar-item active">
                    <a href="productkey.php" class="sidebar-link">
                        <i class="fa-solid fa-helmet-safety"></i>
                        <span>Product Key</span>
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
            <div class="page-wrapper p-2">
                <div class="card p-3">
                    <div class="card-title">
                        <h4>Product Key List</h4>
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary w-25" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            Add Product
                        </button>
                    </div>

                    <table id="productKey" class="table table-bordered table-striped text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-center">Part Number</th>
                                <th class="text-center">Part Name</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="add_product.php" method="POST">
                                <div class="mb-3">
                                    <label for="part_number" class="form-label">Part Number</label>
                                    <input type="text" class="form-control" id="part_number" name="part_number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="part_name" class="form-label">Part Name</label>
                                    <input type="text" class="form-control" id="part_name" name="part_name" required>
                                </div>

                                <button type="submit" class="btn btn-success">Add Product</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="update_product.php" method="POST">
                                <div class="mb-3">
                                    <label for="edit_part_number" class="form-label">Part Number</label>
                                    <input type="text" class="form-control" id="edit_part_number" name="part_number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_part_name" class="form-label">Part Name</label>
                                    <input type="text" class="form-control" id="edit_part_name" name="part_name" required>
                                </div>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let partNumber = this.getAttribute("data-id");
                    let partName = this.getAttribute("data-name");

                    // Populate modal fields
                    document.getElementById("edit_part_number").value = partNumber;
                    document.getElementById("edit_part_name").value = partName;
                });
            });

            $(document).ready(function() {
                var table = $('#productKey').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "productKey_fetch.php",
                        type: "GET",
                        dataSrc: function(json) {
                            return json.data;
                        }
                    },
                    columns: [{
                            data: "part_number"
                        },
                        {
                            data: "part_name"
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                        <button class="btn btn-warning btn-sm edit-btn text-light fw-bold"
                            data-bs-toggle="modal"
                            data-bs-target="#editProductModal"
                            data-id="${row.part_number}"
                            data-name="${row.part_name}">
                            Edit
                        </button>
                    `;
                            }
                        }
                    ],
                    order: [], // ✅ This line disables default ordering
                    pagingType: "full_numbers" // Optional: shows First/Prev/Next/Last buttons
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