<?php
namespace Http\Controllers\Sales\publicinvoice;
use Core\App;
use Core\Database;

class PublicInvoiceController
{
    
    public function show()
    {
//         file_put_contents('debug_log.txt', 'Show method called at '.date('Y-m-d H:i:s'));
//         var_dump($_SERVER);
// exit;
        $db = App::resolve(Database::class);
        $invoiceId = $_GET['id'] ?? null;
        $token = $_GET['token'] ?? null;

        if (!$invoiceId || !$token) {
            die("<h3>Invalid Token.</h3>");
        }

        // Fetch the invoice with token verification
        $invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND public_token = :token", [
            'id' => $invoiceId,
            'token' => $token
        ])->find();

        if (!$invoice) {
            die("<h3>Invoice not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $invoice['customer_id']
        ])->find();

        // Fetch invoice items
        $items = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id", [
            'invoice_id' => $invoiceId
        ])->get();

               // Calculate totals for verification
$calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $items));
$calculatedTax = array_sum(array_column($items, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($items, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($invoice['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $invoice['subtotal'] || $calculatedGrandTotal != $invoice['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$invoice['subtotal']}, Grand Total $calculatedGrandTotal vs {$invoice['grand_total']}");
}
        // Pass to view
        return views('Sales/public/index.view.php', [
            'invoice' => $invoice,
            'customer' => $customer,
            'items' => $items,
            'calculatedSubtotal' => $calculatedSubtotal,
            'calculatedTax' => $calculatedTax,
            'calculatedDiscount' => $calculatedDiscount,
            'calculatedGrandTotal' => $calculatedGrandTotal
        ]);
    }
}
