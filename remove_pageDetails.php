<?php
include_once 'config.php';

// Handle the uploaded file in PHP
$pdfData = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $pdfFile = $_FILES['pdfFile'];

    // Validate the file
    if ($pdfFile['error'] === UPLOAD_ERR_OK && $pdfFile['type'] === 'application/pdf') {
        // Read the file contents
        $filePath = $pdfFile['tmp_name'];
        $pdfData = base64_encode(file_get_contents($filePath));
    } else {
        // Handle errors (e.g., invalid file type or upload error)
        echo '<script>alert("Please upload a valid PDF file.");</script>';
        $pdfData = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Pages - ToolsBuzz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <style>
        /* General Styles */
        body {
            background: linear-gradient(135deg, #e6f0fa 0%, #f0f7ff 100%);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1080px;
            margin: 20px auto 20px;
            padding: 0 15px;
            flex: 1;
        }

        .container h1 {
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .preview-result {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .preview-thumbnails {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            flex: 1;
            min-width: 300px;
            justify-content: center;
        }

        .thumbnail {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            width: 120px;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            position: relative;
            transition: transform 0.2s;
        }

        .thumbnail:hover {
            transform: scale(1.05);
        }

        .thumbnail canvas {
            max-width: 100%;
            max-height: 100%;
        }

        .thumbnail input[type="checkbox"] {
            position: absolute;
            top: 5px;
            left: 5px;
            width: 18px;
            height: 18px;
            z-index: 1;
        }

        .thumbnail label {
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.8rem;
            color: #333;
            padding: 2px;
            background: rgba(255, 255, 255, 0.8);
        }

        .remove-section {
            flex: 1;
            min-width: 250px;
            padding: 15px;
            margin-top: 15px;
        }

        .remove-section h4 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
        }

        .remove-section .total-pages {
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        .remove-section .form-group {
            margin-bottom: 15px;
        }

        .remove-section label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
            display: block;
        }

        .remove-section input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .remove-section button {
            background: #00aaff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
            font-size: 1rem;
            width: 100%;
            margin-top: 10px;
        }

        .remove-section button:hover {
            background: #0099e6;
        }

        .remove-section button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        .result-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            margin: 20px auto;
            display: none;
        }

        #viewer {
            width: 100%;
            height: 500px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        #scrollBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99;
            border: none;
            outline: none;
            background-color: #00aaff;
            color: white;
            cursor: pointer;
            padding: 12px;
            border-radius: 50%;
            font-size: 18px;
            transition: all 0.3s;
        }

        #scrollBtn:hover {
            background-color: #0099e6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                margin-top: 40px;
            }

            .preview-result {
                flex-direction: column;
                align-items: center;
            }

            .preview-thumbnails {
                width: 100%;
            }

            .thumbnail {
                width: 100px;
                height: 140px;
            }

            .remove-section {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                margin-top: 40px;
            }

            .thumbnail {
                width: 80px;
                height: 120px;
            }

            .thumbnail label {
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <h1>Remove Pages from PDF</h1>

        <div class="preview-result">
            <div class="preview-thumbnails" id="preview"></div>
            <div class="remove-section">
                <h4>Remove Pages</h4>
                <p class="total-pages" id="total-pages">Total Pages: 0</p>
                <div class="form-group">
                    <label for="pagesToRemove">Pages to remove:</label>
                    <input type="text" id="pagesToRemove" placeholder="e.g., 1, 5-10">
                </div>
                <button id="btnRemove" type="button">Remove Pages</button>
            </div>
        </div>

        <div class="result-container">
            <iframe id="viewer"></iframe>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <button id="scrollBtn" title="Go to top">↑</button>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Initialize PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';

        document.addEventListener('DOMContentLoaded', function() {
            const preview = document.getElementById('preview');
            const pagesToRemoveInput = document.getElementById('pagesToRemove');
            const totalPagesDisplay = document.getElementById('total-pages');
            const btnRemove = document.getElementById('btnRemove');
            const viewer = document.getElementById('viewer');
            const resultContainer = document.querySelector('.result-container');
            let pdfDoc = null;
            let blobURL = null;

            // Get the PDF data from PHP (base64 encoded)
            const pdfData = '<?php echo $pdfData; ?>';

            if (pdfData) {
                loadPDF();
            } else {
                preview.innerHTML = '<p class="text-center w-100">Please upload a PDF file to begin.</p>';
                btnRemove.disabled = true;
            }

            // Mobile menu toggle (if exists)
            const menuIcon = document.querySelector('.nav-menu-icon');
            if (menuIcon) {
                menuIcon.addEventListener('click', () => {
                    const navMenuPhone = document.querySelector('.nav-menu-icon-phone');
                    if (navMenuPhone) {
                        navMenuPhone.classList.toggle('active');
                    }
                });
            }

            // Scroll to top button
            window.onscroll = function() {
                const scrollBtn = document.getElementById("scrollBtn");
                if (scrollBtn) {
                    scrollBtn.style.display = (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) ?
                        "block" : "none";
                }
            };

            function scrollToTop() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }

            document.getElementById('scrollBtn')?.addEventListener('click', scrollToTop);

            function loadPDF() {
                const binary = atob(pdfData);
                const len = binary.length;
                const bytes = new Uint8Array(len);
                for (let i = 0; i < len; i++) {
                    bytes[i] = binary.charCodeAt(i);
                }

                pdfjsLib.getDocument({
                    data: bytes
                }).promise.then(function(doc) {
                    pdfDoc = doc;
                    totalPagesDisplay.textContent = `Total Pages: ${pdfDoc.numPages}`;
                    renderThumbnails();
                }).catch(function(err) {
                    console.error('Error loading PDF:', err);
                    preview.innerHTML = '<p class="text-danger text-center w-100">Failed to load the PDF. Please try another file.</p>';
                });
            }

            function renderThumbnails() {
                preview.innerHTML = '';

                for (let p = 1; p <= pdfDoc.numPages; p++) {
                    const thumbnail = document.createElement('div');
                    thumbnail.className = 'thumbnail';
                    thumbnail.id = `thumbnail-${p}`;

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.value = p;
                    checkbox.id = `page_${p}`;

                    const canvas = document.createElement('canvas');
                    const label = document.createElement('label');
                    label.htmlFor = `page_${p}`;
                    label.textContent = `Page ${p}`;

                    thumbnail.appendChild(checkbox);
                    thumbnail.appendChild(canvas);
                    thumbnail.appendChild(label);
                    preview.appendChild(thumbnail);

                    // Render each page
                    pdfDoc.getPage(p).then(function(page) {
                        const viewport = page.getViewport({
                            scale: 0.3
                        });
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        page.render({
                            canvasContext: canvas.getContext('2d'),
                            viewport: viewport
                        });
                    });
                }

                // Update input when checkboxes change
                document.querySelectorAll('.thumbnail input[type="checkbox"]').forEach(checkbox => {
                    checkbox.addEventListener('change', updateInputFromCheckboxes);
                });
            }

            function updateInputFromCheckboxes() {
                const checkboxes = document.querySelectorAll('.thumbnail input[type="checkbox"]:checked');
                const selectedPages = Array.from(checkboxes).map(cb => parseInt(cb.value));
                pagesToRemoveInput.value = selectedPages.join(', ');
            }

            function parsePageRange(range) {
                if (!range.trim()) return [];

                const pages = [];
                const parts = range.split(',');
                const totalPages = pdfDoc?.numPages || 0;

                parts.forEach(part => {
                    part = part.trim();
                    if (!part) return;

                    if (part.includes('-')) {
                        const [start, end] = part.split('-').map(num => parseInt(num.trim()));
                        if (!isNaN(start) && !isNaN(end)) {
                            for (let i = Math.max(1, start); i <= Math.min(totalPages, end); i++) {
                                pages.push(i);
                            }
                        }
                    } else {
                        const pageNum = parseInt(part);
                        if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= totalPages) {
                            pages.push(pageNum);
                        }
                    }
                });

                return [...new Set(pages)].sort((a, b) => a - b);
            }

            pagesToRemoveInput.addEventListener('input', function() {
                const pages = parsePageRange(this.value);

                // Update checkboxes
                document.querySelectorAll('.thumbnail input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = pages.includes(parseInt(checkbox.value));
                });
            });

            btnRemove.addEventListener('click', function() {
                const range = pagesToRemoveInput.value.trim();
                const pagesToRemove = parsePageRange(range);

                if (pagesToRemove.length === 0) {
                    alert('Please select pages to remove or enter a page range (e.g., 1, 5-10)');
                    return;
                }

                btnRemove.disabled = true;
                btnRemove.textContent = 'Processing...';

                const formData = new FormData();
                formData.append('pdfData', pdfData);
                formData.append('pagesToRemove', pagesToRemove.join(','));

                fetch('process_remove.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.blob();
                    })
                    .then(blob => {
                        if (blobURL) URL.revokeObjectURL(blobURL);
                        blobURL = URL.createObjectURL(blob);
                        viewer.src = blobURL;
                        resultContainer.style.display = 'block';

                        // Scroll to result
                        resultContainer.scrollIntoView({
                            behavior: 'smooth'
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing the PDF: ' + error.message);
                    })
                    .finally(() => {
                        btnRemove.disabled = false;
                        btnRemove.textContent = 'Remove Pages';
                    });
            });
        });
    </script>
</body>

</html>