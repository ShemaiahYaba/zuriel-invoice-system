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
     * Create receipt with invoice update
     */
    public function createWithInvoice($data) {
        try {
            $this->db->beginTransaction();
            
            // Create receipt
            $receiptId = $this->create($data);
            
            // If linked to invoice, update invoice status and paid amount
            if (!empty($data['invoice_id'])) {
                $this->updateInvoicePayment($data['invoice_id'], $data['total_amount']);
            }
            
            $this->db->commit();
            return $receiptId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update receipt with invoice update
     */
    public function updateWithInvoice($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Get old receipt data
            $oldReceipt = $this->find($id);
            
            // Update receipt
            $this->update($id, $data);
            
            // If invoice changed, update both old and new invoice
            if (!empty($oldReceipt['invoice_id']) && $oldReceipt['invoice_id'] != ($data['invoice_id'] ?? null)) {
                // Recalculate old invoice
                $this->recalculateInvoicePayment($oldReceipt['invoice_id']);
            }
            
            // Update new invoice if exists
            if (!empty($data['invoice_id'])) {
                $this->recalculateInvoicePayment($data['invoice_id']);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update invoice payment status
     */
    private function updateInvoicePayment($invoiceId, $amount) {
        // Get invoice total
        $stmt = $this->db->prepare("SELECT total FROM invoices WHERE id = :id");
        $stmt->execute(['id' => $invoiceId]);
        $invoice = $stmt->fetch();
        
        if ($invoice) {
            // Calculate total paid amount
            $stmt = $this->db->prepare("
                SELECT SUM(total_amount) as total_paid 
                FROM receipts 
                WHERE invoice_id = :invoice_id
            ");
            $stmt->execute(['invoice_id' => $invoiceId]);
            $result = $stmt->fetch();
            $totalPaid = $result['total_paid'] ?? 0;
            
            // Update invoice
            $status = ($totalPaid >= $invoice['total']) ? 'paid' : 'issued';
            $stmt = $this->db->prepare("
                UPDATE invoices 
                SET paid_amount = :paid_amount, status = :status 
                WHERE id = :id
            ");
            $stmt->execute([
                'paid_amount' => $totalPaid,
                'status' => $status,
                'id' => $invoiceId
            ]);
        }
    }
    
    /**
     * Recalculate invoice payment amount and status
     */
    private function recalculateInvoicePayment($invoiceId) {
        // Calculate total paid
        $stmt = $this->db->prepare("
            SELECT SUM(total_amount) as total_paid 
            FROM receipts 
            WHERE invoice_id = :invoice_id
        ");
        $stmt->execute(['invoice_id' => $invoiceId]);
        $result = $stmt->fetch();
        $totalPaid = $result['total_paid'] ?? 0;
        
        // Get invoice total
        $stmt = $this->db->prepare("SELECT total FROM invoices WHERE id = :id");
        $stmt->execute(['id' => $invoiceId]);
        $invoice = $stmt->fetch();
        
        if ($invoice) {
            $status = ($totalPaid >= $invoice['total']) ? 'paid' : 'issued';
            $stmt = $this->db->prepare("
                UPDATE invoices 
                SET paid_amount = :paid_amount, status = :status 
                WHERE id = :id
            ");
            $stmt->execute([
                'paid_amount' => $totalPaid,
                'status' => $status,
                'id' => $invoiceId
            ]);
        }
    }
    
    /**
     * Get receipts for an invoice
     */
    public function getByInvoice($invoiceId) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE invoice_id = :invoice_id 
            ORDER BY receipt_date DESC
        ");
        $stmt->execute(['invoice_id' => $invoiceId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get receipts with filter
     */
    public function getFiltered($dateFrom = null, $dateTo = null, $paymentMethod = null) {
        $sql = "SELECT r.*, i.invoice_number 
                FROM {$this->table} r 
                LEFT JOIN invoices i ON r.invoice_id = i.id 
                WHERE 1=1";
        $params = [];
        
        if ($dateFrom) {
            $sql .= " AND r.receipt_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND r.receipt_date <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        if ($paymentMethod) {
            $sql .= " AND r.payment_method = :payment_method";
            $params['payment_method'] = $paymentMethod;
        }
        
        $sql .= " ORDER BY r.receipt_date DESC, r.id DESC";
        
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