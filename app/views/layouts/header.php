<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? Config::get('COMPANY_NAME'); ?> - Invoice System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            --header-bg: <?php echo Config::get('HEADER_BG_COLOR', '#0066CC'); ?>;
            --gradient-start: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            --gradient-end: #0052A3;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Fancy Company Banner */
        .company-banner {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 50%, #003d7a 100%);
            color: white;
            padding: 30px 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        /* Animated Background Elements */
        .company-banner::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 8s ease-in-out infinite;
        }
        
        .company-banner::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse 10s ease-in-out infinite reverse;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        
        /* Decorative Lines */
        .banner-decoration {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            pointer-events: none;
        }
        
        .banner-decoration .line {
            position: absolute;
            background: white;
            transform: rotate(-45deg);
        }
        
        .banner-decoration .line:nth-child(1) {
            width: 2px;
            height: 300px;
            top: -50px;
            right: 20%;
            animation: slideDown 3s ease-in-out infinite;
        }
        
        .banner-decoration .line:nth-child(2) {
            width: 2px;
            height: 400px;
            top: -100px;
            right: 40%;
            animation: slideDown 4s ease-in-out infinite;
        }
        
        .banner-decoration .line:nth-child(3) {
            width: 2px;
            height: 250px;
            top: -50px;
            right: 60%;
            animation: slideDown 3.5s ease-in-out infinite;
        }
        
        @keyframes slideDown {
            0%, 100% {
                transform: translateY(0) rotate(-45deg);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
        }
        
        /* Company Logo Section */
        .company-logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }
        
        .logo-container {
            position: relative;
        }
        
        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110px;
            height: 110px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
            border-radius: 50%;
            animation: glow 3s ease-in-out infinite;
        }
        
        @keyframes glow {
            0%, 100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.5;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.8;
            }
        }
        
        .company-logo-section img {
            height: 90px;
            width: auto;
            background: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }
        
        .company-logo-section img:hover {
            transform: scale(1.05) rotate(2deg);
        }
        
        .company-info h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
            background: linear-gradient(to right, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .company-info .tagline {
            font-size: 1rem;
            opacity: 0.95;
            font-style: italic;
            margin-top: 8px;
            font-weight: 300;
            letter-spacing: 0.5px;
        }
        
        /* Contact Information Styling */
        .company-contact-info {
            position: relative;
            z-index: 2;
            text-align: right;
        }
        
        .contact-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .company-contact-info .contact-item {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 0.95rem;
            transition: transform 0.2s ease;
        }
        
        .company-contact-info .contact-item:hover {
            transform: translateX(-5px);
        }
        
        .company-contact-info .contact-item:last-child {
            margin-bottom: 0;
        }
        
        .company-contact-info .contact-item i {
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .company-contact-info .contact-item span {
            font-weight: 400;
        }
        
        /* Navigation Styles */
        .navbar {
            background: linear-gradient(to right, var(--primary-color), var(--gradient-end)) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-top: 2px solid rgba(255, 255, 255, 0.1);
            padding: 12px 0;
        }
        
        .navbar-nav .nav-link {
            position: relative;
            transition: all 0.3s ease;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 8px;
        }
        
        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: white;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link:hover::before {
            width: 80%;
        }
        
        .navbar-nav .nav-link i {
            margin-right: 5px;
            font-size: 1.1rem;
        }
        
        .dropdown-menu {
            background: white;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-top: 10px;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--gradient-end));
            border: none;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gradient-end), #002d5a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .company-banner {
                padding: 20px 0;
            }
            
            .company-logo-section {
                flex-direction: column;
                text-align: center;
                margin-bottom: 20px;
            }
            
            .company-info h1 {
                font-size: 1.8rem;
            }
            
            .company-contact-info {
                text-align: center;
            }
            
            .contact-card {
                padding: 15px;
            }
            
            .company-contact-info .contact-item {
                justify-content: center;
                font-size: 0.9rem;
            }
            
            .company-logo-section img {
                height: 70px;
            }
            
            .navbar-nav .nav-link {
                margin: 5px 0;
            }
        }
        
        /* Main Content Styling */
        .container-fluid.mt-4 {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Fancy Company Banner -->
    <div class="company-banner">
        <div class="banner-decoration">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-7">
                    <div class="company-logo-section">
                        <div class="logo-container">
                            <div class="logo-glow"></div>
                            <img src="<?php echo Config::get('COMPANY_LOGO'); ?>" 
                                 alt="<?php echo Config::get('COMPANY_NAME'); ?> Logo">
                        </div>
                        <div class="company-info">
                            <h1><?php echo Config::get('COMPANY_NAME'); ?></h1>
                            <?php if (Config::get('COMPANY_TAGLINE')): ?>
                                <div class="tagline"><?php echo Config::get('COMPANY_TAGLINE'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="company-contact-info">
                        <div class="contact-card">
                            <?php if (Config::get('COMPANY_ADDRESS')): ?>
                            <div class="contact-item">
                                <span><?php echo Config::get('COMPANY_ADDRESS'); ?></span>
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (Config::get('COMPANY_PHONE_1')): ?>
                            <div class="contact-item">
                                <span><?php echo Config::get('COMPANY_PHONE_1'); ?>
                                <?php if (Config::get('COMPANY_PHONE_2')): ?>
                                    | <?php echo Config::get('COMPANY_PHONE_2'); ?>
                                <?php endif; ?>
                                </span>
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (Config::get('COMPANY_EMAIL')): ?>
                            <div class="contact-item">
                                <span><?php echo Config::get('COMPANY_EMAIL'); ?></span>
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::url('invoices'); ?>">
                            <i class="bi bi-file-earmark-text"></i> Invoices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::url('receipts'); ?>">
                            <i class="bi bi-receipt"></i> Receipts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::url('customers'); ?>">
                            <i class="bi bi-people"></i> Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::url('products'); ?>">
                            <i class="bi bi-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo Config::url('settings'); ?>">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo Config::url('change-password'); ?>">
                                <i class="bi bi-key"></i> Change Password
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo Config::url('logout'); ?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <?php if (isset($flash) && $flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>