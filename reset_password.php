<?php
session_start();
if (!isset($_SESSION['otp'])) {
    header("Location: enter_otp.php");
    exit();
}
$otp = $_SESSION['otp'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert CDN -->
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Reset Password</h5>
                <form id="resetForm" action="updatePassword.php" method="POST">
                    <input type="hidden" name="otp" value="<?php echo htmlspecialchars($otp); ?>">

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert: Password match validation -->
    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;

            if (newPass !== confirmPass) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'New password and confirm password do not match.'
                });
            }
        });
    </script>

    <?php if (isset($_SESSION['alert'])): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION["alert"]["type"]; ?>',
                title: '<?php echo $_SESSION["alert"]["title"]; ?>',
                text: '<?php echo $_SESSION["alert"]["message"]; ?>'
            }).then(() => {
                <?php if (!empty($_SESSION["alert"]["redirect"])): ?>
                    window.location.href = "<?php echo $_SESSION["alert"]["redirect"]; ?>";
                <?php endif; ?>
            });
        </script>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
</body>

</html>