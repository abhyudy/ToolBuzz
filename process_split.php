<?php
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadedFile = FILES_PATH . basename($_FILES['pdfFile']['name']);
    move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFile);

    $pages = isset($_POST['pages']) ? trim($_POST['pages']) : '';
    if (empty($pages)) {
        die("<p class='error'>❌ Please enter pages to extract (e.g., 1-3,5).</p>");
    }

    $outputFile = OUTPUT_PATH . 'split.pdf';
    $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';

    // Split pages into an array
    $pageParts = explode(',', str_replace(' ', '', $pages));
    $commands = [];

    foreach ($pageParts as $part) {
        if (strpos($part, '-') !== false) {
            list($start, $end) = explode('-', $part);
            $commands[] = "-dFirstPage=$start -dLastPage=$end";
        } else {
            $commands[] = "-dFirstPage=$part -dLastPage=$part";
        }
    }

    foreach ($commands as $index => $command) {
        $splitOutputFile = OUTPUT_PATH . "split_part_" . ($index + 1) . ".pdf";
        $cmd = "$gsPath -sDEVICE=pdfwrite -dNOPAUSE -dBATCH $command -sOutputFile=" . escapeshellarg($splitOutputFile) . " " . escapeshellarg($uploadedFile);
        exec($cmd);
    }

    echo "<p class='success'>✅ PDF Split Successfully! Check the output folder for extracted pages.</p>";
}
?>
