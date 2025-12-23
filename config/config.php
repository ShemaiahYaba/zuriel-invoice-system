<?php
/**
 * Central Configuration File
 * All dynamic values should be defined here
 * Never hardcode values in the application code
 */

class Config {
    private static $config = [];
    private static $loaded = false;
    
    /**
     * Load configuration from database
     */
    public static function load($db) {
        if (self::$loaded) {
            return;
        }
        
        try {
            $stmt = $db->query("SELECT config_key, config_value FROM config");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                self::$config[$row['config_key']] = $row['config_value'];
            }
            self::$loaded = true;
        } catch (PDOException $e) {
            // If config table doesn't exist, use defaults
            self::loadDefaults();
        }
    }
    
    /**
     * Load default configuration
     */
    private static function loadDefaults() {
        self::$config = [
            'COMPANY_NAME' => 'ZURIEL TECH VENTURES',
            'COMPANY_TAGLINE' => 'Innovating Tomorrow, Today',
            'COMPANY_LOGO' => '/images/logo.png',
            'COMPANY_ADDRESS' => 'Shop 1-041, Area 1 Shopping Plaza, Garki Abuja',
            'COMPANY_PHONE_1' => '+234 (0) 908 444 4240',
            'COMPANY_PHONE_2' => '+234 (0) 908 444 4140',
            'COMPANY_EMAIL' => 'zurieltechventures@gmail.com',
            'INVOICE_PREFIX' => 'INV',
            'INVOICE_START_NUMBER' => '1',
            'RECEIPT_PREFIX' => 'RCP',
            'RECEIPT_START_NUMBER' => '1',
            'PRIMARY_COLOR' => '#0066CC',
            'HEADER_BG_COLOR' => '#0066CC',
            'CURRENCY_SYMBOL_MAJOR' => '₦',
            'CURRENCY_SYMBOL_MINOR' => 'K',
            'CURRENCY_NAME' => 'Naira',
        ];
        self::$loaded = true;
    }
    
    /**
     * Get configuration value
     */
    public static function get($key, $default = null) {
        return self::$config[$key] ?? $default;
    }
    
    /**
     * Set configuration value
     */
    public static function set($key, $value) {
        self::$config[$key] = $value;
    }
    
    /**
     * Get all configuration
     */
    public static function all() {
        return self::$config;
    }
    
    /**
     * Update configuration in database
     */
    public static function update($db, $key, $value) {
        try {
            // First try to update
            $stmt = $db->prepare("
                UPDATE config 
                SET config_value = :value, updated_at = CURRENT_TIMESTAMP 
                WHERE config_key = :key
            ");
            $stmt->execute([
                'key' => $key,
                'value' => $value
            ]);
            
            // If no rows affected, insert
            if ($stmt->rowCount() === 0) {
                $stmt = $db->prepare("
                    INSERT INTO config (config_key, config_value) 
                    VALUES (:key, :value)
                ");
                $stmt->execute([
                    'key' => $key,
                    'value' => $value
                ]);
            }
            
            // Update in-memory config
            self::$config[$key] = $value;
            return true;
        } catch (PDOException $e) {
            error_log("Config update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate URL with base path
     */
    public static function url($path = '') {
        // Get base URL from config or use default
        $baseUrl = rtrim(self::get('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/zuriel-invoice-system'), '/');
        
        // Clean up the path
        $path = ltrim($path, '/');
        
        // Return full URL
        return $baseUrl . ($path ? '/' . $path : '');
    }
}
?>