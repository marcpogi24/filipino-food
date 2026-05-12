<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Lutong Bahay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Poppins', sans-serif; }
        .fp-box { background: white; padding: 40px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-custom { background: #8B4513; color: white; border-radius: 12px; padding: 12px; width: 100%; border: none; }
    </style>
</head>
<body>
    <div class="fp-box">
        <h3 class="text-center fw-bold" style="color: #8B4513;">Reset Password</h3>
        <p class="text-muted text-center small">Enter your email to receive a 6-digit OTP.</p>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger small"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success small"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="send_otp.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">EMAIL ADDRESS</label>
                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required style="border-radius: 10px;">
            </div>
            <button type="submit" name="send_otp" class="btn btn-custom">Send OTP</button>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none small text-muted">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>