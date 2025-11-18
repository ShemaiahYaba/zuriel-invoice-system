<?php
/**
 * Base Model Class
 * All models extend from this class
 */

class Model {
    protected $db;
    protected $table;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all records
     */
    public function all($orderBy = 'id', $order = 'DESC') {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Find record by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new record
     */
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Search records
     */
    public function search($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} LIKE :value");
        $stmt->execute(['value' => "%{$value}%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Count records
     */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>