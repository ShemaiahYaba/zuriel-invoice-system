<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Receipts</h2>
            <a href="<?php echo Config::url('receipts/create'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Receipt
            </a>
        </div>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo Config::url('receipts'); ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $filters['dateFrom'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $filters['dateTo'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="cash" <?php echo ($filters['paymentMethod'] ?? '') === 'cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="transfer" <?php echo ($filters['paymentMethod'] ?? '') === 'transfer' ? 'selected' : ''; ?>>Transfer</option>
                            <option value="pos" <?php echo ($filters['paymentMethod'] ?? '') === 'pos' ? 'selected' : ''; ?>>POS</option>
                            <option value="other" <?php echo ($filters['paymentMethod'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="<?php echo Config::url('receipts'); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Receipts Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($receipts)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No receipts found. <a href="<?php echo Config::url('receipts/create'); ?>">Create your first receipt</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Date</th>
                                    <th>Received From</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($receipts as $receipt): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($receipt['receipt_number']); ?></strong>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($receipt['receipt_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($receipt['received_from']); ?></td>
                                        <td>
                                            <strong><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($receipt['total_amount'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $receipt['payment_method'] === 'cash' ? 'success' : 
                                                    ($receipt['payment_method'] === 'transfer' ? 'primary' : 
                                                    ($receipt['payment_method'] === 'pos' ? 'info' : 'secondary')); 
                                            ?>">
                                                <?php echo ucfirst($receipt['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $receipt['status'] === 'issued' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($receipt['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo Config::url('receipts/view/' . $receipt['id']); ?>" 
                                                   class="btn btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo Config::url('receipts/print/' . $receipt['id']); ?>" 
                                                   class="btn btn-secondary" title="Print" target="_blank">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <a href="<?php echo Config::url('receipts/edit/' . $receipt['id']); ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo Config::url('receipts/delete/' . $receipt['id']); ?>" 
                                                   class="btn btn-danger" title="Archive"
                                                   onclick="return confirm('Archive this receipt?')">
                                                    <i class="bi bi-archive"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($receipts)): ?>
                    <div class="mt-3">
                        <div class="alert alert-light">
                            <strong>Total Receipts:</strong> <?php echo count($receipts); ?>
                            <?php 
                            $totalAmount = array_sum(array_column($receipts, 'total_amount'));
                            ?>
                            &nbsp;|&nbsp;
                            <strong>Total Amount:</strong> <?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($totalAmount, 2); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>