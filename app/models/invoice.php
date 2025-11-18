<?php
require_once __DIR__ . '/Model.php';

/**
 * Invoice Model
 */
class Invoice extends Model {
    protected $table = 'invoices';
    
    /**
     * Get invoice with items
     */
    public function getWithItems($id) {
        $invoice = $this->find($id);
        if ($invoice) {
            $invoice['items'] = $this->getItems($id);
        }
        return $invoice;
    }
    
    /**
     * Get invoice items
     */
    public function getItems($invoiceId) {
        $stmt = $this->db->prepare("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id");
        $stmt->execute(['invoice_id' => $invoiceId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create invoice with items
     */
    public function createWithItems($invoiceData, $items) {
        try {
            $this->db->beginTransaction();
            
            // Create invoice
            $invoiceId = $this->create($invoiceData);
            
            // Create items
            foreach ($items as $item) {
                $item['invoice_id'] = $invoiceId;
                $this->createItem($item);
            }
            
            $this->db->commit();
            return $invoiceId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update invoice with items
     */
    public function updateWithItems($id, $invoiceData, $items) {
        try {
            $this->db->beginTransaction();
            
            // Update invoice
            $this->update($id, $invoiceData);
            
            // Delete old items
            $this->deleteItems($id);
            
            // Create new items
            foreach ($items as $item) {
                $item['invoice_id'] = $id;
                $this->createItem($item);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Create invoice item
     */
    private function createItem($data) {
        $stmt = $this->db->prepare("
            INSERT INTO invoice_items (invoice_id, qty, description, rate, amount)
            VALUES (:invoice_id, :qty, :description, :rate, :amount)
        ");
        return $stmt->execute($data);
    }
    
    /**
     * Delete invoice items
     */
    private function deleteItems($invoiceId) {
        $stmt = $this->db->prepare("DELETE FROM invoice_items WHERE invoice_id = :invoice_id");
        return $stmt->execute(['invoice_id' => $invoiceId]);
    }
    
    /**
     * Generate next invoice number
     */
    public function generateInvoiceNumber() {
        $prefix = Config::get('INVOICE_PREFIX', 'INV');
        $startNumber = Config::get('INVOICE_START_NUMBER', 1);
        
        // Get last invoice number
        $stmt = $this->db->query("SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1");
        $lastInvoice = $stmt->fetch();
        
        if ($lastInvoice) {
            // Extract number from last invoice
            $lastNumber = preg_replace('/[^0-9]/', '', $lastInvoice['invoice_number']);
            $nextNumber = intval($lastNumber) + 1;
        } else {
            $nextNumber = $startNumber;
        }
        
        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get invoices with filter
     */
    public function getFiltered($status = null, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        
        if ($dateFrom) {
            $sql .= " AND invoice_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND invoice_date <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        $sql .= " ORDER BY invoice_date DESC, id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Convert number to words (for amount in words)
     */
    public static function numberToWords($number) {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        
        if ($number == 0) return 'Zero';
        
        $words = '';
        
        // Billions
        if ($number >= 1000000000) {
            $words .= self::numberToWords(floor($number / 1000000000)) . ' Billion ';
            $number %= 1000000000;
        }
        
        // Millions
        if ($number >= 1000000) {
            $words .= self::numberToWords(floor($number / 1000000)) . ' Million ';
            $number %= 1000000;
        }
        
        // Thousands
        if ($number >= 1000) {
            $words .= self::numberToWords(floor($number / 1000)) . ' Thousand ';
            $number %= 1000;
        }
        
        // Hundreds
        if ($number >= 100) {
            $words .= $ones[floor($number / 100)] . ' Hundred ';
            $number %= 100;
        }
        
        // Tens and ones
        if ($number >= 20) {
            $words .= $tens[floor($number / 10)] . ' ';
            $number %= 10;
        } elseif ($number >= 10) {
            $words .= $teens[$number - 10] . ' ';
            return trim($words);
        }
        
        if ($number > 0) {
            $words .= $ones[$number] . ' ';
        }
        
        return trim($words);
    }
}
?>