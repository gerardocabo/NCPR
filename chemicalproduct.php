<?php
require "config.php";
include 'conn.php'; // Ensure your database connection is included

$sql = "SELECT chem_id, part_number, item_description FROM chem_material_table";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>NCPR System - Product List</title>
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
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
                    <a href="#">MENU</a>
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
                <li class="sidebar-item ">
                    <a href="productkey.php" class="sidebar-link">
                        <i class="fa-solid fa-toolbox"></i>
                        <span>Product Key</span>
                    </a>
                </li>
                <li class="sidebar-item active">
                    <a href="chemicalproduct.php" class="sidebar-link">
                        <i class="fa-solid fa-flask"></i>
                        <span>Chemical Product</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="setting_admin.php" class="sidebar-link">
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
            <div class="page-wrapper p-2">
                <div class="card p-3">
                    <div class="card-title">
                        <h4>Chemical Product List</h4>
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary w-25" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            Add Product
                        </button>
                    </div>
                    <table id="chemProductTable" class="table  table-bordered text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>Part Number</th>
                                <th>Part Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0):
                                $seq = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $seq++ ?></td>
                                        <td><?= htmlspecialchars($row['part_number']) ?></td>
                                        <td><?= htmlspecialchars($row['item_description']) ?></td>
                                        <td>
                                            <a href="#"
                                                class="btn btn-sm btn-warning edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editProductModal"
                                                data-id="<?= $row['chem_id'] ?>"
                                                data-number="<?= htmlspecialchars($row['part_number']) ?>"
                                                data-name="<?= htmlspecialchars($row['item_description']) ?>">
                                                Edit
                                            </a>
                                            <form class="d-inline delete-form" data-id="<?= $row['chem_id'] ?>">
                                                <button type="button" class="btn btn-sm btn-danger delete-btn">Delete</button>
                                            </form>

                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="addProductForm">
                        <div class="modal-content">
                            <div class="modal-header text-dark">
                                <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="part_number" class="form-label">Part Number</label>
                                    <input type="text" class="form-control" id="part_number" name="part_number" placeholder="Enter N/A if the product does not have a part number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="part_name" class="form-label">Part Name</label>
                                    <input type="text" class="form-control" id="part_name" name="item_description" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Add product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editProductForm">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title" id="editProductModalLabel">Edit Chemical Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="edit_chem_id" name="chem_id">
                                <div class="mb-3">
                                    <label for="edit_part_number" class="form-label">Part Number</label>
                                    <input type="text" class="form-control" id="edit_part_number" name="part_number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_part_name" class="form-label">Part Name</label>
                                    <input type="text" class="form-control" id="edit_part_name" name="item_description" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/DataTables/datatables.min.js"></script>
<script src="assets/js/sweetalert2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#chemProductTable').DataTable({
            pagingType: 'full_numbers',
            responsive: true,
            language: {
                emptyTable: "No chemical product data available."
            }
        });
    });

    // Part Number validation for Add Form
    document.getElementById("part_number").addEventListener("blur", function() {
        const partNumber = this.value.trim();

        if (partNumber.toUpperCase() === "N/A" || partNumber.toUpperCase() === "NA") {
            if (partNumber !== "N/A") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Input',
                    text: 'Please enter exactly "N/A" (capitalized and with a slash) for products without a part number.',
                }).then(() => {
                    this.value = '';
                    this.focus();
                });
            }
        }
    });

    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'productKey_add.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#addProductModal').modal('hide');
                    $('#addProductForm')[0].reset();
                    $('#productKey').DataTable().ajax.reload();
                    alert('Product added successfully.');
                } else if (response.status === 'exists') {
                    alert('Product already exists (case-insensitive).');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('AJAX error occurred.');
            }
        });
    });

    $(document).ready(function() {
        // Delegate edit button click handler
        $(document).on('click', '.edit-btn', function() {
            $('#edit_chem_id').val($(this).data('id'));
            $('#edit_part_number').val($(this).data('number'));
            $('#edit_part_name').val($(this).data('name'));
        });

        // Handle update submission
        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'update_chemical_product.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Updated!', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });
    });


    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            const chemId = form.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This chemical product will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_chem.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                id: chemId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Deleted!', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Request failed.', 'error');
                            console.error(error);
                        });
                }
            });
        });
    });
</script>

</html>