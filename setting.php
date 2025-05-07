<?php
include 'conn.php';
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginform.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Join users, key_person, and users_role to get full user info including role
$stmt = $conn->prepare("
    SELECT 
        users.id, users.username, users.email, 
        key_person.fname, key_person.lname,
        users_roles.role_name
    FROM users 
    INNER JOIN key_person ON users.person_id = key_person.id 
    INNER JOIN users_roles ON users.role_id = users_roles.id 
    WHERE users.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='loginform.php';</script>";
    exit();
}

// Save user data into session
$_SESSION['user_role'] = $user['role_name'];
$_SESSION['username'] = $user['username'];
$_SESSION['fullname'] = $user['fname'] . ' ' . $user['lname'];

// Determine dashboard route
$dashboardPage = '#'; // default

$role = $_SESSION['user_role'];

if ($role === 'PCO' || $role === 'QA ENGINEER') {
    $dashboardPage = 'engineer_dashboard.php';
} elseif ($role === 'QA SUPERVISOR' || $role === 'QA MANAGER') {
    $dashboardPage = 'supv_mgr_dashboard.php';
} elseif ($role === 'SHELDAHL REPRESENTATIVE') {
    $dashboardPage = 'representative_Dashboard.php';
}

?>



<!DOCTYPE html>
<html>

<head>
    <title>NCPR System - User Settings</title>
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
                    <a href="#">MENU</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="<?php echo $dashboardPage; ?>" class="sidebar-link">
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
                <li class="sidebar-item active">
                    <a href="setting.php" class="sidebar-link">
                        <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
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
        <div class="main p-5">
            <div class="page-wrapper p-5">
                <div class="card shadow" style="max-width: 900px; margin: auto;">
                    <div class="card-header text-center">
                        <h4 class="my-2">Profile Settings</h4>
                    </div>
                    <div class="card-body">
                        <form action="update_settings.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($user['fname']) ?>" class="form-control" placeholder="First Name" required>
                                <label for="fname">First Name</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" id="lname" name="lname" value="<?= htmlspecialchars($user['lname']) ?>" class="form-control" placeholder="Last Name" required>
                                <label for="lname">Last Name</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" placeholder="Username" required>
                                <label for="username">Username</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" placeholder="Email" required>
                                <label for="email">Email</label>
                            </div>

                            <!-- New Password -->
                            <div class="form-floating mb-3 position-relative">
                                <input type="password" id="password" name="password" class="form-control" placeholder="New Password">
                                <label for="password">New Password <small class="text-muted">(leave blank if unchanged)</small></label>
                                <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 toggle-password" data-target="password" style="cursor: pointer;"></i>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-floating mb-3 position-relative">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                                <label for="confirm_password">Confirm Password</label>
                                <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 toggle-password" data-target="confirm_password" style="cursor: pointer;"></i>
                            </div>



                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(function(icon) {
            icon.addEventListener('click', function() {
                const targetInput = document.getElementById(this.getAttribute('data-target'));

                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    targetInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });


        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const form = e.target;
            const formData = new FormData(form);

            const password = form.querySelector('#password').value;
            const confirmPassword = form.querySelector('#confirm_password').value;

            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Mismatch',
                    text: 'The confirm password does not match the new password.'
                });
                return; // Stop submission
            }

            fetch('update_settings.php', {
                    method: 'POST',
                    body: formData
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        const data = JSON.parse(text);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    } catch (err) {
                        console.error("Invalid JSON:", text);
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Invalid response format.'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Something went wrong. Please try again later.'
                    });
                });
        });
    });
</script>

</html>