-- Add invoice_id column to receipts table to link receipts to invoices
ALTER TABLE receipts 
ADD COLUMN invoice_id INT DEFAULT NULL AFTER receipt_number,
ADD FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL;

-- Add index for better performance
CREATE INDEX idx_invoice_id ON receipts(invoice_id);

-- Add paid_amount column to invoices to track how much has been paid
ALTER TABLE invoices
ADD COLUMN paid_amount DECIMAL(10,2) DEFAULT 0.00 AFTER total;

-- Update existing invoices to set paid status based on receipts
UPDATE invoices i
SET status = 'paid',
    paid_amount = total
WHERE EXISTS (
    SELECT 1 FROM receipts r 
    WHERE r.invoice_id = i.id
);