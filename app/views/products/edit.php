<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-pencil-square"></i> Edit Product/Service</h2>
            <a href="<?php echo Config::url('products'); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?php echo Config::url('products/update/' . $product['id']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Product/Service Description *</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Enter product or service description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rate (<?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?>) *</label>
                        <input type="number" name="rate" class="form-control" 
                               step="0.01" min="0" placeholder="0.00" 
                               value="<?php echo htmlspecialchars($product['rate']); ?>" required>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo Config::url('products'); ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>