<?php
require_once __DIR__ . '/Controller.php';

/**
 * Config Controller
 * Handles system configuration
 */
class ConfigController extends Controller {
    
    /**
     * Show configuration page
     */
    public function index() {
        $config = Config::all();
        
        $this->view('settings/index', [
            'config' => $config,
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * Update configuration
     */
    public function update() {
        $this->validateCsrfToken();
        
        // List of configurable items
        $configKeys = [
            'COMPANY_NAME',
            'COMPANY_TAGLINE',
            'COMPANY_ADDRESS',
            'COMPANY_PHONE_1',
            'COMPANY_PHONE_2',
            'COMPANY_EMAIL',
            'INVOICE_PREFIX',
            'INVOICE_START_NUMBER',
            'RECEIPT_PREFIX',
            'RECEIPT_START_NUMBER',
            'PRIMARY_COLOR',
            'HEADER_BG_COLOR',
            'CURRENCY_SYMBOL_MAJOR',
            'CURRENCY_SYMBOL_MINOR',
            'CURRENCY_NAME'
        ];
        
        try {
            foreach ($configKeys as $key) {
                $value = $this->sanitize($this->input($key));
                if ($value !== null) {
                    Config::update($this->db, $key, $value);
                }
            }
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/images/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = 'logo.png';
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                    Config::update($this->db, 'COMPANY_LOGO', '/images/' . $fileName);
                }
            }
            
            $this->setFlash('success', 'Configuration updated successfully');
        } catch (Exception $e) {
            $this->setFlash('danger', 'Error updating configuration: ' . $e->getMessage());
        }
        
        $this->redirect('settings');
    }
}
?>