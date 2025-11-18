<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-plus"></i> Create New Invoice</h2>
            <a href="/invoices" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Invoices
            </a>
        </div>
        
        <form method="POST" action="<?php echo Config::url('invoices/store'); ?>" id="invoiceForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Invoice Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Invoice Number *</label>
                            <input type="text" name="invoice_number" class="form-control" 
                                   value="<?php echo $invoice_number; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Invoice Date *</label>
                            <input type="date" name="invoice_date" class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Invoice Type *</label>
                            <select name="invoice_type" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Customer (Optional)</label>
                            <select name="customer_id" id="customerSelect" class="form-select">
                                <option value="">-- Or enter manually below --</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                            data-address="<?php echo htmlspecialchars($customer['address']); ?>">
                                        <?php echo htmlspecialchars($customer['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">LPO Number</label>
                            <input type="text" name="lpo_number" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name *</label>
                            <input type="text" name="customer_name" id="customerName" 
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Address</label>
                            <textarea name="customer_address" id="customerAddress" 
                                      class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Invoice Items</h5>
                    <button type="button" class="btn btn-light btn-sm" onclick="addInvoiceRow()">
                        <i class="bi bi-plus-circle"></i> Add Row
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">QTY</th>
                                    <th>Description</th>
                                    <th style="width: 150px;">Rate</th>
                                    <th style="width: 150px;">Amount</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <input type="number" name="qty[]" class="form-control qty-input" 
                                               min="1" value="1" onchange="calculateRow(this)">
                                    </td>
                                    <td>
                                        <input type="text" name="description[]" class="form-control" 
                                               list="productsList" placeholder="Start typing...">
                                    </td>
                                    <td>
                                        <input type="number" name="rate[]" class="form-control rate-input" 
                                               step="0.01" min="0" value="0" onchange="calculateRow(this)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control amount-display" readonly value="0.00">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td>
                                        <input type="text" id="totalAmount" class="form-control fw-bold" readonly value="0.00">
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <datalist id="productsList">
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['description']); ?>" 
                                    data-rate="<?php echo $product['rate']; ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="/invoices" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-fill customer details
document.getElementById('customerSelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value) {
        document.getElementById('customerName').value = option.dataset.name || '';
        document.getElementById('customerAddress').value = option.dataset.address || '';
    }
});

// Calculate row amount
function calculateRow(input) {
    const row = input.closest('.item-row');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const amount = qty * rate;
    
    row.querySelector('.amount-display').value = amount.toFixed(2);
    calculateTotal();
}

// Calculate total
function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.amount-display').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalAmount').value = total.toFixed(2);
}

// Add new row
function addInvoiceRow() {
    const tbody = document.getElementById('itemsBody');
    const newRow = tbody.querySelector('.item-row').cloneNode(true);
    
    // Clear inputs
    newRow.querySelectorAll('input').forEach(input => {
        if (input.classList.contains('qty-input')) {
            input.value = '1';
        } else if (input.classList.contains('rate-input')) {
            input.value = '0';
        } else if (input.classList.contains('amount-display')) {
            input.value = '0.00';
        } else {
            input.value = '';
        }
    });
    
    tbody.appendChild(newRow);
}

// Remove row
function removeRow(button) {
    const tbody = document.getElementById('itemsBody');
    if (tbody.querySelectorAll('.item-row').length > 1) {
        button.closest('.item-row').remove();
        calculateTotal();
    } else {
        alert('At least one item is required');
    }
}

// Auto-fill rate when product is selected
document.getElementById('itemsBody').addEventListener('input', function(e) {
    if (e.target.name === 'description[]') {
        const desc = e.target.value;
        const option = document.querySelector(`#productsList option[value="${desc}"]`);
        if (option) {
            const rate = option.dataset.rate;
            if (rate) {
                const row = e.target.closest('.item-row');
                row.querySelector('.rate-input').value = rate;
                calculateRow(row.querySelector('.rate-input'));
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>