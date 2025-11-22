<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';

/**
 * Product Controller
 * Handles all product CRUD operations
 */
class ProductController extends Controller {
    private $productModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->productModel = new Product($db);
    }
    
    /**
     * Display all products
     */
    public function index() {
        $products = $this->productModel->getAllOrdered();
        
        $this->view('products/index', [
            'products' => $products,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Show create product form
     */
    public function create() {
        $this->view('products/create', [
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Store new product
     */
    public function store() {
        $this->validateCsrfToken();
        
        $data = [
            'description' => $this->sanitize($this->input('description')),
            'rate' => $this->sanitize($this->input('rate'))
        ];
        
        // Validate
        $errors = $this->productModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect('products/create');
            return;
        }
        
        // Create product
        try {
            $this->productModel->create($data);
            $this->setFlash('success', 'Product created successfully');
            $this->redirect('products');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error creating product: ' . $e->getMessage());
            $this->redirect('products/create');
        }
    }
    
    /**
     * Show edit product form
     */
    public function edit($id) {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('danger', 'Product not found');
            $this->redirect('products');
            return;
        }
        
        $this->view('products/edit', [
            'product' => $product,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Update product
     */
    public function update($id) {
        $this->validateCsrfToken();
        
        $data = [
            'description' => $this->sanitize($this->input('description')),
            'rate' => $this->sanitize($this->input('rate'))
        ];
        
        // Validate
        $errors = $this->productModel->validate($data);
        if (!empty($errors)) {
            $this->setFlash('danger', implode('<br>', $errors));
            $this->redirect('products/edit/' . $id);
            return;
        }
        
        // Update product
        try {
            $this->productModel->update($id, $data);
            $this->setFlash('success', 'Product updated successfully');
            $this->redirect('products');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error updating product: ' . $e->getMessage());
            $this->redirect('products/edit/' . $id);
        }
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        try {
            $this->productModel->delete($id);
            $this->setFlash('success', 'Product deleted successfully');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error deleting product: ' . $e->getMessage());
        }
        
        $this->redirect('products');
    }
    
    /**
     * Search products (AJAX)
     */
    public function search() {
        $query = $this->query('q', '');
        $products = $this->productModel->searchByDescription($query);
        $this->json($products);
    }
}
?>