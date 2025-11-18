<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
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
        
        .invoice-container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            border: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            padding: 15px;
        }
        
        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .logo-section {
            flex: 1;
        }
        
        .logo-section img {
            height: 80px;
            margin-bottom: 5px;
        }
        
        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            margin: 0;
            line-height: 1;
        }
        
        .tagline {
            font-size: 14px;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            font-style: italic;
            margin-top: 2px;
        }
        
        .contact-box {
            background: <?php echo Config::get('HEADER_BG_COLOR', '#0066CC'); ?>;
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            font-size: 12px;
            line-height: 1.8;
            max-width: 350px;
        }
        
        .contact-box i {
            margin-right: 8px;
        }
        
        /* Invoice Title */
        .invoice-title {
            text-align: center;
            margin: 20px 0;
        }
        
        .invoice-badge {
            display: inline-block;
            background: #333;
            color: white;
            padding: 8px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
        }
        
        .invoice-number {
            display: inline-block;
            margin-left: 20px;
            font-size: 20px;
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        /* Customer Details */
        .details-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .detail-label {
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            min-width: 80px;
        }
        
        .detail-value {
            border-bottom: 1px solid #333;
            flex: 1;
            padding-left: 5px;
        }
        
        /* Invoice Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .invoice-table thead {
            background: white;
        }
        
        .invoice-table th {
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            border-bottom: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            font-size: 13px;
        }
        
        .invoice-table td {
            padding: 10px 8px;
            border-bottom: 1px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            font-size: 12px;
        }
        
        .invoice-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .col-qty { width: 60px; text-align: center; }
        .col-description { text-align: left; }
        .col-rate { width: 100px; text-align: center; }
        .col-amount-n { width: 80px; text-align: center; }
        .col-amount-k { width: 60px; text-align: center; }
        
        /* Footer Section */
        .footer-note {
            font-style: italic;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        
        .total-label {
            font-weight: bold;
            font-size: 16px;
            margin-right: 20px;
        }
        
        .total-box {
            border: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
            padding: 8px 15px;
            border-radius: 5px;
            display: flex;
            gap: 20px;
        }
        
        .total-amount {
            font-weight: bold;
            font-size: 16px;
        }
        
        .amount-words {
            border-bottom: 1px solid #333;
            padding: 5px 0;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .currency-labels {
            display: flex;
            justify-content: flex-end;
            gap: 100px;
            margin-bottom: 30px;
            font-weight: bold;
        }
        
        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-line {
            text-align: center;
            flex: 1;
        }
        
        .signature-line::before {
            content: '';
            display: block;
            width: 200px;
            border-top: 1px solid #333;
            margin: 0 auto 5px;
        }
        
        .signature-label {
            font-size: 12px;
            color: <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .invoice-container {
                border: 2px solid <?php echo Config::get('PRIMARY_COLOR', '#0066CC'); ?>;
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
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Invoice</button>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <img src="<?php echo Config::get('COMPANY_LOGO'); ?>" alt="Company Logo">
                <div class="company-name"><?php echo Config::get('COMPANY_NAME'); ?></div>
                <div class="tagline"><?php echo Config::get('COMPANY_TAGLINE'); ?></div>
            </div>
            
            <div class="contact-box">
                🏠 <?php echo Config::get('COMPANY_ADDRESS'); ?><br>
                📞 <?php echo Config::get('COMPANY_PHONE_1'); ?><br>
                📞 <?php echo Config::get('COMPANY_PHONE_2'); ?><br>
                ✉️ <?php echo Config::get('COMPANY_EMAIL'); ?>
            </div>
        </div>
        
        <!-- Invoice Title -->
        <div class="invoice-title">
            <span class="invoice-badge"><?php echo strtoupper($invoice['invoice_type']); ?>/CREDIT INVOICE</span>
            <span class="invoice-number">No. <?php echo htmlspecialchars($invoice['invoice_number']); ?></span>
        </div>
        
        <!-- Customer Details -->
        <div class="details-section">
            <div style="flex: 1;">
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($invoice['customer_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($invoice['customer_address']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"></span>
                    <span class="detail-value"></span>
                </div>
            </div>
            
            <div style="flex: 0 0 250px;">
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($invoice['invoice_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">LPO No:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($invoice['lpo_number']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Invoice Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="col-qty">QTY.</th>
                    <th class="col-description">DESCRIPTION OF GOODS</th>
                    <th class="col-rate">RATE</th>
                    <th colspan="2">AMOUNT</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="col-amount-n"><?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?></th>
                    <th class="col-amount-k"><?php echo Config::get('CURRENCY_SYMBOL_MINOR', 'K'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice['items'] as $item): 
                    $naira = floor($item['amount']);
                    $kobo = round(($item['amount'] - $naira) * 100);
                ?>
                <tr>
                    <td class="col-qty"><?php echo $item['qty']; ?></td>
                    <td class="col-description"><?php echo htmlspecialchars($item['description']); ?></td>
                    <td class="col-rate"><?php echo number_format($item['rate'], 2); ?></td>
                    <td class="col-amount-n"><?php echo number_format($naira); ?></td>
                    <td class="col-amount-k"><?php echo str_pad($kobo, 2, '0', STR_PAD_LEFT); ?></td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Empty rows to maintain layout -->
                <?php for ($i = count($invoice['items']); $i < 12; $i++): ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer-note">
            Received the above goods in good condition
        </div>
        
        <div class="total-section">
            <span class="total-label">TOTAL <?php echo Config::get('CURRENCY_SYMBOL_MAJOR', '₦'); ?></span>
            <div class="total-box">
                <span class="total-amount"><?php echo number_format(floor($invoice['total'])); ?></span>
                <span class="total-amount"><?php echo str_pad(round(($invoice['total'] - floor($invoice['total'])) * 100), 2, '0', STR_PAD_LEFT); ?></span>
            </div>
        </div>
        
        <div style="margin-bottom: 5px;">
            <strong>Amount in words:</strong>
        </div>
        <div class="amount-words">
            <?php echo htmlspecialchars($invoice['amount_in_words']); ?>
        </div>
        
        <div class="currency-labels">
            <span><?php echo Config::get('CURRENCY_NAME', 'Naira'); ?></span>
            <span><?php echo Config::get('CURRENCY_SYMBOL_MINOR', 'Kobo'); ?></span>
        </div>
        
        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-line">
                <div class="signature-label">Customer's Signature</div>
            </div>
            <div class="signature-line">
                <div class="signature-label">Manager's Signature</div>
            </div>
        </div>
    </div>
</body>
</html>