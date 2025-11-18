<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt-cutoff"></i> Create New Receipt</h2>
            <a href="<?php echo Config::url('receipts'); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Receipts
            </a>
        </div>
        
        <form method="POST" action="<?php echo Config::url('receipts/store'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Receipt Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Receipt Number *</label>
                            <input type="text" name="receipt_number" class="form-control" 
                                   value="<?php echo $receipt_number; ?>" required readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Receipt Date *</label>
                            <input type="date" name="receipt_date" class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Received From *</label>
                            <input type="text" name="received_from" class="form-control" 
                                   placeholder="Customer Name" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (<?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?>) *</label>
                            <input type="number" name="total_amount" class="form-control" 
                                   step="0.01" min="0" placeholder="0.00" required>
                            <small class="text-muted">Enter amount in decimal format (e.g., 5000.50)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Method *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">-- Select Method --</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="pos">POS</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Payment For (Purpose) *</label>
                            <textarea name="payment_for" class="form-control" rows="3" 
                                      placeholder="Describe what this payment is for..." required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo Config::url('receipts'); ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Create Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>