<?php
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadedFile = FILES_PATH . basename($_FILES['pdfFile']['name']);
    move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFile);

    $targetSizeKB = isset($_POST['targetSize']) ? floatval($_POST['targetSize']) : 0;
    $outputFile = OUTPUT_PATH . 'resized.pdf';

    $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';
    $cmd = "$gsPath -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dPDFSETTINGS=/screen -sOutputFile=" . escapeshellarg($outputFile) . " " . escapeshellarg($uploadedFile);

    exec($cmd);
    
    echo "<p>✅ PDF Resized! <a href='$outputFile'>Download Here</a></p>";
}
?>
