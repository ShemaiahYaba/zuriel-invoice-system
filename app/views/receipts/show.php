<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Receipt Details</h2>
            <div>
                <a href="<?php echo Config::url('receipts/print/' . $receipt['id']); ?>" 
                   class="btn btn-secondary" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="<?php echo Config::url('receipts/edit/' . $receipt['id']); ?>" 
                   class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="<?php echo Config::url('receipts'); ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Receipt #<?php echo htmlspecialchars($receipt['receipt_number']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date:</strong><br>
                        <?php echo date('d F Y', strtotime($receipt['receipt_date'])); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        <span class="badge bg-<?php echo $receipt['status'] === 'issued' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($receipt['status']); ?>
                        </span>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Received From:</strong><br>
                        <?php echo htmlspecialchars($receipt['received_from']); ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Amount:</strong><br>
                        <h4 class="text-primary">
                            <?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($receipt['total_amount'], 2); ?>
                        </h4>
                        <small class="text-muted">
                            (<?php echo $receipt['amount_naira']; ?> <?php echo Config::get('CURRENCY_NAME', 'Naira'); ?> 
                            <?php if ($receipt['amount_kobo'] > 0): ?>
                                and <?php echo $receipt['amount_kobo']; ?> <?php echo Config::get('CURRENCY_SYMBOL_MINOR', 'Kobo'); ?>
                            <?php endif; ?>)
                        </small>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong><br>
                        <span class="badge bg-<?php 
                            echo $receipt['payment_method'] === 'cash' ? 'success' : 
                                ($receipt['payment_method'] === 'transfer' ? 'primary' : 
                                ($receipt['payment_method'] === 'pos' ? 'info' : 'secondary')); 
                        ?>">
                            <?php echo ucfirst($receipt['payment_method']); ?>
                        </span>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Payment For:</strong><br>
                        <p><?php echo nl2br(htmlspecialchars($receipt['payment_for'])); ?></p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Created: <?php echo date('d M Y H:i', strtotime($receipt['created_at'])); ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            Last Updated: <?php echo date('d M Y H:i', strtotime($receipt['updated_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3 text-center">
            <a href="<?php echo Config::url('receipts/print/' . $receipt['id']); ?>" 
               class="btn btn-lg btn-secondary" target="_blank">
                <i class="bi bi-printer"></i> Print Receipt
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>