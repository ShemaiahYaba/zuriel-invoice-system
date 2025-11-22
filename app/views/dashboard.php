<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Invoices</h6>
                        <h2 class="mb-0"><?php echo $stats['total_invoices'] ?? 0; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-file-earmark-text" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Receipts</h6>
                        <h2 class="mb-0"><?php echo $stats['total_receipts'] ?? 0; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-receipt" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Customers</h6>
                        <h2 class="mb-0"><?php echo $stats['total_customers'] ?? 0; ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Revenue</h6>
                        <h3 class="mb-0"><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h3>
                    </div>
                    <div>
                        <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Invoices</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_invoices)): ?>
                    <p class="text-muted">No recent invoices</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_invoices as $invoice): ?>
                            <a href="<?php echo Config::url('invoices/view/' . $invoice['id']); ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($invoice['customer_name']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($invoice['total'], 2); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo date('M d', strtotime($invoice['invoice_date'])); ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="<?php echo Config::url('invoices'); ?>" class="btn btn-sm btn-primary">
                    View All Invoices <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Receipts</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_receipts)): ?>
                    <p class="text-muted">No recent receipts</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_receipts as $receipt): ?>
                            <a href="<?php echo Config::url('receipts/view/' . $receipt['id']); ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($receipt['receipt_number']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($receipt['received_from']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($receipt['total_amount'], 2); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo date('M d', strtotime($receipt['receipt_date'])); ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="<?php echo Config::url('receipts'); ?>" class="btn btn-sm btn-success">
                    View All Receipts <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>