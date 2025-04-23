<?php
include_once 'config.php';

$inputFileSize = null;
$outputFileSize = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadedFile = FILES_PATH . basename($_FILES['pdfFile']['name']);
    move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFile);
    $inputFileSize = round(filesize($uploadedFile) / 1024, 2); // Convert to KB

    $targetSizeKB = isset($_POST['targetSize']) ? floatval($_POST['targetSize']) : 0;
    $outputFile = OUTPUT_PATH . 'resized.pdf';

    $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';
    $cmd = "$gsPath -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dPDFSETTINGS=/screen -sOutputFile=" . escapeshellarg($outputFile) . " " . escapeshellarg($uploadedFile);

    exec($cmd);

    if (file_exists($outputFile)) {
        $outputFileSize = round(filesize($outputFile) / 1024, 2);
        $_SESSION['resized_file'] = $outputFile;
    } else {
        $error = "❌ PDF resizing failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resize PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4 text-center">Resize PDF</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="pdfFile" class="form-control mb-3" accept="application/pdf" required onchange="displayFileSize(this)">
            <small id="fileSize" class="text-muted"></small>

            <input type="number" name="targetSize" class="form-control mb-3" placeholder="Target Size (KB)" required>
            <button type="submit" class="btn btn-success btn-lg btn-block">Resize PDF</button>
        </form>

        <?php if ($inputFileSize !== null): ?>
            <p class="alert alert-info mt-3">📂 Input File Size: <?php echo $inputFileSize; ?> KB</p>
        <?php endif; ?>

        <?php if ($outputFileSize !== null): ?>
            <p class="alert alert-success">📂 Output File Size: <?php echo $outputFileSize; ?> KB</p>
            <div class="embed-responsive embed-responsive-16by9 mt-3">
                <iframe src="<?php echo str_replace(HOME_PATH, '', $_SESSION['resized_file']); ?>" class="embed-responsive-item" frameborder="0"></iframe>
            </div>
        <?php unset($_SESSION['resized_file']); endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>

    <script>
        function displayFileSize(input) {
            if (input.files && input.files[0]) {
                let fileSize = (input.files[0].size / 1024).toFixed(2);
                document.getElementById('fileSize').innerText = `Selected File Size: ${fileSize} KB`;
            }
        }
    </script>
</body>
</html>
