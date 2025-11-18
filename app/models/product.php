<?php
require_once __DIR__ . '/Model.php';

/**
 * Product Model
 */
class Product extends Model {
    protected $table = 'products';
    
    /**
     * Get products ordered by description
     */
    public function getAllOrdered() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY description ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Search products by description
     */
    public function searchByDescription($query) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE description LIKE :query 
            ORDER BY description ASC
        ");
        $stmt->execute(['query' => "%{$query}%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Validate product data
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['description'])) {
            $errors[] = 'Product description is required';
        }
        
        if (empty($data['rate']) || !is_numeric($data['rate']) || $data['rate'] < 0) {
            $errors[] = 'Valid product rate is required';
        }
        
        return $errors;
    }
}
?>