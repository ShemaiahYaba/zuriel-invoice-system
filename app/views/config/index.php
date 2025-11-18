<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-gear"></i> System Configuration</h2>
        <p class="text-muted">Configure company information and system settings</p>
        
        <form method="POST" action="/config/update" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <!-- Company Information -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Company Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="COMPANY_NAME" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['COMPANY_NAME'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="COMPANY_TAGLINE" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['COMPANY_TAGLINE'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="COMPANY_ADDRESS" class="form-control" rows="2"><?php echo htmlspecialchars($config['COMPANY_ADDRESS'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone 1</label>
                            <input type="text" name="COMPANY_PHONE_1" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['COMPANY_PHONE_1'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone 2</label>
                            <input type="text" name="COMPANY_PHONE_2" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['COMPANY_PHONE_2'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="COMPANY_EMAIL" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['COMPANY_EMAIL'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Company Logo</label>
                        <?php if (!empty($config['COMPANY_LOGO'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($config['COMPANY_LOGO']); ?>" 
                                     alt="Logo" style="max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">PNG format recommended. Max 2MB.</small>
                    </div>
                </div>
            </div>
            
            <!-- Numbering -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Invoice & Receipt Numbering</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Prefix</label>
                            <input type="text" name="INVOICE_PREFIX" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['INVOICE_PREFIX'] ?? 'INV'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Start Number</label>
                            <input type="number" name="INVOICE_START_NUMBER" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['INVOICE_START_NUMBER'] ?? '1'); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Receipt Prefix</label>
                            <input type="text" name="RECEIPT_PREFIX" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['RECEIPT_PREFIX'] ?? 'RCP'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Receipt Start Number</label>
                            <input type="number" name="RECEIPT_START_NUMBER" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['RECEIPT_START_NUMBER'] ?? '1'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Appearance -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Appearance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="PRIMARY_COLOR" class="form-control form-control-color" 
                                   value="<?php echo htmlspecialchars($config['PRIMARY_COLOR'] ?? '#0066CC'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Header Background Color</label>
                            <input type="color" name="HEADER_BG_COLOR" class="form-control form-control-color" 
                                   value="<?php echo htmlspecialchars($config['HEADER_BG_COLOR'] ?? '#0066CC'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Currency -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Currency Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Currency Name</label>
                            <input type="text" name="CURRENCY_NAME" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['CURRENCY_NAME'] ?? 'Naira'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Major Symbol (₦)</label>
                            <input type="text" name="CURRENCY_SYMBOL_MAJOR" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['CURRENCY_SYMBOL_MAJOR'] ?? '₦'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minor Symbol (K)</label>
                            <input type="text" name="CURRENCY_SYMBOL_MINOR" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['CURRENCY_SYMBOL_MINOR'] ?? 'K'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> Save Configuration
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>