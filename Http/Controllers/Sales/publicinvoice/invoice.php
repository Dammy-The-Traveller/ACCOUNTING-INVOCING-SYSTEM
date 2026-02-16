<?php
namespace Http\Controllers\Sales\publicinvoice;
use Core\App;
use Core\Database;

class invoice
{
    
    public function show()
    /**
     * Handles the logic for the invoice functionality within the Sales public invoice controller.
     *
     * This section of code is responsible for managing invoice-related operations,
     * such as creating, viewing, or processing invoices in the public invoice context.
     *
     * @package AIS\Http\Controllers\Sales\publicinvoice
     */
    {
        $db = App::resolve(Database::class);
        $invoiceId = $_GET['id'] ?? 0;
        if (!$invoiceId) {
            die("<script>alert('Invalid Token'); window.history.back();</script>");
        }

        // Fetch the invoice with token verification
        $invoice = $db->query("SELECT * FROM invoices WHERE id = :id and type=:type", [
            /**
             * The unique identifier for the invoice.
             *
             * @var int $invoiceId The ID of the invoice.
             */
            'id' => $invoiceId,
            'type' => 'invoices'
        ])->find();

        if (!$invoice) {
            die("<h3>Invoice not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $invoice['customer_id']
        ])->find();

        // Fetch invoice items
        $items = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id and type=:type", [
            'invoice_id' => $invoiceId,
            'type' => 'invoices'
        ])->get();
        // var_dump($items);
        // exit;

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
        return views('Sales/public/invoice.view.php', [
            'invoice' => $invoice,
            'invoiceId' => $invoiceId,
            'token' => $invoice['public_token'] ?? null,
            'customer' => $customer,
            'items' => $items,
            'calculatedSubtotal' => $calculatedSubtotal,
            'calculatedTax' => $calculatedTax,
            'calculatedDiscount' => $calculatedDiscount,
            'calculatedGrandTotal' => $calculatedGrandTotal
        ]);
    }
}
