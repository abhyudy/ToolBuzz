<?php
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadedFile = FILES_PATH . basename($_FILES['pdfFile']['name']);
    move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFile);

    $reorderPages = isset($_POST['reorder']) ? trim($_POST['reorder']) : '';
    $deletePages = isset($_POST['deletePages']) ? trim($_POST['deletePages']) : '';
    $rotatePages = isset($_POST['rotatePages']) ? trim($_POST['rotatePages']) : '';
    $outputFile = OUTPUT_PATH . 'organized.pdf';

    $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';

    // Build Ghostscript command
    $cmd = "$gsPath -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile=" . escapeshellarg($outputFile) . " " . escapeshellarg($uploadedFile);

    // Apply Page Reordering
    if (!empty($reorderPages)) {
        $pages = explode(',', $reorderPages);
        $reorderCmd = "-dPageList='[" . implode(' ', $pages) . "]'";
        $cmd .= " " . $reorderCmd;
    }

    // Apply Page Deletion
    if (!empty($deletePages)) {
        $pagesToDelete = explode(',', $deletePages);
        foreach ($pagesToDelete as $page) {
            $cmd .= " -dFirstPage=$page -dLastPage=$page -dNOPAUSE -dBATCH";
        }
    }

    // Apply Rotation
    if (!empty($rotatePages)) {
        $rotations = explode(',', $rotatePages);
        foreach ($rotations as $rotation) {
            list($page, $angle) = explode('-', $rotation);
            $cmd .= " -c \"<< /Page $page /Rotate $angle >> setpagedevice\"";
        }
    }

    exec($cmd);

    if (file_exists($outputFile)) {
        echo json_encode(['filePath' => $outputFile]);
    } else {
        echo json_encode(['error' => 'PDF organization failed.']);
    }
    exit;
}
