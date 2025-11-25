<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt-cutoff"></i> Create New Receipt</h2>
            <a href="<?php echo Config::url('receipts'); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Receipts
            </a>
        </div>
        
        <form method="POST" action="<?php echo Config::url('receipts/store'); ?>" id="receiptForm">
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
                            <label class="form-label">Link to Invoice (Optional)</label>
                            <select name="invoice_id" id="invoiceSelect" class="form-select">
                                <option value="">-- Select Invoice (Optional) --</option>
                                <?php if (!empty($unpaid_invoices)): ?>
                                    <?php foreach ($unpaid_invoices as $inv): ?>
                                        <option value="<?php echo $inv['id']; ?>"
                                                data-customer="<?php echo htmlspecialchars($inv['customer_name']); ?>"
                                                data-amount="<?php echo $inv['total']; ?>"
                                                data-paid="<?php echo $inv['paid_amount'] ?? 0; ?>"
                                                <?php echo (!empty($invoice) && $invoice['id'] == $inv['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($inv['invoice_number']); ?> - 
                                            <?php echo htmlspecialchars($inv['customer_name']); ?> - 
                                            <?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($inv['total'], 2); ?>
                                            <?php if (!empty($inv['paid_amount'])): ?>
                                                (Paid: <?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($inv['paid_amount'], 2); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Select an invoice to link this receipt to a specific invoice</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Received From *</label>
                            <input type="text" name="received_from" id="receivedFrom" class="form-control" 
                                   placeholder="Customer Name" 
                                   value="<?php echo !empty($invoice) ? htmlspecialchars($invoice['customer_name']) : ''; ?>" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (<?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?>) *</label>
                            <input type="number" name="total_amount" id="totalAmount" class="form-control" 
                                   step="0.01" min="0" placeholder="0.00" 
                                   value="<?php echo !empty($invoice) ? $invoice['total'] : ''; ?>" 
                                   required>
                            <small class="text-muted">Enter amount in decimal format (e.g., 5000.50)</small>
                            <div id="balanceInfo" class="mt-2" style="display:none;">
                                <span class="badge bg-info">Balance: <span id="balanceAmount">0.00</span></span>
                            </div>
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
                            <textarea name="payment_for" id="paymentFor" class="form-control" rows="3" 
                                      placeholder="Describe what this payment is for..." required><?php 
                                echo !empty($invoice) ? 'Payment for Invoice ' . htmlspecialchars($invoice['invoice_number']) : ''; 
                            ?></textarea>
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

<script>
// Auto-fill from selected invoice
document.getElementById('invoiceSelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    
    if (option.value) {
        const customer = option.dataset.customer;
        const amount = parseFloat(option.dataset.amount);
        const paid = parseFloat(option.dataset.paid) || 0;
        const balance = amount - paid;
        
        document.getElementById('receivedFrom').value = customer;
        document.getElementById('totalAmount').value = balance.toFixed(2);
        document.getElementById('paymentFor').value = 'Payment for Invoice ' + option.text.split(' - ')[0];
        
        // Show balance info
        const balanceInfo = document.getElementById('balanceInfo');
        const balanceAmount = document.getElementById('balanceAmount');
        balanceInfo.style.display = 'block';
        balanceAmount.textContent = '<?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?>' + balance.toFixed(2);
    } else {
        document.getElementById('receivedFrom').value = '';
        document.getElementById('totalAmount').value = '';
        document.getElementById('paymentFor').value = '';
        document.getElementById('balanceInfo').style.display = 'none';
    }
});

// Trigger change if invoice is pre-selected
if (document.getElementById('invoiceSelect').value) {
    document.getElementById('invoiceSelect').dispatchEvent(new Event('change'));
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>