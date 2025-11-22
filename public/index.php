<?php
/**
 * Front Controller
 * All requests are routed through this file
 */

session_start();

// Enable error reporting for development (REMOVE IN PRODUCTION)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load dependencies
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

// Get database connection
$db = Database::getInstance()->getConnection();

// Load configuration
Config::load($db);

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove /public/index.php from the URI if present
$requestUri = str_replace('/public/index.php', '', $requestUri);

// Get the base path dynamically
// For: /zuriel-invoice-system/public/index.php -> base is /zuriel-invoice-system
$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = str_replace('/public', '', $scriptPath);

// Remove the base path from request URI
if (!empty($basePath) && $basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Ensure URI starts with /
if (empty($requestUri) || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// Remove trailing slash (except for root)
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}

// Simple router
$routes = [
    // Home
    'GET /' => ['controller' => 'InvoiceController', 'action' => 'index'],
    
    // Invoices
    'GET /invoices' => ['controller' => 'InvoiceController', 'action' => 'index'],
    'GET /invoices/create' => ['controller' => 'InvoiceController', 'action' => 'create'],
    'POST /invoices/store' => ['controller' => 'InvoiceController', 'action' => 'store'],
    'GET /invoices/view/{id}' => ['controller' => 'InvoiceController', 'action' => 'show'],
    'GET /invoices/edit/{id}' => ['controller' => 'InvoiceController', 'action' => 'edit'],
    'POST /invoices/update/{id}' => ['controller' => 'InvoiceController', 'action' => 'update'],
    'GET /invoices/delete/{id}' => ['controller' => 'InvoiceController', 'action' => 'delete'],
    'GET /invoices/print/{id}' => ['controller' => 'InvoiceController', 'action' => 'printInvoice'],
    
    // Receipts
    'GET /receipts' => ['controller' => 'ReceiptController', 'action' => 'index'],
    'GET /receipts/create' => ['controller' => 'ReceiptController', 'action' => 'create'],
    'POST /receipts/store' => ['controller' => 'ReceiptController', 'action' => 'store'],
    'GET /receipts/view/{id}' => ['controller' => 'ReceiptController', 'action' => 'show'],
    'GET /receipts/edit/{id}' => ['controller' => 'ReceiptController', 'action' => 'edit'],
    'POST /receipts/update/{id}' => ['controller' => 'ReceiptController', 'action' => 'update'],
    'GET /receipts/delete/{id}' => ['controller' => 'ReceiptController', 'action' => 'delete'],
    'GET /receipts/print/{id}' => ['controller' => 'ReceiptController', 'action' => 'printReceipt'],
    
    // Customers
    'GET /customers' => ['controller' => 'CustomerController', 'action' => 'index'],
    'GET /customers/create' => ['controller' => 'CustomerController', 'action' => 'create'],
    'POST /customers/store' => ['controller' => 'CustomerController', 'action' => 'store'],
    'GET /customers/view/{id}' => ['controller' => 'CustomerController', 'action' => 'show'],
    'GET /customers/edit/{id}' => ['controller' => 'CustomerController', 'action' => 'edit'],
    'POST /customers/update/{id}' => ['controller' => 'CustomerController', 'action' => 'update'],
    'GET /customers/delete/{id}' => ['controller' => 'CustomerController', 'action' => 'delete'],
    'GET /customers/search' => ['controller' => 'CustomerController', 'action' => 'search'],
    
    // Products
    'GET /products' => ['controller' => 'ProductController', 'action' => 'index'],
    'GET /products/create' => ['controller' => 'ProductController', 'action' => 'create'],
    'POST /products/store' => ['controller' => 'ProductController', 'action' => 'store'],
    'GET /products/edit/{id}' => ['controller' => 'ProductController', 'action' => 'edit'],
    'POST /products/update/{id}' => ['controller' => 'ProductController', 'action' => 'update'],
    'GET /products/delete/{id}' => ['controller' => 'ProductController', 'action' => 'delete'],
    'GET /products/search' => ['controller' => 'ProductController', 'action' => 'search'],
    
    // Configuration
    'GET /settings' => ['controller' => 'ConfigController', 'action' => 'index'],
    'POST /settings/update' => ['controller' => 'ConfigController', 'action' => 'update'],

    // Authentication Routes
'GET /login' => ['controller' => 'AuthController', 'action' => 'login'],
'POST /authenticate' => ['controller' => 'AuthController', 'action' => 'authenticate'],
'GET /logout' => ['controller' => 'AuthController', 'action' => 'logout'],
'GET /register' => ['controller' => 'AuthController', 'action' => 'register'],
'POST /register/store' => ['controller' => 'AuthController', 'action' => 'store'],
'GET /forgot-password' => ['controller' => 'AuthController', 'action' => 'forgotPassword'],
'POST /reset-password' => ['controller' => 'AuthController', 'action' => 'resetPassword'],
'GET /change-password' => ['controller' => 'AuthController', 'action' => 'changePassword'],
'POST /update-password' => ['controller' => 'AuthController', 'action' => 'updatePassword'],

// Dashboard
'GET /dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
];

// Match route
$matchedRoute = null;
$params = [];

foreach ($routes as $route => $handler) {
    list($method, $path) = explode(' ', $route);
    
    if ($method !== $requestMethod) {
        continue;
    }
    
    // Convert route pattern to regex
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $requestUri, $matches)) {
        $matchedRoute = $handler;
        
        // Extract named parameters
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        
        break;
    }
}

// Execute matched route
if ($matchedRoute) {
    $controllerName = $matchedRoute['controller'];
    $actionName = $matchedRoute['action'];
    
    $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        $controller = new $controllerName($db);
        
        // Call action with parameters
        if (method_exists($controller, $actionName)) {
            call_user_func_array([$controller, $actionName], $params);
        } else {
            http_response_code(404);
            echo "Action not found: {$actionName}";
        }
    } else {
        http_response_code(404);
        echo "Controller not found: {$controllerName}";
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Page not found</h1>";
    echo "<p>Requested URI: <strong>" . htmlspecialchars($requestUri) . "</strong></p>";
    echo "<p>Request Method: <strong>" . htmlspecialchars($requestMethod) . "</strong></p>";
    echo "<hr>";
    echo "<h3>Debug Information:</h3>";
    echo "<ul>";
    echo "<li>Original REQUEST_URI: <code>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</code></li>";
    echo "<li>SCRIPT_NAME: <code>" . htmlspecialchars($_SERVER['SCRIPT_NAME']) . "</code></li>";
    echo "<li>Parsed requestUri: <code>" . htmlspecialchars($requestUri) . "</code></li>";
    echo "<li>Request Method: <code>" . htmlspecialchars($requestMethod) . "</code></li>";
    echo "</ul>";
    echo "<hr>";
    echo "<h3>Available Routes:</h3>";
    echo "<ul>";
    foreach ($routes as $route => $handler) {
        echo "<li>" . htmlspecialchars($route) . "</li>";
    }
    echo "</ul>";
    echo "<hr>";
    echo "<p><a href='/zuriel-invoice-system/public/'>Try: /zuriel-invoice-system/public/</a></p>";
}
?>