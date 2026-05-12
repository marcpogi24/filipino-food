<?php
session_start();
include 'db.php'; // Database: lutong_bahay

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (!isset($_SESSION['otp_code'])) {
    header("Location: register.php");
    exit();
}

// --- RESEND LOGIC: Pag-ulit ng OTP ---
if (isset($_POST['resend'])) {
    $new_otp = rand(100000, 999999);
    $_SESSION['otp_code'] = $new_otp;
    $email = $_SESSION['temp_user']['email'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'marclaurence91@gmail.com'; 
        $mail->Password   = 'mvlouuimgwbrkysx'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('marclaurence91@gmail.com', 'Lutong Bahay');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'New OTP Code - Lutong Bahay';
        $mail->Body    = "Ang iyong bagong verification code ay: <b>$new_otp</b>";

        $mail->send();
        $success = "Bagong code ang ipinadala!";
    } catch (Exception $e) {
        $error = "Error: " . $mail->ErrorInfo;
    }
}

// --- VERIFY LOGIC ---
if (isset($_POST['verify'])) {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp_code']) {
        $data = $_SESSION['temp_user']; 
        $username = mysqli_real_escape_string($conn, $data['username']);
        $email    = mysqli_real_escape_string($conn, $data['email']);
        $contact  = mysqli_real_escape_string($conn, $data['contact']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, contact_number, password, role) 
                  VALUES ('$username', '$email', '$contact', '$password', 'user')";

        if (mysqli_query($conn, $query)) {
            unset($_SESSION['otp_code']);
            unset($_SESSION['temp_user']);
            $_SESSION['success'] = "Salamat sa pag-join sa aming growing family! Pwede ka na mag-login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Mali ang OTP code. Pakisubukang muli.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Lutong Bahay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #fdf5e6; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .otp-box { background: white; padding: 40px; border-radius: 25px; box-shadow: 0 15px 35px rgba(139, 69, 19, 0.15); width: 100%; max-width: 420px; text-align: center; border-top: 5px solid #8B4513; }
        .brand-name { font-family: 'Great Vibes', cursive; font-size: 2.5rem; color: #8B4513; margin-bottom: 10px; }
        .btn-custom { background: #8B4513; color: white; border-radius: 12px; width: 100%; padding: 14px; font-weight: 700; border: none; transition: 0.3s; }
        .btn-custom:hover:not(:disabled) { background: #5d2e0d; transform: translateY(-2px); }
        .otp-input { font-size: 2.5rem; letter-spacing: 10px; font-weight: 800; border-radius: 15px; color: #8B4513; border: 2px solid #eee; }
        #timer { color: #d9534f; font-weight: bold; }
        .resend-btn { background: none; border: none; color: #8B4513; font-weight: 600; text-decoration: underline; display: none; }
    </style>
</head>
<body>
    <div class="otp-box">
        <div class="brand-name">Lutong Bahay</div>
        <h4 class="fw-bold mb-2">Verify OTP</h4>
        <p class="text-muted small mb-4">I-check ang code sa: <b><?php echo $_SESSION['temp_user']['email']; ?></b></p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <div class="alert alert-success py-2 small"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" id="mainForm">
            <div class="mb-3">
                <input type="text" name="otp" id="otpField" class="form-control text-center otp-input" 
                       placeholder="000000" maxlength="6" pattern="\d{6}" required autocomplete="off">
            </div>
            
            <p id="timerContainer" class="small text-muted mb-4">Mag-eexpire ang code sa: <span id="timer">01:00</span></p>

            <button type="submit" name="verify" id="verifyBtn" class="btn btn-custom">VERIFY CODE</button>
        </form>

        <!-- Hidden Resend Form -->
        <form method="POST" id="resendForm">
            <button type="submit" name="resend" id="resendBtn" class="resend-btn mt-3">Muling magpadala ng code (Resend)</button>
        </form>
        
        <div class="mt-4 small">
            <a href="register.php" class="text-decoration-none fw-bold" style="color: #8B4513;">
                <i class="fas fa-redo me-1"></i> Ulitin ang Registration
            </a>
        </div>
    </div>

    <script>
        let timeLeft = 60;
        const timerElement = document.getElementById('timer');
        const otpField = document.getElementById('otpField');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const timerContainer = document.getElementById('timerContainer');

        const countdown = setInterval(function() {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerContainer.style.display = "none";
                otpField.disabled = true;
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = "CODE EXPIRED";
                resendBtn.style.display = "inline-block"; // Lalabas ang Resend option
            } else {
                let seconds = timeLeft < 10 ? '0' + timeLeft : timeLeft;
                timerElement.innerHTML = `00:${seconds}`;
            }
            timeLeft -= 1;
        }, 1000);
    </script>
</body>
</html>