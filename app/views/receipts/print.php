<?php
date_default_timezone_set('Africa/Lagos');
require_once __DIR__ . '/../../models/Invoice.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt <?php echo htmlspecialchars($receipt['receipt_number']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20mm;
            background: white;
        }
        
        .receipt-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            border: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            padding: 20px;
        }
        
        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .logo-section {
            flex: 1;
        }
        
        .logo-section img {
            height: 80px;
            margin-bottom: 5px;
        }
        
        .company-name {
            font-size: 36px;
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            margin: 0;
            line-height: 1;
        }
        
        .tagline {
            font-size: 16px;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            font-style: italic;
            margin-top: 3px;
        }
        
        .contact-box {
            background: <?php echo Config::get('HEADER_BG_COLOR', '#0066CC'); ?>;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 13px;
            line-height: 2;
            max-width: 400px;
        }
        
        /* Receipt Details */
        .receipt-header {
            margin-bottom: 30px;
        }
        
        .date-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .date-label {
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            margin-right: 15px;
        }
        
        .date-value {
            border-bottom: 2px solid #333;
            flex: 1;
            padding-bottom: 3px;
        }
        
        .receipt-badge {
            display: inline-block;
            background: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            color: white;
            padding: 12px 40px;
            border-radius: 5px;
            font-size: 20px;
            font-weight: bold;
            font-style: italic;
            margin-right: 20px;
        }
        
        .receipt-number {
            display: inline-block;
            font-size: 24px;
            font-weight: bold;
        }
        
        /* Form Fields */
        .form-section {
            margin-bottom: 25px;
        }
        
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .form-label {
            font-weight: normal;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            min-width: 180px;
        }
        
        .form-value {
            border-bottom: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            flex: 1;
            padding: 5px 10px;
            min-height: 30px;
        }
        
        .spacer-line {
            border-bottom: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            margin: 20px 0;
        }
        
        /* Payment Methods */
        .payment-methods {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        .checkbox {
            width: 30px;
            height: 30px;
            border: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            margin-left: 15px;
            border-radius: 3px;
            display: inline-block;
            position: relative;
        }
        
        .checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        /* Amount Box */
        .amount-box {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .amount-container {
            border: 3px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            border-radius: 10px;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .currency-symbol {
            font-size: 32px;
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        .amount-value {
            font-size: 28px;
            font-weight: bold;
            min-width: 150px;
            text-align: center;
        }
        
        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
        }
        
        .signature-section {
            flex: 1;
            text-align: center;
        }
        
        .signature-line {
            border-top: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            width: 250px;
            margin: 0 auto 10px;
            padding-top: 10px;
        }
        
        .signature-label {
            font-size: 14px;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        /* Footer Bar */
        .footer-bar {
            background: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            height: 15px;
            margin-top: 30px;
            border-radius: 3px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #0052A3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Receipt</button>
    
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <img src="<?php echo Config::get('COMPANY_LOGO'); ?>" alt="Company Logo">
                <div class="company-name"><?php echo Config::get('COMPANY_NAME'); ?></div>
                <div class="tagline"><?php echo Config::get('COMPANY_TAGLINE'); ?></div>
            </div>
            
            <div class="contact-box">
                🏠 <?php echo Config::get('COMPANY_ADDRESS'); ?><br>
                📞 <?php echo Config::get('COMPANY_PHONE_1'); ?>, <?php echo Config::get('COMPANY_PHONE_2'); ?><br>
                ✉️ <?php echo Config::get('COMPANY_EMAIL'); ?>
            </div>
        </div>
        
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="date-row">
                <span class="date-label">Date:</span>
                <span class="date-value"><?php echo date('d/m/Y'); ?></span>
                <div style="margin-left: 30px; display: flex; align-items: center;">
                    <span class="receipt-badge">Payment Receipt</span>
                    <span class="receipt-number">No: RCPT-<?php echo date('Ymd-His'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="form-section">
            <div class="form-row">
                <span class="form-label">Received from:</span>
                <span class="form-value"><?php echo !empty($receipts) ? htmlspecialchars($receipts[0]['customer_name'] ?? 'Customer') : 'Customer Name'; ?></span>
            </div>
            
            <div class="form-row">
                <span class="form-label">the sum of:</span>
                <span class="form-value">
                    <?php 
                    if (class_exists('Invoice')) {
                        $amount = $receipts[0]['amount'] ?? 0;
                        $amount_naira = floor($amount);
                        $amount_kobo = round(($amount - $amount_naira) * 100);
                        echo Invoice::numberToWords($amount_naira) . ' ' . Config::get('CURRENCY_NAME', 'Naira');
                        if ($amount_kobo > 0) {
                            echo ' and ' . Invoice::numberToWords($amount_kobo) . ' ' . Config::get('CURRENCY_SYMBOL_MINOR', 'Kobo');
                        }
                        echo ' Only';
                    } else {
                        echo 'Zero ' . Config::get('CURRENCY_NAME', 'Naira') . ' Only';
                    }
                    ?>
                </span>
            </div>
            
            <div class="spacer-line"></div>
            
            <div class="form-row">
                <span class="form-label">Being part/full payment for:</span>
                <span class="form-value"><?php echo !empty($receipts[0]['description']) ? htmlspecialchars($receipts[0]['description']) : 'Goods/Services'; ?></span>
            </div>
            
            <div class="spacer-line"></div>
        </div>
        
        <!-- Payment Methods -->
        <div class="payment-methods">
            <div class="payment-option">
                Cash
                <span class="checkbox <?php echo (!empty($receipts[0]['payment_method']) && $receipts[0]['payment_method'] === 'cash') ? 'checked' : ''; ?>"></span>
            </div>
            <div class="payment-option">
                Transfer
                <span class="checkbox <?php echo (!empty($receipts[0]['payment_method']) && $receipts[0]['payment_method'] === 'transfer') ? 'checked' : ''; ?>"></span>
            </div>
            <div class="payment-option">
                POS
                <span class="checkbox <?php echo (!empty($receipts[0]['payment_method']) && $receipts[0]['payment_method'] === 'pos') ? 'checked' : ''; ?>"></span>
            </div>
            <div class="payment-option">
                Other
                <span class="checkbox <?php echo (!empty($receipts[0]['payment_method']) && $receipts[0]['payment_method'] === 'other') ? 'checked' : ''; ?>"></span>
            </div>
        </div>
        
        <!-- Amount Box -->
        <div class="amount-box">
            <div class="amount-container">
                <span class="currency-symbol"><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?></span>
                <span class="amount-value"><?php echo number_format($receipts[0]['amount'] ?? 0, 0); ?></span>
                <span class="currency-symbol">:</span>
                <span class="amount-value"><?php 
                    $amount = $receipts[0]['amount'] ?? 0;
                    $amount_naira = floor($amount);
                    $amount_kobo = round(($amount - $amount_naira) * 100);
                    echo str_pad($amount_kobo, 2, '0', STR_PAD_LEFT); 
                ?></span>
                <span class="currency-symbol"><?php echo Config::get('CURRENCY_SYMBOL_MINOR', 'K'); ?></span>
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Customer's Signature</div>
            </div>
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Manager's Signature</div>
            </div>
        </div>
        
        <!-- Footer Bar -->
        <div class="footer-bar"></div>
    </div>
</body>
</html>