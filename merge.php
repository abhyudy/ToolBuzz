<?php
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFiles'])) {
    $uploadedFiles = [];
    
    foreach ($_FILES['pdfFiles']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['pdfFiles']['name'][$key]);
        $targetFilePath = FILES_PATH . $file_name;
        move_uploaded_file($tmp_name, $targetFilePath);
        $uploadedFiles[] = $targetFilePath;
    }

    if (empty($uploadedFiles)) {
        $error = "❌ No PDF files found for merging.";
    } else {
        $outputFile = OUTPUT_PATH . 'merged.pdf';
        $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';
        $cmd = "$gsPath -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($outputFile) . " " . implode(" ", array_map('escapeshellarg', $uploadedFiles));

        exec($cmd);
        if (file_exists($outputFile)) {
            $_SESSION['merged_file'] = $outputFile;  
        } else {
            $error = "❌ PDF merging failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Merge PDFs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4 text-center">Merge PDFs</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="pdfFiles[]" class="form-control mb-3" multiple accept="application/pdf" required>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Merge PDFs</button>
        </form>

        <?php if (isset($_SESSION['merged_file'])): ?>
            <div class="alert alert-success mt-3">
                ✅ PDF Merged! <a href="<?php echo str_replace(HOME_PATH, '', $_SESSION['merged_file']); ?>" target="_blank" class="btn btn-success">View PDF</a>
            </div>
            <!-- Embed the PDF in an iframe -->
            <iframe src="<?php echo str_replace(HOME_PATH, '', $_SESSION['merged_file']); ?>" 
                    width="100%" height="500px" class="mt-3 border"></iframe>
        <?php unset($_SESSION['merged_file']); endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
