<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'db.php';
session_start();

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']); // Dito kinukuha ang email ng user
    $contact  = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = $_POST['password']; 

    // Validation (11 digits)
    if (!preg_match('/^[0-9]{11}$/', $contact)) {
        $_SESSION['error'] = "Ang contact number dapat ay 11 digits.";
        header("Location: register.php");
        exit();
    }

    // Check existing user
    $check_user = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = mysqli_query($conn, $check_user);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "Username o Email ay gamit na.";
        header("Location: register.php");
        exit();
    }

    // OTP Generation
    $otp = rand(100000, 999999);
    $_SESSION['temp_user'] = [
        'username' => $username,
        'email'    => $email,
        'contact'  => $contact,
        'password' => $password
    ];
    $_SESSION['otp_code'] = $otp;

    $mail = new PHPMailer(true);

    try {
        // --- SMTP CONFIG ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'marclaurence91@gmail.com'; // Ang iyong sender email
        $mail->Password   = 'mvlouuimgwbrkysx';         // Ang iyong App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465; 

        // Bypass SSL para sa Localhost
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // --- EMAIL SENDER & RECEIVER ---
        // 'marclaurence91@gmail.com' ang magse-send
        $mail->setFrom('marclaurence91@gmail.com', 'Lutong Bahay'); 
        
        // Ito ang magse-send sa gmail na nilagay ng user sa registration form
        $mail->addAddress($email); 

        $mail->isHTML(true);
        $mail->Subject = 'Verification Code - Lutong Bahay';
        $mail->Body    = "Salamat sa pag-register, <b>$username</b>!<br><br>Ang iyong OTP code ay: <h2 style='color: #8B4513;'>$otp</h2><br>Gamitin ito para ma-verify ang iyong account.";

        $mail->send();
        header("Location: verify_otp.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
        header("Location: register.php");
        exit();
    }
}
// --- LOGIN LOGIC ---
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['pass']; 

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit(); 
        } else {
            $_SESSION['error'] = "Maling password.";
        }
    } else {
        $_SESSION['error'] = "Hindi mahanap ang username.";
    }
    header("Location: login.php");
    exit();
}
?>