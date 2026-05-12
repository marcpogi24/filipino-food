<?php 
include 'db.php'; 
session_start(); 

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
        exit(); 
    } else {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lutong Bahay</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-color: #8B4513; --bg-light: #fdf5e6; }
        body, html { height: 100%; margin: 0; font-family: 'Poppins', sans-serif; overflow: hidden; }
        .main-container { display: flex; height: 100vh; width: 100%; }
        
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
            padding: 40px; 
        }
        
        .brand-content h1 { font-family: 'Great Vibes', cursive; font-size: 7rem; margin-bottom: 0; }
        .brand-content p { font-size: 1.8rem; border-top: 2px solid white; padding-top: 10px; letter-spacing: 1px; }
        
        .form-side { flex: 0.8; background-color: var(--bg-light); display: flex; align-items: center; justify-content: center; padding: 40px; }
        .login-box { background: white; padding: 45px; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); width: 100%; max-width: 440px; }
        
        .form-label-custom { font-size: 0.8rem; font-weight: 700; color: #444; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { border-radius: 12px; padding: 14px 18px; background: #f3f4f6; border: 2px solid transparent; transition: 0.3s; font-size: 0.95rem; }
        .form-control:focus { background: white; border-color: var(--primary-color); box-shadow: none; }
        
        .input-group-text { background: #f3f4f6; border: none; color: var(--primary-color); border-radius: 12px 0 0 12px; padding-left: 20px; }
        .eye-toggle { cursor: pointer; border-radius: 0 12px 12px 0 !important; background: #f3f4f6; color: #888; border: none; padding-right: 20px; }
        
        .btn-custom { background: var(--primary-color); color: white; border: none; border-radius: 12px; padding: 15px; font-weight: 700; width: 100%; transition: 0.4s; font-size: 1.1rem; }
        .btn-custom:hover { background: #5d2e0d; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(139, 69, 19, 0.2); }

        .forgot-link { font-size: 0.85rem; color: #888; text-decoration: none; transition: 0.3s; }
        .forgot-link:hover { color: var(--primary-color); text-decoration: underline; }

        @media (max-width: 992px) { .brand-side { display: none; } .form-side { flex: 1; } }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="brand-side animate__animated animate__fadeIn">
            <div class="brand-content">
                <h1>Lutong Bahay</h1>
                <p>Taste the goodness of home-cooked meals.</p>
            </div>
        </div>

        <div class="form-side">
            <div class="login-box animate__animated animate__fadeInRight">
                
                <div class="text-center mb-5">
                    <h2 class="fw-bold" style="color: var(--primary-color); font-size: 2.2rem; letter-spacing: 2px;">LOGIN</h2>
                    <p class="text-muted small">Please login to your account.</p>
                </div>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger py-2 small text-center animate__animated animate__shakeX">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="auth_logic.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label-custom">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="pass" id="passField" class="form-control" placeholder="••••••••" required>
                            <span class="input-group-text eye-toggle" onclick="togglePass('passField', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-custom shadow-sm mb-3">
                        LOG IN <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>

                    <div class="text-center">
                        <!-- Forgot Password moved here -->
                        <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotModal">Forgot Password?</a>
                    </div>

                    <div class="text-center mt-5 pt-3 border-top">    
                        <span class="text-muted small">No account yet?</span>
                        <a href="register.php" class="fw-bold text-decoration-none ms-1" style="color: var(--primary-color);">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FORGOT PASSWORD MODAL (ENGLISH) -->
    <div class="modal fade" id="forgotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 25px; border: none;">
                <div class="modal-body p-5 text-center">
                    <div class="mb-3">
                        <i class="fas fa-envelope-open-text fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h4 class="fw-bold">Reset Password</h4>
                    <p class="text-muted small mb-4">Enter your registered email address to receive a password reset link.</p>
                    
                    <form action="forgot_logic.php" method="POST">
                        <input type="email" name="email" class="form-control mb-3 text-center" placeholder="email@example.com" required>
                        <button type="submit" name="reset_request" class="btn btn-custom">SEND RESET LINK</button>
                    </form>
                    
                    <button type="button" class="btn btn-link btn-sm mt-3 text-decoration-none text-muted" data-bs-dismiss="modal">Back to Login</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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