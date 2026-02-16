<?php
// viewer.php

$invoiceId = $_GET['id'] ?? null;

if (!$invoiceId) {
    die("Invoice ID not provided.");
}


$pdfFile = "Public/return/return_{$invoiceId}.pdf";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>return Viewer</title>
    <link rel="stylesheet" href="Public/assets/pdf/viewer.css">
</head>
<body>
<div id="viewerContainer" class="pdfViewer">
    
    <iframe
        src="Public/returns/return_<?php echo $invoiceId; ?>.pdf"
        width="100%" height="1000px" style="border:none;">
    </iframe>
</div>

<script src="Public/assets/pdf/lib/pdf.js"></script>
<script src="Public/assets/pdf/viewer.js"></script>
</body>
</html>
