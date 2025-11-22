<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Customer.php';

/**
 * Customer Controller
 * Handles all customer CRUD operations
 */
class CustomerController extends Controller {
    private $customerModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->customerModel = new Customer($db);
    }
    
    /**
     * Display all customers
     */
    public function index() {
        $customers = $this->customerModel->getWithInvoiceCount();
        $this->view('customers/index', [
            'customers' => $customers,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Show create customer form
     */
    public function create() {
        $this->view('customers/create', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Store new customer
     */
    public function store() {
        $this->validateCsrfToken();
        
        $data = [
            'name' => $this->sanitize($this->input('name')),
            'address' => $this->sanitize($this->input('address')),
            'phone' => $this->sanitize($this->input('phone')),
            'email' => $this->sanitize($this->input('email'))
        ];
        
        // Validate
        $errors = $this->customerModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect('customers/create');
            return;
        }
        
        // Create customer
        try {
            $this->customerModel->create($data);
            $this->setFlash('success', 'Customer created successfully');
            $this->redirect('customers');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error creating customer: ' . $e->getMessage());
            $this->redirect('customers/create');
        }
    }
    
    /**
     * Show customer details
     */
    public function show($id) {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            $this->setFlash('danger', 'Customer not found');
            $this->redirect('customers');
            return;
        }
        
        $invoices = $this->customerModel->getInvoices($id);
        
        $this->view('customers/show', [
            'customer' => $customer,
            'invoices' => $invoices
        ]);
    }
    
    /**
     * Show edit customer form
     */
    public function edit($id) {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            $this->setFlash('danger', 'Customer not found');
            $this->redirect('customers');
            return;
        }
        
        $this->view('customers/edit', [
            'customer' => $customer,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Update customer
     */
    public function update($id) {
        $this->validateCsrfToken();
        
        $data = [
            'name' => $this->sanitize($this->input('name')),
            'address' => $this->sanitize($this->input('address')),
            'phone' => $this->sanitize($this->input('phone')),
            'email' => $this->sanitize($this->input('email'))
        ];
        
        // Validate
        $errors = $this->customerModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect("customers/edit/{$id}");
            return;
        }
        
        // Update customer
        try {
            $this->customerModel->update($id, $data);
            $this->setFlash('success', 'Customer updated successfully');
            $this->redirect('customers');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error updating customer: ' . $e->getMessage());
            $this->redirect("customers/edit/{$id}");
        }
    }
    
    /**
     * Delete customer
     */
    public function delete($id) {
        try {
            $this->customerModel->delete($id);
            $this->setFlash('success', 'Customer deleted successfully');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error deleting customer: ' . $e->getMessage());
        }
        
        $this->redirect('customers');
    }
    
    /**
     * Search customers (AJAX)
     */
    public function search() {
        $query = $this->query('q', '');
        $customers = $this->customerModel->search('name', $query);
        $this->json($customers);
    }
}
?>