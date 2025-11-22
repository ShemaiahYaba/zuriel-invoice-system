<?php
require_once __DIR__ . '/ProtectedController.php';
require_once __DIR__ . '/../models/Receipt.php';

/**
 * Receipt Controller
 * Handles all receipt CRUD operations
 */
class ReceiptController extends ProtectedController {
    private $receiptModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->receiptModel = new Receipt($db);
    }
    
    /**
     * Display all receipts
     */
    public function index() {
        $dateFrom = $this->query('date_from');
        $dateTo = $this->query('date_to');
        $paymentMethod = $this->query('payment_method');
        
        $receipts = $this->receiptModel->getFiltered($dateFrom, $dateTo, $paymentMethod);
        
        $this->view('receipts/index', [
            'receipts' => $receipts,
            'flash' => $this->getFlash(),
            'filters' => compact('dateFrom', 'dateTo', 'paymentMethod')
        ]);
    }
    
    /**
     * Show create receipt form
     */
    public function create() {
        $receiptNumber = $this->receiptModel->generateReceiptNumber();
        
        $this->view('receipts/create', [
            'receipt_number' => $receiptNumber,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Store new receipt
     */
    public function store() {
        $this->validateCsrfToken();
        
        // Get amount
        $totalAmount = floatval($this->input('total_amount', 0));
        $nairaKobo = Receipt::convertToNairaKobo($totalAmount);
        
        $data = [
            'receipt_number' => $this->sanitize($this->input('receipt_number')),
            'receipt_date' => $this->sanitize($this->input('receipt_date')),
            'received_from' => $this->sanitize($this->input('received_from')),
            'amount_naira' => $nairaKobo['naira'],
            'amount_kobo' => $nairaKobo['kobo'],
            'total_amount' => $totalAmount,
            'payment_for' => $this->sanitize($this->input('payment_for')),
            'payment_method' => $this->sanitize($this->input('payment_method')),
            'status' => 'issued'
        ];
        
        // Validate
        $errors = $this->receiptModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect('receipts/create');
            return;
        }
        
        // Create receipt
        try {
            $receiptId = $this->receiptModel->create($data);
            $this->setFlash('success', 'Receipt created successfully');
            $this->redirect('receipts/view/' . $receiptId);
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error creating receipt: ' . $e->getMessage());
            $this->redirect('receipts/create');
        }
    }
    
    /**
     * Show receipt details
     */
    public function show($id) {
        $receipt = $this->receiptModel->find($id);
        
        if (!$receipt) {
            $this->setFlash('danger', 'Receipt not found');
            $this->redirect('receipts');
            return;
        }
        
        $this->view('receipts/show', [
            'receipt' => $receipt
        ]);
    }
    
    /**
     * Show edit receipt form
     */
    public function edit($id) {
        $receipt = $this->receiptModel->find($id);
        
        if (!$receipt) {
            $this->setFlash('danger', 'Receipt not found');
            $this->redirect('receipts');
            return;
        }
        
        $this->view('receipts/edit', [
            'receipt' => $receipt,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Update receipt
     */
    public function update($id) {
        $this->validateCsrfToken();
        
        // Get amount
        $totalAmount = floatval($this->input('total_amount', 0));
        $nairaKobo = Receipt::convertToNairaKobo($totalAmount);
        
        $data = [
            'receipt_date' => $this->sanitize($this->input('receipt_date')),
            'received_from' => $this->sanitize($this->input('received_from')),
            'amount_naira' => $nairaKobo['naira'],
            'amount_kobo' => $nairaKobo['kobo'],
            'total_amount' => $totalAmount,
            'payment_for' => $this->sanitize($this->input('payment_for')),
            'payment_method' => $this->sanitize($this->input('payment_method'))
        ];
        
        // Validate
        $errors = $this->receiptModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect('receipts/edit/' . $id);
            return;
        }
        
        // Update receipt
        try {
            $this->receiptModel->update($id, $data);
            $this->setFlash('success', 'Receipt updated successfully');
            $this->redirect('receipts/view/' . $id);
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error updating receipt: ' . $e->getMessage());
            $this->redirect('receipts/edit/' . $id);
        }
    }
    
    /**
     * Delete/Archive receipt
     */
    public function delete($id) {
        try {
            // Archive instead of delete for audit trail
            $this->receiptModel->update($id, ['status' => 'archived']);
            $this->setFlash('success', 'Receipt archived successfully');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error archiving receipt: ' . $e->getMessage());
        }
        
        $this->redirect('receipts');
    }
    
    /**
     * Print receipt
     */
    public function printReceipt($id) {
        require_once __DIR__ . '/../models/Invoice.php'; // Load Invoice for numberToWords
        
        $receipt = $this->receiptModel->find($id);
        
        if (!$receipt) {
            $this->setFlash('danger', 'Receipt not found');
            $this->redirect('receipts');
            return;
        }
        
        $this->view('receipts/print', [
            'receipt' => $receipt
        ]);
    }
}
?>