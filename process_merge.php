<?php
include_once 'config.php';

if (isset($_POST['submit']) && !empty($_FILES['pdfFiles']['name'][0])) {
    foreach ($_FILES['pdfFiles']['tmp_name'] as $key => $tmp_name) {
        move_uploaded_file($tmp_name, FILES_PATH . basename($_FILES['pdfFiles']['name'][$key]));
    }

    $files = glob(FILES_PATH . '*.pdf');
    if (empty($files)) {
        die("<p class='error'>❌ No PDF files found for merging.</p>");
    }

    $output_file = OUTPUT_PATH . 'merged.pdf';
    $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';
    $cmd = "$gsPath -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($output_file) . " " . implode(" ", array_map('escapeshellarg', $files));

    exec($cmd);

    if (file_exists($output_file)) {
        echo "<p>✅ PDFs Merged! <a href='$output_file'>Download Here</a></p>";
    } else {
        echo "<p class='error'>❌ PDF merging failed.</p>";
    }
}
?>
