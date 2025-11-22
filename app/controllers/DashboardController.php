<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController extends Controller {
    
    public function index() {
        // Require login
        AuthMiddleware::requireLogin();
        
        // Get statistics
        $stats = [
            'total_invoices' => $this->db->query("SELECT COUNT(*) as count FROM invoices")->fetch()['count'],
            'total_receipts' => $this->db->query("SELECT COUNT(*) as count FROM receipts")->fetch()['count'],
            'total_customers' => $this->db->query("SELECT COUNT(*) as count FROM customers")->fetch()['count'],
            'total_revenue' => $this->db->query("SELECT SUM(total_amount) as total FROM receipts")->fetch()['total'] ?? 0
        ];
        
        // Get recent invoices
        $recent_invoices = $this->db->query("
            SELECT * FROM invoices 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll();
        
        // Get recent receipts
        $recent_receipts = $this->db->query("
            SELECT * FROM receipts 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll();
        
        $this->view('dashboard', [
            'stats' => $stats,
            'recent_invoices' => $recent_invoices,
            'recent_receipts' => $recent_receipts,
            'flash' => $this->getFlash()
        ]);
    }
}
?>