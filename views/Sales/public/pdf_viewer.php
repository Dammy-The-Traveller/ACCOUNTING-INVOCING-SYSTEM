<?php
// viewer.php

$invoiceId = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;
if (!$invoiceId || !$token) {
    die("Invoice ID not provided.");
}


$pdfFile = "Public/invoices/invoice_{$invoiceId}&{$token}.pdf";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Viewer</title>
    <link rel="stylesheet" href="Public/assets/pdf/viewer.css">
</head>
<body>
<div id="viewerContainer" class="pdfViewer">
    
    <iframe
        src="Public/invoices/invoice_<?php echo $invoiceId.'_'. $token; ?>.pdf"
        width="100%" height="1000px" style="border:none;">
    </iframe>
</div>

<script src="Public/assets/pdf/lib/pdf.js"></script>
<script src="Public/assets/pdf/viewer.js"></script>
</body>
</html>
