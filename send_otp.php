<?php
include 'db.php'; // Your database name should be lutong_bahay
session_start();

if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if user exists in the database
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
        
        // Save to database columns
        mysqli_query($conn, "UPDATE users SET otp_code = '$otp', otp_expiry = '$expiry' WHERE email = '$email'");

        // Save to session to fix the "Undefined array key" error
        $_SESSION['reset_email'] = $email;
        $_SESSION['temp_otp'] = $otp; 
        
        header("Location: verify_otp.php"); 
        exit();
    } else {
        echo "<script>alert('Email not found!'); window.location='forgot_password.php';</script>";
    }
}
?>