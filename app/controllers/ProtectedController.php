<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ProtectedController extends Controller {
    public function __construct($db) {
        parent::__construct($db);
        AuthMiddleware::requireLogin();
    }
}