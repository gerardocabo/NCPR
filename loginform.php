<?php

require "config.php";
require_once 'csrf.php';
$token = generateCSRFToken();
// Block direct access if not coming from the gateway
/* if (!isset($_SESSION['GATEWAY_VERIFIED']) || $_SESSION['GATEWAY_VERIFIED'] !== true) {
    http_response_code(403);
    exit;
} */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="secure-token" content="ABC123SECRET">

    <title>NCPR - Login</title>

    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">

</head>
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background: #f1f3f5;
    }
</style>

<body class="d-flex flex-column min-vh-100">
    <header class="py-3 shadow-md">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <img src="assets/img/Picture1.png" alt="Left Logo" style="height: 50px;">
                <a class="navbar-brand mx-auto text-center" href="#">
                    <span class="fs-4 fw-bold" style="color: black">
                        NON-CONFORMING PRODUCT RECORD
                    </span>
                </a>
                <img src="assets/img/Picture2.png" alt="Right Logo" style="height: 50px;">
            </div>
        </div>
    </header>

    <div class="container d-flex flex-grow-1 justify-content-center align-items-center">
        <div class="login-form bg-light p-4 rounded shadow" style="width: 500px;">
            <h2 class="text-center">Login</h2>
            <form>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="mb-3">
                    <label class="form-label">EMAIL</label>
                    <input type="text" class="form-control p-2 fs-6" name="username" placeholder="Enter Email or ID number">
                </div>
                <div class="mb-3">
                    <label class="form-label">PASSWORD</label>
                    <div class="position-relative">
                        <input type="password" class="form-control p-2 fs-6 pe-5" id="password" name="password" placeholder="Enter Password">
                        <button class="btn position-absolute end-0 top-50 translate-middle-y p-0 border-0 bg-transparent shadow-none me-2" type="button" id="togglePassword">
                            <i class="fas fa-eye" style="font-size: 1rem;"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 p-2" name="login"><span class="fs-5 fw-bold text-dark">LOGIN</span></button>
            </form>
            <hr>
            <form>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="guest_role" value="GUEST"> <!-- Hidden field for guest role -->
                <button type="submit" class="btn btn-primary w-100 p-2" name="guest" id="guestLogin">
                    <span class="fs-5 fw-bold text-dark">GUEST</span>
                </button>
            </form>

            <p class="text-end mt-2">
                <a href="requestOTP.php"
                    style="text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s ease-in-out;"
                    onmouseover="this.style.color='#007bff'; this.style.textDecoration='underline';"
                    onmouseout="this.style.color='#333'; this.style.textDecoration='none';">
                    <span style="font-size: 15px">Forgot Password?</span>
                </a>
            </p>
        </div>
    </div>


    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password:</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/bootstrap/js/all.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>

    <script>
        //dors not actually work
        /*$(document).ready(function() {
            $("#loginForm").submit(function(e) {
                e.preventDefault(); // Prevent form submission

                var username = $("#username").val();
                var password = $("#password").val();
                var guestRole = $("#guest_role").val(); // Assuming there's a dropdown for guest roles
                var secureToken = $('meta[name="secure-token"]').attr('content'); // or $('body').data('token')
                var requestData = {};

                if (guestRole) {
                    // Guest login scenario
                    requestData = {
                        guest_role: guestRole
                    };
                } else {
                    // Admin login scenario
                    requestData = {
                        username: username,
                        password: password
                    };
                }

                $.ajax({
                    type: "POST",
                    url: "login.php",
                    data: requestData,
                    headers: {
                        "X-SECURE-TOKEN": secureToken
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === "success") {
                            window.location.href = data.redirect;
                        } else {
                            alert(data.message); // Show error message
                        }
                    },
                    error: function() {
                        console.error("Error:", error); // Log any AJAX error
                    }
                });
            });
        });*/

        $(document).ready(function() {
            $("#resetPasswordForm").on("submit", function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "reset_password.php",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Password Reset Successful",
                                text: response.message,
                                confirmButtonColor: "#577BC1"
                            }).then(() => {
                                $("#resetPasswordModal").modal("hide");
                            });
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Error",
                                text: response.message,
                                confirmButtonColor: "#d33"
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Something went wrong. Please try again.",
                            confirmButtonColor: "#d33"
                        });
                    }
                });
            });

            $("form:not(#resetPasswordForm)").on("submit", function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "login.php",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "WELCOME!",
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: "#577BC1"
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Login Failed",
                                text: response.message,
                                confirmButtonColor: "#d33"
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Something went wrong. Please try again.",
                            confirmButtonColor: "#d33"
                        });
                    }
                });
            });

            document.getElementById('togglePassword').addEventListener('click', function() {
                var passwordField = document.getElementById('password');
                var icon = this.querySelector('i');

                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        sessionStorage.clear(); // Clears previous user data
    </script>
</body>

</html>