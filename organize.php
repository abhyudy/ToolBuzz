<?php
require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Handle PDF Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadDir = 'uploads/';
    $uploadedFile = $uploadDir . basename($_FILES['pdfFile']['name']);
    move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFile);

    // Get page count
    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($uploadedFile);

    echo json_encode([
        'filePath' => $uploadedFile,
        'message' => 'PDF uploaded successfully.',
        'pageCount' => $pageCount
    ]);
    exit;
}

// Handle Reordering
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reorderPages') {
    $filePath = $_POST['filePath'];
    $newOrder = explode(',', $_POST['order']); // Input like "3,1,2"

    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($filePath);

    $outputFile = 'uploads/reordered.pdf';

    foreach ($newOrder as $page) {
        $tpl = $pdf->importPage(intval($page));
        $pdf->AddPage();
        $pdf->useTemplate($tpl);
    }

    $pdf->Output('F', $outputFile);
    echo json_encode(['filePath' => $outputFile, 'message' => 'PDF reordered successfully.']);
    exit;
}
?>
<html lang="en">

<head>
    <!DOCTYPE html>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Reorder Tool</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>PDF Reorder Tool</h2>

        <!-- Step 1: Upload PDF -->
        <form id="uploadForm">
            <input type="file" name="pdfFile" id="pdfFile" accept="application/pdf" required>
            <button type="submit" class="btn">Upload PDF</button>
        </form>

        <!-- Step 2: Choose Reordering Method -->
        <div id="methodSelection" style="display: none;">
            <h3>Choose Reordering Method</h3>
            <button id="dragDropBtn" class="btn">Drag & Drop</button>
            <button id="manualInputBtn" class="btn">Manual Input</button>
        </div>

        <!-- Step 3: Reorder Pages -->
        <div id="dragDropContainer" style="display: none;">
            <h3>Reorder Pages (Drag & Drop)</h3>
            <ul id="pageList"></ul>
            <button id="dragApplyOrderBtn" class="btn">Apply Order</button>
        </div>


        <div id="manualInputContainer" style="display: none;">
            <input type="text" id="manualOrder" placeholder="Enter new order (e.g., 3,1,2)">
            <button id="applyOrderBtn" class="btn">Apply Order</button>
        </div>
    </div>

    <!-- Step 4: Save and Download -->
    <div id="saveSection" style="display: none;">
        <button id="viewPdfBtn" class="btn">View PDF</button>
        <a id="downloadPdfBtn" class="btn" href="#" download>Download PDF</a>
    </div>
    </div>

    <script>
        let uploadedFilePath = '';
        let pageCount = 0;

        // Step 1: Upload PDF
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: 'organize.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    const data = JSON.parse(response);
                    uploadedFilePath = data.filePath;
                    pageCount = data.pageCount;
                    alert(data.message);
                    $('#methodSelection').show();
                },
                error: function() {
                    alert('Failed to upload PDF.');
                }
            });
        });

        // Step 2: Choose Reordering Method
        $('#dragDropBtn').on('click', function() {
            $('#methodSelection').hide();
            $('#dragDropContainer').show();
            $('#reorderSection').show();
            generatePageList();
        });

        $('#manualInputBtn').on('click', function() {
            $('#methodSelection').hide();
            $('#manualInputContainer').show();
            $('#reorderSection').show();
        });

        // Generate Page List for Drag & Drop
        function generatePageList() {
            $('#pageList').empty();
            for (let i = 1; i <= pageCount; i++) {
                $('#pageList').append(`<li data-page="${i}" draggable="true">${i}</li>`);
            }
            enableDragAndDrop();
        }

        // Enable Drag and Drop Functionality
        function enableDragAndDrop() {
            let dragged;

            $('#pageList li').on('dragstart', function(e) {
                dragged = $(this);
            });

            $('#pageList li').on('dragover', function(e) {
                e.preventDefault();
            });

            $('#pageList li').on('drop', function(e) {
                e.preventDefault();
                if (dragged) {
                    const dropped = $(this);
                    if (dragged[0] !== dropped[0]) {
                        dragged.insertAfter(dropped);
                    }
                }
            });
        }

        // Step 3: Apply Reordering
        $('#applyOrderBtn').on('click', function() {
            const order = $('#manualOrder').val();
            submitReorder(order);
        });

        $('#dragDropContainer').on('mouseup', function() {
            const order = Array.from($('#pageList li')).map((li) => $(li).data('page'));
            submitReorder(order.join(','));
        });

        function submitReorder(order) {
            $.post('organize.php', {
                action: 'reorderPages',
                filePath: uploadedFilePath,
                order: order
            }, function(response) {
                const data = JSON.parse(response);
                uploadedFilePath = data.filePath;
                alert(data.message);

                // Show the options to View and Download the reordered PDF
                $('#saveSection').show();
                $('#downloadPdfBtn').attr('href', uploadedFilePath);
            });
        }


        // Step 4: Save and Download
        $('#viewPdfBtn').on('click', function() {
            window.open(uploadedFilePath, '_blank');
        });
    </script>
</body>

</html>