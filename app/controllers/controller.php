<?php
/**
 * Base Controller Class
 * All controllers extend from this class
 */

class Controller {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Render a view
     */
    protected function view($viewPath, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: {$viewPath}");
        }
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        // If URL doesn't start with http, use Config::url()
    if (strpos($url, 'http') !== 0) {
        $url = Config::url($url);
    }
    header("Location: {$url}");
    exit;
    }
    
    /**
     * Get POST data
     */
    protected function input($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function query($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Sanitize input
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Invalid CSRF token');
        }
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get and clear flash message
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>