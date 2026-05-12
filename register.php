<?php 
session_start(); 
include 'db.php'; 

// I-prepare ang PHPMailer para magamit sa auth_logic.php o sa page na ito
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lutong Bahay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        :root { --primary-color: #8B4513; }
        
        /* 1. Inalis ang overflow:hidden para makapag-scroll kung mahaba ang form */
        body, html { min-height: 100%; margin: 0; font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        
        .main-container { display: flex; min-height: 100vh; width: 100%; }
        
        /* 2. Brand side settings */
        .brand-side { 
            flex: 1.2; 
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('luto.png'); 
            background-size: cover; 
            background-position: center; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            color: white; 
            text-align: center; 
            padding: 20px;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        
        .brand-content h1 { font-family: 'Great Vibes', cursive; font-size: 6rem; text-shadow: 2px 2px 10px rgba(0,0,0,0.5); }
        
        /* 3. Pinaganda ang spacing ng Form Side */
        .form-side { 
            flex: 0.8; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 50px 20px; /* Nilakihan ang padding sa taas at baba */
        }
        
        .register-box { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 450px; /* Bahagyang nilakihan ang width */
        }

        .form-control { border-radius: 10px; padding: 12px 15px; background: #f1f1f1; border: 1px solid transparent; }
        .btn-custom { background: var(--primary-color); color: white; border: none; border-radius: 10px; padding: 14px; font-weight: 700; width: 100%; transition: 0.3s; margin-top: 15px; }
        .btn-custom:hover { background: #5d2e0d; transform: translateY(-2px); color: white; }
        .input-group-text { background: #f1f1f1; border: none; color: var(--primary-color); border-radius: 10px 0 0 10px; }
        
        .eye-toggle { cursor: pointer; border-radius: 0 10px 10px 0 !important; background: #f1f1f1; color: #888; padding-right: 15px; }

        /* 4. Responsive reCAPTCHA */
        .captcha-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            transform: scale(0.9); /* Bahagyang pinaliit para magkasya sa mobile */
            transform-origin: center;
        }

        @media (max-width: 992px) { 
            .main-container { flex-direction: column; }
            .brand-side { display: none; } 
            .form-side { flex: 1; padding: 40px 20px; } 
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="brand-side animate__animated animate__fadeIn">
            <div class="brand-content">
                <h1>Lutong Bahay</h1>
                <p>Join our growing family.</p>
            </div>
        </div>
        
        <div class="form-side">
            <div class="register-box animate__animated animate__fadeInRight">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: var(--primary-color);">CREATE ACCOUNT</h3>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success py-2 small text-center animate__animated animate__fadeIn">
                        <i class="fas fa-check-circle me-1"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger py-2 small text-center">
                        <i class="fas fa-exclamation-triangle me-1"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="auth_logic.php" method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Choose username" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Contact Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="contact" class="form-control" placeholder="09123456789" maxlength="11" pattern="\d{11}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="regPass" class="form-control" style="border-radius: 0;" placeholder="Password" required>
                            <span class="input-group-text eye-toggle" onclick="togglePass('regPass', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <!-- reCAPTCHA Container -->
                    <div class="captcha-container">
                        <div class="g-recaptcha" data-sitekey="6Lc6-d4sAAAAAPRpOwY87d0CemYNUsf7FSNBAZEd"></div>
                    </div>

                    <button type="submit" name="register" class="btn btn-custom shadow-sm">REGISTER NOW</button>
                    
                    <div class="text-center mt-3 small">
                        Already have an account? <a href="login.php" class="fw-bold text-decoration-none" style="color: var(--primary-color);">Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePass(id, el) {
            const input = document.getElementById(id);
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