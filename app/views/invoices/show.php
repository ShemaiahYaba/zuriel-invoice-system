<?php 
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/Receipt.php';

// Get receipts for this invoice
$receiptModel = new Receipt($GLOBALS['db'] ?? $this->db ?? Database::getInstance()->getConnection());
$receipts = $receiptModel->getByInvoice($invoice['id']);
$totalPaid = array_sum(array_column($receipts, 'total_amount'));
$balance = $invoice['total'] - $totalPaid;
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h2>
            <div>
                <a href="<?php echo Config::url('invoices/print/' . $invoice['id']); ?>" class="btn btn-outline-secondary me-2" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="<?php echo Config::url('invoices/edit/' . $invoice['id']); ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>

        <!-- Invoice Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Bill To:</h5>
                        <address>
                            <strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($invoice['customer_address'])); ?>
                        </address>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <table class="table table-sm table-borderless float-md-end">
                            <tr>
                                <th class="text-start">Invoice #:</th>
                                <td class="text-end"><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                            </tr>
                            <tr>
                                <th class="text-start">Date:</th>
                                <td class="text-end"><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></td>
                            </tr>
                            <?php if (!empty($invoice['lpo_number'])): ?>
                            <tr>
                                <th class="text-start">LPO #:</th>
                                <td class="text-end"><?php echo htmlspecialchars($invoice['lpo_number']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th class="text-start">Status:</th>
                                <td class="text-end">
                                    <span class="badge bg-<?php 
                                        echo match($invoice['status']) {
                                            'draft' => 'secondary',
                                            'issued' => 'info',
                                            'paid' => 'success',
                                            'overdue' => 'warning',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst(htmlspecialchars($invoice['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Rate (<?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?>)</th>
                            <th class="text-end">Amount (<?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoice['items'] as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo nl2br(htmlspecialchars($item['description'])); ?></td>
                            <td class="text-end"><?php echo number_format($item['qty']); ?></td>
                            <td class="text-end"><?php echo number_format($item['rate'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($item['amount'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                            <td class="text-end fw-bold"><?php echo number_format($invoice['subtotal'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold"><?php echo number_format($invoice['total'], 2); ?></td>
                        </tr>
                        <?php if ($totalPaid > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end text-success">Paid:</td>
                            <td class="text-end text-success"><?php echo number_format($totalPaid, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end <?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?>">
                                <strong>Balance:</strong>
                            </td>
                            <td class="text-end <?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?>">
                                <strong><?php echo number_format($balance, 2); ?></strong>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="5" class="text-muted small">
                                <strong>Amount in words:</strong> <?php echo htmlspecialchars($invoice['amount_in_words']); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Receipts -->
        <?php if (!empty($receipts)): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Payment Receipts</h5>
                <span class="badge bg-light text-dark">
                    Total Paid: <?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($totalPaid, 2); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($receipt['receipt_number']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($receipt['receipt_date'])); ?></td>
                                <td><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?><?php echo number_format($receipt['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($receipt['payment_method']) {
                                            'cash' => 'success',
                                            'transfer' => 'primary',
                                            'pos' => 'info',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($receipt['payment_method']); ?>
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
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between">
            <a href="<?php echo Config::url('invoices'); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Invoices
            </a>
            <div>
                <?php if ($invoice['status'] !== 'paid' && $balance > 0): ?>
                <a href="<?php echo Config::url('receipts/create?invoice_id=' . $invoice['id']); ?>" class="btn btn-success">
                    <i class="bi bi-cash-coin"></i> Record Payment
                </a>
                <?php endif; ?>
                <a href="<?php echo Config::url('invoices/print/' . $invoice['id']); ?>" class="btn btn-primary" target="_blank">
                    <i class="bi bi-printer"></i> Print Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>