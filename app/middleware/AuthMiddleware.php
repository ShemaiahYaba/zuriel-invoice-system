<?php
/**
 * Authentication Middleware
 * Protects routes that require authentication
 */

class AuthMiddleware {
    
    /**
     * Check if user is logged in
     */
    public static function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            // Store intended destination
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            
            // Redirect to login
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Please log in to access this page'
            ];
            
            header('Location: ' . Config::url('login'));
            exit;
        }
    }
    
    /**
     * Check if user has required role
     */
    public static function requireRole($allowedRoles) {
        // First check if logged in
        self::requireLogin();
        
        // Make sure $allowedRoles is an array
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        $userRole = $_SESSION['role'] ?? null;
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'You do not have permission to access this page'
            ];
            
            header('Location: ' . Config::url('dashboard'));
            exit;
        }
    }
    
    /**
     * Check if user is admin
     */
    public static function requireAdmin() {
        self::requireRole('admin');
    }
    
    /**
     * Check if user is admin or manager
     */
    public static function requireManager() {
        self::requireRole(['admin', 'manager']);
    }
    
    /**
     * Check if user is guest (not logged in)
     */
    public static function requireGuest() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            header('Location: ' . Config::url('dashboard'));
            exit;
        }
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current username
     */
    public static function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    /**
     * Get current user's full name
     */
    public static function getFullName() {
        return $_SESSION['full_name'] ?? null;
    }
    
    /**
     * Get current user's role
     */
    public static function getRole() {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Check if current user has a specific role
     */
    public static function hasRole($role) {
        $userRole = self::getRole();
        
        if (is_array($role)) {
            return in_array($userRole, $role);
        }
        
        return $userRole === $role;
    }
    
    /**
     * Check if current user is admin
     */
    public static function isAdmin() {
        return self::hasRole('admin');
    }
    
    /**
     * Check if current user is manager or admin
     */
    public static function isManager() {
        return self::hasRole(['admin', 'manager']);
    }
    
    /**
     * Check if user is logged in (without redirecting)
     */
    public static function check() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}
?>