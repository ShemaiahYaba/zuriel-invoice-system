<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-text"></i> Invoices</h2>
            <a href="<?php echo Config::url('invoices/create'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Invoice
            </a>
        </div>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo Config::url('invoices'); ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="draft" <?php echo ($filters['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="issued" <?php echo ($filters['status'] ?? '') === 'issued' ? 'selected' : ''; ?>>Issued</option>
                            <option value="paid" <?php echo ($filters['status'] ?? '') === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $filters['dateFrom'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $filters['dateTo'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="<?php echo Config::url('invoices'); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Invoices Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($invoices)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No invoices found. <a href="<?php echo Config::url('invoices/create'); ?>">Create your first invoice</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
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
                                        <td>
                                            <strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
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
                                                <a href="<?php echo Config::url('invoices/edit/' . $invoice['id']); ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo Config::url('invoices/delete/' . $invoice['id']); ?>" 
                                                   class="btn btn-danger" title="Archive"
                                                   onclick="return confirm('Archive this invoice?')">
                                                    <i class="bi bi-archive"></i>
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