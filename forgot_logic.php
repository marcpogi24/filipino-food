<?php
session_start();
include 'db.php'; // Database name: lutong_bahay

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['reset_request'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // I-check kung existing ang email sa database
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result) > 0) {
        // Gumawa ng temporary OTP para sa password reset
        $reset_code = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $reset_code;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'marclaurence91@gmail.com'; 
            $mail->Password   = 'mvlouuimgwbrkysx'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('marclaurence91@gmail.com', 'Lutong Bahay Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code - Lutong Bahay';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #8B4513;'>Lutong Bahay</h2>
                    <p>We received a request to reset your password. Use the code below to proceed:</p>
                    <div style='font-size: 24px; font-weight: bold; color: #8B4513; padding: 10px; background: #fdf5e6; display: inline-block;'>
                        $reset_code
                    </div>
                    <p style='margin-top: 20px; font-size: 12px; color: #888;'>If you did not request this, please ignore this email.</p>
                </div>";

            $mail->send();
            
            // Redirect sa page kung saan i-eenter ang OTP at bagong password
            header("Location: reset_password.php");
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email address not found in our system.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>