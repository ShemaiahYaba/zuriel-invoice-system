<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo Config::get('COMPANY_NAME'); ?></title>
    
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
            padding: 20px 0;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 15px;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .register-header {
            background: var(--primary-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .register-header img {
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .register-header h3 {
            margin: 0;
            font-size: 24px;
        }
        
        .register-body {
            padding: 40px 30px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }
        
        .btn-register {
            background: var(--primary-color);
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            background: #0052A3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 102, 204, 0.3);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .register-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .register-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-footer a:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <img src="<?php echo Config::get('COMPANY_LOGO'); ?>" alt="Logo">
                <h3>Create Account</h3>
            </div>
            
            <div class="register-body">
                <?php if (isset($flash) && $flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo Config::url('register/store'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" 
                               placeholder="Enter your full name" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" 
                               placeholder="Choose a username" required>
                        <small class="text-muted">Letters, numbers, and underscores only. Min 3 characters.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" 
                               placeholder="Enter your email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Create a password" required minlength="6">
                        <div class="password-requirements">
                            <i class="bi bi-info-circle"></i> Minimum 6 characters
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control" 
                               placeholder="Confirm your password" required minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-register">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>
                </form>
            </div>
            
            <div class="register-footer">
                Already have an account? 
                <a href="<?php echo Config::url('login'); ?>">Sign In</a>
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
        
        // Password confirmation validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>
</body>
</html>