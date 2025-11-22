<?php
require_once __DIR__ . '/Model.php';

/**
 * User Model
 * Handles user authentication and management
 */
class User extends Model {
    protected $table = 'users';
    
    /**
     * Find user by username
     */
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Hash password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Create new user
     */
    public function createUser($data) {
        // Hash password before storing
        $data['password'] = $this->hashPassword($data['password']);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (username, email, password, full_name, role, status)
            VALUES (:username, :email, :password, :full_name, :role, :status)
        ");
        
        return $stmt->execute($data);
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin($userId) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET last_login = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $userId]);
    }
    
    /**
     * Get active users only
     */
    public function getActiveUsers() {
        $stmt = $this->db->prepare("
            SELECT id, username, email, full_name, role, last_login, created_at 
            FROM {$this->table} 
            WHERE status = 'active' 
            ORDER BY full_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        $params = ['username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Validate user data
     */
    public function validate($data, $isUpdate = false, $userId = null) {
        $errors = [];
        
        // Username validation
        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        } elseif ($this->usernameExists($data['username'], $userId)) {
            $errors[] = 'Username already exists';
        }
        
        // Email validation
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        } elseif ($this->emailExists($data['email'], $userId)) {
            $errors[] = 'Email already exists';
        }
        
        // Password validation (only for new users or if password is being changed)
        if (!$isUpdate || !empty($data['password'])) {
            if (empty($data['password'])) {
                $errors[] = 'Password is required';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }
            
            // Confirm password
            if (isset($data['confirm_password']) && $data['password'] !== $data['confirm_password']) {
                $errors[] = 'Passwords do not match';
            }
        }
        
        // Full name validation
        if (empty($data['full_name'])) {
            $errors[] = 'Full name is required';
        }
        
        // Role validation
        if (!in_array($data['role'], ['admin', 'manager', 'staff'])) {
            $errors[] = 'Invalid role selected';
        }
        
        return $errors;
    }
    
    /**
     * Record login attempt
     */
    public function recordLoginAttempt($username, $ipAddress, $success) {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (username, ip_address, success)
            VALUES (:username, :ip_address, :success)
        ");
        
        return $stmt->execute([
            'username' => $username,
            'ip_address' => $ipAddress,
            'success' => $success ? 1 : 0
        ]);
    }
    
    /**
     * Get failed login attempts in last X minutes
     */
    public function getFailedAttempts($username, $ipAddress, $minutes = 15) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM login_attempts 
            WHERE username = :username 
            AND ip_address = :ip_address 
            AND success = 0 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
        ");
        
        $stmt->execute([
            'username' => $username,
            'ip_address' => $ipAddress,
            'minutes' => $minutes
        ]);
        
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Change user password
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = $this->hashPassword($newPassword);
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET password = :password 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }
    
    /**
     * Update user status
     */
    public function updateStatus($userId, $status) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = :status 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'status' => $status,
            'id' => $userId
        ]);
    }
}
?>