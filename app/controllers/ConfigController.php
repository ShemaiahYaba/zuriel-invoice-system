<?php
require_once __DIR__ . '/ProtectedController.php';

/**
 * Config Controller
 * Handles system configuration
 */
class ConfigController extends ProtectedController {
    
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
        
        $updateCount = 0;
        $errors = [];
        
        try {
            // Update each configuration value
            foreach ($configKeys as $key) {
                $value = $this->sanitize($this->input($key));
                if ($value !== null && $value !== '') {
                    $result = Config::update($this->db, $key, $value);
                    if ($result) {
                        $updateCount++;
                    } else {
                        $errors[] = "Failed to update {$key}";
                    }
                }
            }
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                $fileType = $_FILES['logo']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $this->setFlash('warning', 'Logo file type not supported. Please upload JPG, PNG, or GIF.');
                } else {
                    // Create directory if it doesn't exist
                    $uploadDir = __DIR__ . '/../../public/images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Use original extension
                    $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $fileName = 'logo.' . $extension;
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                        Config::update($this->db, 'COMPANY_LOGO', '/zuriel-invoice-system/public/images/' . $fileName);
                        $updateCount++;
                    } else {
                        $errors[] = "Failed to upload logo file";
                    }
                }
            }
            
            if ($updateCount > 0) {
                $this->setFlash('success', "Configuration updated successfully! ({$updateCount} settings changed)");
            } else if (empty($errors)) {
                $this->setFlash('info', 'No changes were made to the configuration.');
            }
            
            if (!empty($errors)) {
                $this->setFlash('danger', 'Some settings failed to update: ' . implode(', ', $errors));
            }
            
        } catch (Exception $e) {
            error_log("Configuration update error: " . $e->getMessage());
            $this->setFlash('danger', 'Error updating configuration: ' . $e->getMessage());
        }
        
        $this->redirect('settings');
    }
}
?>