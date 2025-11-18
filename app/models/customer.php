<?php
require_once __DIR__ . '/Model.php';

/**
 * Customer Model
 */
class Customer extends Model {
    protected $table = 'customers';
    
    /**
     * Get customer with invoice count
     */
    public function getWithInvoiceCount() {
        $sql = "
            SELECT c.*, COUNT(i.id) as invoice_count 
            FROM customers c 
            LEFT JOIN invoices i ON c.id = i.customer_id 
            GROUP BY c.id 
            ORDER BY c.created_at DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get customer invoices
     */
    public function getInvoices($customerId) {
        $stmt = $this->db->prepare("
            SELECT * FROM invoices 
            WHERE customer_id = :customer_id 
            ORDER BY invoice_date DESC
        ");
        $stmt->execute(['customer_id' => $customerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Validate customer data
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Customer name is required';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        return $errors;
    }
}
?>