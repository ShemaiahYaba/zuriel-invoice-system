<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Customers</h2>
            <a href="<?php echo Config::url('customers/create'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Customer
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (empty($customers)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No customers found. <a href="<?php echo Config::url('customers/create'); ?>">Add your first customer</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Invoices</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($customer['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $customer['invoice_count'] ?? 0; ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo Config::url('customers/view/' . $customer['id']); ?>" 
                                                   class="btn btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo Config::url('customers/edit/' . $customer['id']); ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo Config::url('customers/delete/' . $customer['id']); ?>" 
                                                   class="btn btn-danger" title="Delete"
                                                   onclick="return confirm('Delete this customer?')">
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