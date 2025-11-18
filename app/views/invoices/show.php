<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h2>
            <div>
                <a href="<?php echo Config::url('invoices/print/' . $invoice['id']); ?>" class="btn btn-outline-secondary me-2">
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
                            <?php echo nl2br(htmlspecialchars($invoice['customer_address'])); ?><br>
                            <?php if (!empty($invoice['customer_phone'])): ?>
                                <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($invoice['customer_phone']); ?><br>
                            <?php endif; ?>
                            <?php if (!empty($invoice['customer_email'])): ?>
                                <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($invoice['customer_email']); ?>
                            <?php endif; ?>
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
                        <tr>
                            <td colspan="5" class="text-muted small">
                                <strong>Amount in words:</strong> <?php echo htmlspecialchars($invoice['amount_in_words']); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Payment Method:</h6>
                        <p class="mb-0">
                            <?php 
                            $paymentMethod = $invoice['invoice_type'] ?? 'cash';
                            echo match($paymentMethod) {
                                'cash' => '<i class="bi bi-cash-coin"></i> Cash',
                                'transfer' => '<i class="bi bi-bank"></i> Bank Transfer',
                                'card' => '<i class="bi bi-credit-card"></i> Credit/Debit Card',
                                'cheque' => '<i class="bi bi-file-earmark-text"></i> Cheque',
                                default => ucfirst($paymentMethod)
                            };
                            ?>
                        </p>
                    </div>
                    <?php if (!empty($invoice['notes'])): ?>
                    <div class="col-md-6">
                        <h6>Notes:</h6>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?php echo Config::url('invoices'); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Invoices
            </a>
            <div>
                <?php if ($invoice['status'] !== 'paid'): ?>
                <a href="<?php echo Config::url('receipts/create?invoice_id=' . $invoice['id']); ?>" class="btn btn-success">
                    <i class="bi bi-cash-coin"></i> Record Payment
                </a>
                <?php endif; ?>
                <a href="<?php echo Config::url('invoices/print/' . $invoice['id']); ?>" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
