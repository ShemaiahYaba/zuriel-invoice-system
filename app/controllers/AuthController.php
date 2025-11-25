<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

/**
 * Authentication Controller
 * Handles login, logout, and registration
 */
class AuthController extends Controller {
    private $userModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new User($db);
    }
    
    /**
     * Show login form
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $this->view('auth/login', [
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Process login
     */
    public function authenticate() {
        $this->validateCsrfToken();
        
        $username = $this->sanitize($this->input('username'));
        $password = $this->input('password');
        $remember = $this->input('remember_me') ? true : false;
        
        // Get user IP
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        
        // Check for too many failed attempts
        $failedAttempts = $this->userModel->getFailedAttempts($username, $ipAddress, 15);
        if ($failedAttempts >= 5) {
            $this->setFlash('danger', 'Too many failed login attempts. Please try again in 15 minutes.');
            $this->redirect('login');
            return;
        }
        
        // Find user
        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            // Record failed attempt
            $this->userModel->recordLoginAttempt($username, $ipAddress, false);
            $this->setFlash('danger', 'Invalid username or password');
            $this->redirect('login');
            return;
        }
        
        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            // Record failed attempt
            $this->userModel->recordLoginAttempt($username, $ipAddress, false);
            $this->setFlash('danger', 'Invalid username or password');
            $this->redirect('login');
            return;
        }
        
        // Check if account is active
        if ($user['status'] !== 'active') {
            $this->setFlash('danger', 'Your account has been ' . $user['status'] . '. Please contact an administrator.');
            $this->redirect('login');
            return;
        }
        
        // Login successful
        $this->userModel->recordLoginAttempt($username, $ipAddress, true);
        $this->userModel->updateLastLogin($user['id']);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        // Remember me functionality
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
            // Store token in database (implement session table usage)
        }
        
        $this->setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
        
        // Redirect to intended page or dashboard
        $redirectTo = $_SESSION['redirect_after_login'] ?? 'dashboard';
        unset($_SESSION['redirect_after_login']);
        
        $this->redirect($redirectTo);
    }
    
    /**
     * Logout
     */
    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        $this->setFlash('success', 'You have been logged out successfully');
        $this->redirect('login');
    }
    
    /**
     * Show registration form
     */
    public function register() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $this->view('auth/register', [
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Process registration
     */
    public function store() {
        $this->validateCsrfToken();
        
        $data = [
            'username' => $this->sanitize($this->input('username')),
            'email' => $this->sanitize($this->input('email')),
            'password' => $this->input('password'),
            'confirm_password' => $this->input('confirm_password'),
            'full_name' => $this->sanitize($this->input('full_name')),
            'role' => 'staff', // Default role for new registrations
            'status' => 'active'
        ];
        
        // Validate
        $errors = $this->userModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect('register');
            return;
        }
        
        // Remove confirm_password before creating user
        unset($data['confirm_password']);
        
        // Create user
        try {
            $this->userModel->createUser($data);
            $this->setFlash('success', 'Account created successfully! You can now log in.');
            $this->redirect('login');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error creating account: ' . $e->getMessage());
            $this->redirect('register');
        }
    }
    
    /**
     * Show forgot password form
     */
    public function forgotPassword() {
        $this->view('auth/forgot-password', [
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Process password reset
     */
    public function resetPassword() {
        $this->validateCsrfToken();
        
        $usernameOrEmail = $this->sanitize($this->input('username'));
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        // Basic validation
        if (empty($usernameOrEmail) || empty($newPassword) || empty($confirmPassword)) {
            $this->setFlash('error', 'All fields are required.');
            $this->redirect('forgot-password');
            return;
        }
        
        // Check if passwords match
        if ($newPassword !== $confirmPassword) {
            $this->setFlash('error', 'Passwords do not match.');
            $this->redirect('forgot-password');
            return;
        }
        
        // Check password length
        if (strlen($newPassword) < 6) {
            $this->setFlash('error', 'Password must be at least 6 characters long.');
            $this->redirect('forgot-password');
            return;
        }
        
        // Try to find user by email or username
        $user = $this->userModel->findByEmail($usernameOrEmail);
        if (!$user) {
            // If not found by email, try by username
            $user = $this->userModel->findByUsername($usernameOrEmail);
        }
        
        if ($user) {
            // Update the user's password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userModel->update($user['id'], ['password' => $hashedPassword]);
            
            $this->setFlash('success', 'Your password has been updated successfully. You can now login with your new password.');
            $this->redirect('login');
        } else {
            // For security, don't reveal if the user exists or not
            $this->setFlash('success', 'If an account exists with that username/email, the password has been updated.');
            $this->redirect('login');
        }
    }
    
    /**
     * Show change password form (for logged-in users)
     */
    public function changePassword() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }
        
        $this->view('auth/change-password', [
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Process password change
     */
    public function updatePassword() {
        $this->validateCsrfToken();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }
        
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        // Get current user
        $user = $this->userModel->find($_SESSION['user_id']);
        
        // Verify current password
        if (!$this->userModel->verifyPassword($currentPassword, $user['password'])) {
            $this->setFlash('danger', 'Current password is incorrect');
            $this->redirect('change-password');
            return;
        }
        
        // Validate new password
        if (strlen($newPassword) < 6) {
            $this->setFlash('danger', 'New password must be at least 6 characters');
            $this->redirect('change-password');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->setFlash('danger', 'New passwords do not match');
            $this->redirect('change-password');
            return;
        }
        
        // Update password
        try {
            $this->userModel->changePassword($_SESSION['user_id'], $newPassword);
            $this->setFlash('success', 'Password changed successfully');
            $this->redirect('dashboard');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error changing password: ' . $e->getMessage());
            $this->redirect('change-password');
        }
    }
}
?>