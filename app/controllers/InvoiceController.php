<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Product.php';

/**
 * Invoice Controller
 * Handles all invoice CRUD operations
 */
class InvoiceController extends Controller {
    private $invoiceModel;
    private $customerModel;
    private $productModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->invoiceModel = new Invoice($db);
        $this->customerModel = new Customer($db);
        $this->productModel = new Product($db);
    }
    
    /**
     * Display all invoices
     */
    public function index() {
        $status = $this->query('status');
        $dateFrom = $this->query('date_from');
        $dateTo = $this->query('date_to');
        
        $invoices = $this->invoiceModel->getFiltered($status, $dateFrom, $dateTo);
        
        $this->view('invoices/index', [
            'invoices' => $invoices,
            'flash' => $this->getFlash(),
            'filters' => compact('status', 'dateFrom', 'dateTo')
        ]);
    }
    
    /**
     * Show create invoice form
     */
    public function create() {
        $customers = $this->customerModel->all();
        $products = $this->productModel->getAllOrdered();
        $invoiceNumber = $this->invoiceModel->generateInvoiceNumber();
        
        $this->view('invoices/create', [
            'customers' => $customers,
            'products' => $products,
            'invoice_number' => $invoiceNumber,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Store new invoice
     */
    public function store() {
        $this->validateCsrfToken();
        
        // Prepare invoice data
        $invoiceData = [
            'invoice_number' => $this->sanitize($this->input('invoice_number')),
            'customer_id' => $this->input('customer_id') ?: null,
            'customer_name' => $this->sanitize($this->input('customer_name')),
            'customer_address' => $this->sanitize($this->input('customer_address')),
            'invoice_date' => $this->sanitize($this->input('invoice_date')),
            'lpo_number' => $this->sanitize($this->input('lpo_number')),
            'invoice_type' => $this->sanitize($this->input('invoice_type', 'cash')),
            'subtotal' => 0,
            'total' => 0,
            'amount_in_words' => '',
            'status' => 'issued'
        ];
        
        // Prepare items
        $items = [];
        $quantities = $this->input('qty', []);
        $descriptions = $this->input('description', []);
        $rates = $this->input('rate', []);
        
        $total = 0;
        foreach ($quantities as $index => $qty) {
            if (!empty($qty) && !empty($descriptions[$index])) {
                $rate = floatval($rates[$index]);
                $amount = intval($qty) * $rate;
                $total += $amount;
                
                $items[] = [
                    'qty' => intval($qty),
                    'description' => $this->sanitize($descriptions[$index]),
                    'rate' => $rate,
                    'amount' => $amount
                ];
            }
        }
        
        $invoiceData['subtotal'] = $total;
        $invoiceData['total'] = $total;
        $invoiceData['amount_in_words'] = Invoice::numberToWords($total) . ' ' . Config::get('CURRENCY_NAME', 'Naira') . ' Only';
        
        // Create invoice
        try {
            $invoiceId = $this->invoiceModel->createWithItems($invoiceData, $items);
            $this->setFlash('success', 'Invoice created successfully');
            $this->redirect("invoices/view/{$invoiceId}");
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error creating invoice: ' . $e->getMessage());
            $this->redirect('invoices/create');
        }
    }
    
    /**
     * Show invoice details
     */
    public function show($id) {
        $invoice = $this->invoiceModel->getWithItems($id);
        
        if (!$invoice) {
            $this->setFlash('danger', 'Invoice not found');
            $this->redirect('invoices');
            return;
        }
        
        $this->view('invoices/show', [
            'invoice' => $invoice
        ]);
    }
    
    /**
     * Show edit invoice form
     */
    public function edit($id) {
        $invoice = $this->invoiceModel->getWithItems($id);
        
        if (!$invoice) {
            $this->setFlash('danger', 'Invoice not found');
            $this->redirect('invoices');
            return;
        }
        
        $customers = $this->customerModel->all();
        $products = $this->productModel->getAllOrdered();
        
        $this->view('invoices/edit', [
            'invoice' => $invoice,
            'customers' => $customers,
            'products' => $products,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Update invoice
     */
    public function update($id) {
        $this->validateCsrfToken();
        
        // Prepare invoice data
        $invoiceData = [
            'customer_id' => $this->input('customer_id') ?: null,
            'customer_name' => $this->sanitize($this->input('customer_name')),
            'customer_address' => $this->sanitize($this->input('customer_address')),
            'invoice_date' => $this->sanitize($this->input('invoice_date')),
            'lpo_number' => $this->sanitize($this->input('lpo_number')),
            'invoice_type' => $this->sanitize($this->input('invoice_type', 'cash')),
            'subtotal' => 0,
            'total' => 0,
            'amount_in_words' => ''
        ];
        
        // Prepare items
        $items = [];
        $quantities = $this->input('qty', []);
        $descriptions = $this->input('description', []);
        $rates = $this->input('rate', []);
        
        $total = 0;
        foreach ($quantities as $index => $qty) {
            if (!empty($qty) && !empty($descriptions[$index])) {
                $rate = floatval($rates[$index]);
                $amount = intval($qty) * $rate;
                $total += $amount;
                
                $items[] = [
                    'qty' => intval($qty),
                    'description' => $this->sanitize($descriptions[$index]),
                    'rate' => $rate,
                    'amount' => $amount
                ];
            }
        }
        
        $invoiceData['subtotal'] = $total;
        $invoiceData['total'] = $total;
        $invoiceData['amount_in_words'] = Invoice::numberToWords($total) . ' ' . Config::get('CURRENCY_NAME', 'Naira') . ' Only';
        
        // Update invoice
        try {
            $this->invoiceModel->updateWithItems($id, $invoiceData, $items);
            $this->setFlash('success', 'Invoice updated successfully');
            $this->redirect("invoices/view/{$id}");
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error updating invoice: ' . $e->getMessage());
            $this->redirect("invoices/edit/{$id}");
        }
    }
    
    /**
     * Delete/Archive invoice
     */
    public function delete($id) {
        try {
            // Archive instead of delete for audit trail
            $this->invoiceModel->update($id, ['status' => 'archived']);
            $this->setFlash('success', 'Invoice archived successfully');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error archiving invoice: ' . $e->getMessage());
        }
        
        $this->redirect('invoices');
    }
    
    /**
     * Print invoice
     */
    public function printInvoice($id) {
        $invoice = $this->invoiceModel->getWithItems($id);
        
        if (!$invoice) {
            $this->setFlash('danger', 'Invoice not found');
            $this->redirect('invoices');
            return;
        }
        
        $this->view('invoices/print', [
            'invoice' => $invoice
        ]);
    }
}
?>