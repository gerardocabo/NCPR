<?php
require "connection.php"; // Include database connection
require "config.php";

try {
    // Fetch roles
    $stmt = $pdo->query("SELECT id, role_name FROM users_roles WHERE NOT (id = 1); ORDER BY id ASC");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
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
                    <a href="SuperAdmin_dashboard.php" class="sidebar-link">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="sidebar-item active">
                    <a href="Setting_SAdmin.php" class="sidebar-link">
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

            <div class="card">
                <div class="card-body">
                    <header class="py-3 shadow-sm" style="background-color: #0e2238">
                        <div class="container d-flex align-items-center">
                            <a class="navbar-brand text-white" href="#">
                                <span class="fs-4 fw-bold ms-2">NCPR</span>
                            </a>
                        </div>
                    </header>

                    <div class="container d-flex flex-grow-1 justify-content-center align-items-center">
                        <div class="login-form bg-light p-4 rounded shadow" style="width: 500px;">
                            <h2 class="text-center">ADD ACCESS ACCOUNTS</h2>
                            <form id="accessForm">
                                <div class="mb-3">
                                    <label class="form-label">EMAIL/USERNAME</label>
                                    <input type="text" class="form-control p-2 fs-6" name="username" placeholder="Enter email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">PASSWORD</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control p-2 fs-6 pe-5" id="password" name="password" placeholder="Enter password" required>
                                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y p-0 border-0 bg-transparent shadow-none me-2" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ROLE</label>
                                    <select class="form-control p-2 fs-6" id="role" name="role" required>
                                        <option value="" disabled selected>Select a role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= htmlspecialchars($role['id']) ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100 p-2"><span class="fs-5 fw-bold text-dark">ADD NEW</span></button>
                                <button type="button" class="btn btn-primary w-100 p-2 mt-2" data-bs-toggle="modal" data-bs-target="#viewAccountsModal">
                                    <span class="fs-5 fw-bold text-white">VIEW ALL ACCOUNTS</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Modal for Viewing Accounts -->
                    <div class="modal fade" id="viewAccountsModal" tabindex="-1" aria-labelledby="viewAccountsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewAccountsModalLabel">All Access Accounts</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="accountList">
                                            <tr>
                                                <td colspan="4" class="text-center">Loading...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Changing Password -->
                    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="passwordForm">
                                        <!-- Hidden field to store user ID -->
                                        <input type="hidden" id="changePasswordUserId" name="userId">
                                        <div class="mb-3">
                                            <label for="newPassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="newPassword" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirmPassword" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
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
                $(document).ready(function() {
                    $("#accessForm").on("submit", function(event) {
                        event.preventDefault(); // Prevent default form submission

                        var formData = $(this).serialize(); // Serialize form data
                        formData += "&action=register_user"; // Append action parameter

                        $.ajax({
                            url: "Setting_register.php", // PHP handler
                            type: "POST",
                            data: formData,
                            dataType: "json",
                            success: function(response) {
                                if (response.status === "success") {
                                    alert(response.message); // Show success message
                                    $("#accessForm")[0].reset(); // Reset the form

                                    // Redirect if needed
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                } else {
                                    alert(response.message); // Show error message
                                }
                            },
                            error: function() {
                                alert("An error occurred. Please try again.");
                            }
                        });
                    });
                });


                // Toggle Password Visibility
                $("#togglePassword").click(function() {
                    var passwordField = $("#password");
                    var type = passwordField.attr("type") === "password" ? "text" : "password";
                    passwordField.attr("type", type);
                    $(this).find("i").toggleClass("fa-eye fa-eye-slash");
                });

                $(document).ready(function() {
                    // Toggle password visibility
                    $("#togglePassword").click(function() {
                        let passwordField = $("#newPassword");
                        let icon = $(this).find("i");

                        if (passwordField.attr("type") === "password") {
                            passwordField.attr("type", "text");
                            icon.removeClass("fa-eye").addClass("fa-eye-slash");
                        } else {
                            passwordField.attr("type", "password");
                            icon.removeClass("fa-eye-slash").addClass("fa-eye");
                        }
                    });

                    // Load accounts when the 'View Accounts' modal is shown
                    $('#viewAccountsModal').on('show.bs.modal', function() {
                        $.ajax({
                            url: 'Setting_register.php',
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                let rows = "";
                                if (response.length > 0) {
                                    response.forEach(function(user) {
                                        rows += `<tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.role}</td>
                            <td>
                                <button class="btn btn-warning btn-sm change-password" 
                                    data-userid="${user.id}" 
                                    data-username="${user.username}">
                                    Change Password
                                </button>
                                <button class="btn btn-secondary btn-sm block-user" 
                                data-userid="${user.id}" 
                                data-username="${user.username}">
                                Block
                            </button>
                            <button class="btn btn-danger btn-sm delete-user" 
                                data-userid="${user.id}" 
                                data-username="${user.username}">
                                Delete
                            </button>
                            </td>
                        </tr>`;
                                    });
                                } else {
                                    rows = `<tr><td colspan="4" class="text-center">No accounts found.</td></tr>`;
                                }
                                $('#accountList').html(rows);
                            }
                        });
                    });

                    // Remove lingering modal backdrop when 'View Accounts' modal is closed
                    $('#viewAccountsModal').on('hidden.bs.modal', function() {
                        $("body").removeClass("modal-open"); // Remove modal-open class
                        $(".modal-backdrop").remove(); // Remove any existing modal backdrop
                    });

                    // Handle Change Password button click (Event Delegation)
                    $(document).on('click', '.change-password', function() {
                        let userId = $(this).data('userid');
                        let username = $(this).data('username');

                        // Set user details in the Change Password modal
                        $('#changePasswordUserId').val(userId);
                        $('#changePasswordUsername').text(username);

                        // Hide the View Accounts modal
                        $('#viewAccountsModal').modal('hide');

                        // Show the Change Password modal
                        var passwordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                        passwordModal.show();
                    });

                    // Handle Cancel button click inside Change Password modal
                    $('#changePasswordModal').on('hidden.bs.modal', function() {
                        // Reset the form
                        $('#passwordForm')[0].reset();

                        // Show the View Accounts modal again
                        var accountsModal = new bootstrap.Modal(document.getElementById('viewAccountsModal'));
                        accountsModal.show();
                    });

                    // Handle Block User button click
                    $(document).on('click', '.block-user', function() {
                        let userId = $(this).data('userid');
                        let username = $(this).data('username');

                        Swal.fire({
                            title: "Are you sure?",
                            text: `Do you want to block ${username}?`,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes, Block",
                            cancelButtonText: "Cancel"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "Setting_block.php",
                                    type: "POST",
                                    data: {
                                        action: "block_user",
                                        userId: userId
                                    },
                                    dataType: "json",
                                    success: function(response) {
                                        Swal.fire(response.status === "success" ? "Blocked!" : "Error", response.message, response.status);
                                        $('#viewAccountsModal').modal('hide');
                                    },
                                    error: function() {
                                        Swal.fire("Error", "Something went wrong! Please try again.", "error");
                                    }
                                });
                            }
                        });
                    });

                    // Handle Delete User button click
                    $(document).on('click', '.delete-user', function() {
                        let userId = $(this).data('userid');
                        let username = $(this).data('username');

                        Swal.fire({
                            title: "Are you sure?",
                            text: `Do you want to delete ${username}? This action cannot be undone.`,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes, Delete",
                            cancelButtonText: "Cancel"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "Setting_delete.php",
                                    type: "POST",
                                    data: {
                                        action: "delete_user",
                                        userId: userId
                                    },
                                    dataType: "json",
                                    success: function(response) {
                                        Swal.fire(response.status === "success" ? "Deleted!" : "Error", response.message, response.status);
                                        $('#viewAccountsModal').modal('hide');
                                    },
                                    error: function() {
                                        Swal.fire("Error", "Something went wrong! Please try again.", "error");
                                    }
                                });
                            }
                        });
                    });
                });

                // HANDLE PASSWORD CHANGE FORM SUBMISSION
                $(document).on("submit", "#passwordForm", function(e) {
                    e.preventDefault(); // Prevent default form submission

                    let userId = $("#changePasswordUserId").val().trim();
                    let newPassword = $("#newPassword").val().trim();
                    let confirmPassword = $("#confirmPassword").val().trim();

                    if (!userId) {
                        Swal.fire("Error", "Invalid user ID. Please try again.", "error");
                        return;
                    }

                    if (newPassword !== confirmPassword) {
                        Swal.fire("Error", "Passwords do not match!", "error");
                        return;
                    }

                    $.ajax({
                        url: "Setting_change.php",
                        type: "POST",
                        data: {
                            action: "change_password",
                            userId: userId,
                            newPassword: newPassword
                        },
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        success: function(response) {
                            Swal.fire({
                                title: response.status === "success" ? "Success" : "Error",
                                text: response.message,
                                icon: response.status
                            }).then(() => {
                                if (response.status === "success") {
                                    $("#passwordForm")[0].reset(); // Reset form
                                    $("#changePasswordModal").modal("hide"); // Hide modal
                                    $("#viewAccountsModal").modal("show"); // Show accounts modal again
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire("Error", "Something went wrong! Please try again.", "error");
                            console.error("AJAX Error:", status, error);
                        }
                    });
                });
            });
        </script>
</body>

</html>