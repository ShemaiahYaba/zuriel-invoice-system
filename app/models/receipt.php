<?php
require_once __DIR__ . '/Model.php';

/**
 * Receipt Model
 */
class Receipt extends Model {
    protected $table = 'receipts';
    
    /**
     * Generate next receipt number
     */
    public function generateReceiptNumber() {
        $prefix = Config::get('RECEIPT_PREFIX', 'RCP');
        $startNumber = Config::get('RECEIPT_START_NUMBER', 1);
        
        // Get last receipt number
        $stmt = $this->db->query("SELECT receipt_number FROM receipts ORDER BY id DESC LIMIT 1");
        $lastReceipt = $stmt->fetch();
        
        if ($lastReceipt) {
            // Extract number from last receipt
            $lastNumber = preg_replace('/[^0-9]/', '', $lastReceipt['receipt_number']);
            $nextNumber = intval($lastNumber) + 1;
        } else {
            $nextNumber = $startNumber;
        }
        
        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Convert decimal amount to Naira and Kobo
     */
    public static function convertToNairaKobo($amount) {
        $naira = floor($amount);
        $kobo = round(($amount - $naira) * 100);
        
        return [
            'naira' => $naira,
            'kobo' => $kobo
        ];
    }
    
    /**
     * Convert Naira and Kobo to decimal
     */
    public static function convertToDecimal($naira, $kobo) {
        return $naira + ($kobo / 100);
    }
    
    /**
     * Get receipts with filter
     */
    public function getFiltered($dateFrom = null, $dateTo = null, $paymentMethod = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if ($dateFrom) {
            $sql .= " AND receipt_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND receipt_date <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        if ($paymentMethod) {
            $sql .= " AND payment_method = :payment_method";
            $params['payment_method'] = $paymentMethod;
        }
        
        $sql .= " ORDER BY receipt_date DESC, id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total receipts for a date range
     */
    public function getTotalForPeriod($dateFrom, $dateTo) {
        $stmt = $this->db->prepare("
            SELECT SUM(total_amount) as total 
            FROM {$this->table} 
            WHERE receipt_date BETWEEN :date_from AND :date_to
        ");
        $stmt->execute([
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Validate receipt data
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['received_from'])) {
            $errors[] = 'Received from field is required';
        }
        
        if (empty($data['payment_for'])) {
            $errors[] = 'Payment purpose is required';
        }
        
        if (empty($data['amount_naira']) || !is_numeric($data['amount_naira'])) {
            $errors[] = 'Valid amount is required';
        }
        
        if (!in_array($data['payment_method'], ['cash', 'transfer', 'pos', 'other'])) {
            $errors[] = 'Invalid payment method';
        }
        
        return $errors;
    }
}
?>