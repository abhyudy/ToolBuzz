<?php
include_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadedFile = FILES_PATH . basename($_FILES['pdfFile']['name']);
    move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFile);

    $rangeType = $_POST['rangeType'];
    $outputFiles = [];

    $gsPath = '"C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe"';

    if ($rangeType === 'custom') {
        $pages = isset($_POST['pages']) ? trim($_POST['pages']) : '';
        if (empty($pages)) {
            die("<p class='error'>❌ Please enter pages to extract (e.g., 1-3,5).</p>");
        }

        $pageParts = explode(',', str_replace(' ', '', $pages));
        foreach ($pageParts as $index => $part) {
            if (strpos($part, '-') !== false) {
                list($start, $end) = explode('-', $part);
            } else {
                $start = $end = $part;
            }

            $splitOutputFile = OUTPUT_PATH . "split_part_" . ($index + 1) . ".pdf";
            $cmd = "$gsPath -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dFirstPage=$start -dLastPage=$end -sOutputFile=" . escapeshellarg($splitOutputFile) . " " . escapeshellarg($uploadedFile);
            exec($cmd);
            $outputFiles[] = $splitOutputFile;
        }
    } elseif ($rangeType === 'fixed') {
        $parts = intval($_POST['fixedParts']);
        $totalPages = 0;

        // Get total pages in the PDF
        $cmd = "$gsPath -q -dNODISPLAY -c \"($uploadedFile) (r) file runpdfbegin pdfpagecount = quit\"";
        exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && isset($output[0])) {
            $totalPages = intval($output[0]);
        } else {
            die("<p class='error'>❌ Failed to read the total number of pages in the PDF.</p>");
        }

        $pagesPerPart = ceil($totalPages / $parts);
        for ($i = 0; $i < $parts; $i++) {
            $start = $i * $pagesPerPart + 1;
            $end = min(($i + 1) * $pagesPerPart, $totalPages);

            $splitOutputFile = OUTPUT_PATH . "split_part_" . ($i + 1) . ".pdf";
            $cmd = "$gsPath -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dFirstPage=$start -dLastPage=$end -sOutputFile=" . escapeshellarg($splitOutputFile) . " " . escapeshellarg($uploadedFile);
            exec($cmd);
            $outputFiles[] = $splitOutputFile;
        }
    }

    $_SESSION['split_files'] = $outputFiles;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Split PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4 text-center">Split PDF</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="pdfFile">Upload PDF File:</label>
                <input type="file" name="pdfFile" id="pdfFile" class="form-control" accept="application/pdf" required>
            </div>

            <div class="form-group">
                <label for="rangeType">Split Method:</label>
                <select name="rangeType" id="rangeType" class="form-control" onchange="toggleRangeType(this)">
                    <option value="custom">Custom Range</option>
                    <option value="fixed">Fixed Parts</option>
                </select>
            </div>

            <div class="form-group" id="customRangeGroup">
                <label for="customRange">Enter Page Range (e.g., 1-3, 5):</label>
                <input type="text" name="pages" id="customRange" class="form-control" placeholder="e.g., 1-3, 5">
            </div>

            <div class="form-group" id="fixedPartsGroup" style="display: none;">
                <label for="fixedParts">Select Number of Parts:</label>
                <select name="fixedParts" id="fixedParts" class="form-control">
                    <option value="2">Split into 2 parts</option>
                    <option value="3">Split into 3 parts</option>
                    <option value="4">Split into 4 parts</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Split PDF</button>
        </form>

        <?php if (isset($_SESSION['split_files'])): ?>
            <div class="mt-4">
                <h4>Split Files:</h4>
                <?php foreach ($_SESSION['split_files'] as $file): ?>
                    <div class="alert alert-success">
                        ✅ PDF Split! <a href="<?php echo str_replace(HOME_PATH, '', $file); ?>" target="_blank" class="btn btn-success">Download Part</a>
                    </div>
                <?php endforeach; unset($_SESSION['split_files']); ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleRangeType(select) {
            const customRangeGroup = document.getElementById('customRangeGroup');
            const fixedPartsGroup = document.getElementById('fixedPartsGroup');

            if (select.value === 'custom') {
                customRangeGroup.style.display = 'block';
                fixedPartsGroup.style.display = 'none';
            } else if (select.value === 'fixed') {
                customRangeGroup.style.display = 'none';
                fixedPartsGroup.style.display = 'block';
            }
        }
    </script>
</body>
</html>
