<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo Config::get('COMPANY_NAME'); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #004999 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: var(--primary-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-header img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        
        .login-header h3 {
            margin: 0;
            font-size: 24px;
        }
        
        .login-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        .btn-login {
            background: var(--primary-color);
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #0052A3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 102, 204, 0.3);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border-right: none;
        }
        
        .form-control {
            border-left: none;
        }
        
        .form-control:focus + .input-group-text {
            border-color: var(--primary-color);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="<?php echo Config::get('COMPANY_LOGO'); ?>" alt="Logo">
                <h3><?php echo Config::get('COMPANY_NAME'); ?></h3>
                <p><?php echo Config::get('COMPANY_TAGLINE'); ?></p>
            </div>
            
            <div class="login-body">
                <?php if (isset($flash) && $flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <h4 class="mb-4 text-center">Sign In</h4>
                
                <form method="POST" action="<?php echo Config::url('authenticate'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control" 
                                   placeholder="Enter your username" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <div class="mb-4 remember-me">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                            <label class="form-check-label" for="remember_me">
                                Remember me
                            </label>
                        </div>
                        <a href="<?php echo Config::url('forgot-password'); ?>" class="text-muted small">
                            Forgot password?
                        </a>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                Don't have an account? 
                <a href="<?php echo Config::url('register'); ?>">Sign Up</a>
            </div>
        </div>
        
        <p class="text-center text-white mt-4">
            <small>&copy; <?php echo date('Y'); ?> <?php echo Config::get('COMPANY_NAME'); ?>. All rights reserved.</small>
        </p>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>