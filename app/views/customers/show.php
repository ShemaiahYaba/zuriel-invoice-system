<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person"></i> Customer Details</h2>
            <div>
                <a href="<?php echo Config::url('customers/edit/' . $customer['id']); ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="<?php echo Config::url('customers'); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        
        <!-- Customer Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="bi bi-person-fill"></i> Name:</strong><br>
                        <p class="ms-4"><?php echo htmlspecialchars($customer['name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="bi bi-telephone-fill"></i> Phone:</strong><br>
                        <p class="ms-4"><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="bi bi-envelope-fill"></i> Email:</strong><br>
                        <p class="ms-4"><?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong><i class="bi bi-geo-alt-fill"></i> Address:</strong><br>
                        <p class="ms-4"><?php echo nl2br(htmlspecialchars($customer['address'] ?? 'N/A')); ?></p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-calendar-plus"></i> Created: <?php echo date('d M Y', strtotime($customer['created_at'])); ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="bi bi-calendar-check"></i> Last Updated: <?php echo date('d M Y', strtotime($customer['updated_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer Invoices -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Invoices</h5>
                <a href="<?php echo Config::url('invoices/create'); ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle"></i> New Invoice
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($invoices)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No invoices found for this customer.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $invoice): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong></td>
                                        <td><?php echo date('d M Y', strtotime($invoice['invoice_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $invoice['invoice_type'] === 'cash' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($invoice['invoice_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($invoice['total'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $invoice['status'] === 'paid' ? 'success' : 
                                                    ($invoice['status'] === 'issued' ? 'primary' : 
                                                    ($invoice['status'] === 'draft' ? 'secondary' : 'dark')); 
                                            ?>">
                                                <?php echo ucfirst($invoice['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo Config::url('invoices/view/' . $invoice['id']); ?>" 
                                                   class="btn btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo Config::url('invoices/print/' . $invoice['id']); ?>" 
                                                   class="btn btn-secondary" title="Print" target="_blank">
                                                    <i class="bi bi-printer"></i>
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