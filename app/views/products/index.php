<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-box"></i> Products & Services</h2>
            <a href="<?php echo Config::url('products/create'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Product
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No products found. <a href="<?php echo Config::url('products/create'); ?>">Add your first product</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 70%;">Description</th>
                                    <th style="width: 20%;">Rate</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                                        <td>
                                            <strong><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($product['rate'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo Config::url('products/edit/' . $product['id']); ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo Config::url('products/delete/' . $product['id']); ?>" 
                                                   class="btn btn-danger" title="Delete"
                                                   onclick="return confirm('Delete this product?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>