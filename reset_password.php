<?php
session_start();
include 'db.php'; // Database: lutong_bahay

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_otp'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['reset_password'])) {
    $entered_otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $new_password = $_POST['new_password'];
    
    if ($entered_otp == $_SESSION['reset_otp']) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $update_query = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";
        
        if (mysqli_query($conn, $update_query)) {
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_otp']);
            $_SESSION['success'] = "Password reset successful! You can now login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Error updating password: " . mysqli_error($conn);
        }
    } else {
        $error = "Invalid reset code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Lutong Bahay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #8B4513; --bg-light: #fdf5e6; }
        body { background: var(--bg-light); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .reset-box { background: white; padding: 40px; border-radius: 25px; box-shadow: 0 15px 35px rgba(139, 69, 19, 0.15); width: 100%; max-width: 420px; text-align: center; border-top: 5px solid var(--primary-color); }
        .brand-name { font-family: 'Great Vibes', cursive; font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px; }
        .btn-custom { background: var(--primary-color); color: white; border-radius: 12px; width: 100%; padding: 14px; font-weight: 700; border: none; transition: 0.3s; margin-top: 10px; }
        .btn-custom:hover { background: #5d2e0d; transform: translateY(-2px); }
        
        .form-label-custom { font-size: 0.8rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase; }
        .form-control { border-radius: 12px; padding: 12px 15px; background: #f3f4f6; border: 2px solid transparent; transition: 0.3s; }
        .form-control:focus { background: white; border-color: var(--primary-color); box-shadow: none; }
        
        .input-group-text { background: #f3f4f6; border: none; color: var(--primary-color); border-radius: 12px 0 0 12px; }
        .eye-toggle { cursor: pointer; border-radius: 0 12px 12px 0 !important; background: #f3f4f6; color: #888; border: none; }
    </style>
</head>
<body>
    <div class="reset-box">
        <div class="brand-name">Lutong Bahay</div>
        <h4 class="fw-bold mb-2">Create New Password</h4>
        <p class="text-muted small mb-4">Please enter your reset code and new password below.</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <!-- Reset Code with Icon -->
            <div class="mb-3 text-start">
                <label class="form-label-custom">Reset Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    <input type="text" name="otp" class="form-control" placeholder="000000" maxlength="6" required>
                </div>
            </div>

            <!-- New Password with Icon and Toggle -->
            <div class="mb-4 text-start">
                <label class="form-label-custom">New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="new_password" id="newPassField" class="form-control" placeholder="••••••••" required minlength="6">
                    <span class="input-group-text eye-toggle" onclick="togglePass('newPassField', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" name="reset_password" class="btn btn-custom shadow-sm">
                UPDATE PASSWORD <i class="fas fa-check-circle ms-1"></i>
            </button>
        </form>

        <div class="mt-4 small">
            <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
            </a>
        </div>
    </div>

    <script>
        function togglePass(id, el) {
            const input = document.getElementById(id);s
            const icon = el.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text'; 
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password'; 
                icon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>