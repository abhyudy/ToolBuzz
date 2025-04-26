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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script>
        // Set the worker source for pdf.js to avoid deprecated API warning
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles.css">
    <style>
        /* General Styles */
        body {
            background: linear-gradient(135deg, #e6f0fa 0%, #f0f7ff 100%);
            position: relative;
            overflow-x: hidden;
            max-width: 1440px;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            max-width: 1440px;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50"><circle cx="25" cy="25" r="20" fill="rgba(255,255,255,0.2)" /></svg>') repeat;
            opacity: 0.5;
            z-index: -1;
        }
        .container {
            max-width: 800px;
            margin: 120px auto 20px;
            text-align: center;
            padding: 0 15px;
        }
        .container h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .preview-result {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .preview-thumbnails {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            max-width: 60%;
        }
        .thumbnail {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            width: 150px;
            height: 200px;
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
        }
        .thumbnail a {
            text-decoration: none;
            color: #333;
            font-size: 0.9rem;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
        .remove-section {
            text-align: right;
            flex: 1;
            min-width: 200px;
            padding-left: 20px;
        }
        .remove-section h4 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .remove-section .total-pages {
            font-size: 0.9rem;
            color: #333;
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
            margin-bottom: 15px;
            box-sizing: border-box;
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
            max-width: 800px;
            margin: 20px auto;
        }
        #viewer {
            display: none;
            width: 100%;
            height: 560px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .container { margin-top: 100px; }
            .container h1 { font-size: 2.2rem; }
            .preview-result { padding: 15px; }
            .preview-thumbnails { max-width: 55%; gap: 15px; }
            .thumbnail { width: 130px; height: 180px; }
            .remove-section { min-width: 180px; padding-left: 15px; }
            #viewer { height: 500px; }
        }
        @media (max-width: 768px) {
            .container { margin-top: 80px; }
            .container h1 { font-size: 2rem; }
            .preview-result { flex-direction: column; align-items: center; padding: 15px; margin: 15px 10px; }
            .preview-thumbnails { justify-content: center; max-width: 100%; gap: 10px; }
            .thumbnail { width: 120px; height: 160px; }
            .thumbnail a { font-size: 0.8rem; }
            .thumbnail input[type="checkbox"] { width: 16px; height: 16px; }
            .remove-section { text-align: center; margin-top: 20px; min-width: 0; padding-left: 0; width: 100%; }
            .remove-section h4 { font-size: 1rem; }
            .remove-section .total-pages { font-size: 0.85rem; }
            .remove-section label { font-size: 0.85rem; }
            .remove-section input[type="text"] { font-size: 0.85rem; padding: 6px; }
            .remove-section button { padding: 8px 15px; font-size: 0.9rem; }
            #viewer { height: 400px; }
        }
        @media (max-width: 480px) {
            .container { margin-top: 60px; }
            .container h1 { font-size: 1.8rem; }
            .preview-result { padding: 10px; margin: 10px 5px; }
            .thumbnail { width: 100px; height: 130px; }
            .thumbnail a { font-size: 0.7rem; }
            .thumbnail input[type="checkbox"] { width: 14px; height: 14px; }
            .remove-section h4 { font-size: 0.9rem; }
            .remove-section .total-pages { font-size: 0.8rem; }
            .remove-section label { font-size: 0.8rem; }
            .remove-section input[type="text"] { font-size: 0.8rem; padding: 5px; }
            .remove-section button { padding: 6px 12px; font-size: 0.8rem; }
            #viewer { height: 300px; }
        }
        @media (max-width: 360px) {
            .container h1 { font-size: 1.5rem; }
            .thumbnail { width: 90px; height: 120px; }
            .thumbnail a { font-size: 0.65rem; }
            .thumbnail input[type="checkbox"] { width: 12px; height: 12px; }
            .remove-section h4 { font-size: 0.85rem; }
            .remove-section .total-pages { font-size: 0.75rem; }
            .remove-section label { font-size: 0.75rem; }
            .remove-section input[type="text"] { font-size: 0.75rem; }
            .remove-section button { padding: 5px 10px; font-size: 0.75rem; }
            #viewer { height: 250px; }
        }

        /* Navbar Styles */
        .nav {
            background: #e6f0fa;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-bar {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-logo img {
            height: 30px;
            width: auto;
        }
        .nav-menu {
            list-style: none;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        .nav-menu li {
            margin-left: 30px;
        }
        .nav-menu li a {
            color: #2c3e50;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: color 0.3s;
        }
        .nav-menu li a:hover {
            color: #00aaff;
        }
        .nav-menu li a i {
            margin-left: 5px;
            font-size: 0.8rem;
        }
        .nav-menu-icon {
            display: none;
            font-size: 1.5rem;
            color: #2c3e50;
            cursor: pointer;
        }

        /* Mobile Menu */
        .nav-menu-icon-phone {
            display: none;
        }
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            .nav-menu-icon {
                display: block;
            }
            .nav-menu-icon-phone.active .nav-menu-phone {
                display: block;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background: #e6f0fa;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                padding: 10px 20px;
            }
            .nav-menu-phone {
                display: none;
                list-style: none;
                padding: 0;
            }
            .nav-menu-phone ul {
                padding: 0;
            }
            .nav-menu-phone li {
                margin: 10px 0;
            }
            .nav-menu-phone a {
                color: #2c3e50;
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 600;
                text-transform: uppercase;
            }
            .nav-menu-phone a:hover {
                color: #00aaff;
            }
        }

        /* Footer Styles */
        .footer-main {
            background: #e6f0fa;
            padding: 40px 20px;
            color: #2c3e50;
            margin-top: 40px;
        }
        .footer-box-main {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .footer-box {
            flex: 1;
            min-width: 200px;
        }
        .footer-box h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .footer-box ul {
            list-style: none;
            padding: 0;
        }
        .footer-box li {
            margin-bottom: 10px;
        }
        .footer-box a, .footer-box p {
            color: #2c3e50;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .footer-box a:hover {
            color: #00aaff;
        }
        .footer-box .contact-info i {
            margin-right: 10px;
        }
        .footer-end {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .footer-end p {
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        .footer-tel-icons {
            margin-top: 10px;
        }
        .footer-tel-icons i {
            font-size: 1.2rem;
            margin: 0 10px;
            color: #2c3e50;
            transition: color 0.3s;
        }
        .footer-tel-icons i:hover {
            color: #00aaff;
        }
        .footer-tel-quicklink {
            text-align: center;
            margin-top: 10px;
        }
        .footer-tel-quicklink ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .footer-tel-quicklink a {
            color: #2c3e50;
            text-decoration: none;
            font-size: 0.8rem;
            transition: color 0.3s;
        }
        .footer-tel-quicklink a:hover {
            color: #00aaff;
        }
        #scrollBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #00aaff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 1.2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }
        #scrollBtn:hover {
            background: #0099e6;
        }

        /* Responsive Footer */
        @media (max-width: 768px) {
            .footer-box-main {
                flex-direction: column;
                text-align: center;
            }
            .footer-box {
                margin-bottom: 20px;
            }
            .footer-tel-quicklink ul {
                flex-direction: column;
                gap: 10px;
            }
        }
        @media (max-width: 480px) {
            .footer-box h3 { font-size: 1rem; }
            .footer-box a, .footer-box p { font-size: 0.85rem; }
            .footer-tel-icons i { font-size: 1rem; }
            .footer-tel-quicklink a { font-size: 0.75rem; }
            #scrollBtn { width: 35px; height: 35px; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="nav">
        <div class="nav-bar">
            <div class="nav-logo">
                <img src="<?php echo BASE_URL; ?>images/logo.png" alt="ToolsBuzz Logo">
            </div>
            <ul class="nav-menu">
                <li><a href="./home">Home</a></li>
                <li><a href="./tools">Tools <i class="fa-solid fa-chevron-down"></i></a></li>
                <li><a href="./blogs">Blogs</a></li>
                <li><a href="./contact">Contact</a></li>
            </ul>
            <div class="nav-menu-icon">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
        <div class="nav-menu-icon-phone">
            <div class="nav-menu-phone">
                <ul>
                    <li><a href="./home">Home</a></li>
                    <li><a href="./tools">Tools</a></li>
                    <li><a href="./blogs">Blogs</a></li>
                    <li><a href="./contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Remove Pages</h1>
    </div>
    <div class="preview-result">
        <div class="preview-thumbnails" id="preview"></div>
        <div class="remove-section">
            <h4>Remove Pages</h4>
            <p class="total-pages" id="total-pages">Total Pages: 0</p>
            <label>Pages to remove:</label>
            <input type="text" id="pagesToRemove" placeholder="e.g., 1, 5-10">
            <button id="btn" type="button">Remove Pages</button>
        </div>
    </div>
    <div class="result-container">
        <iframe id="viewer"></iframe>
    </div>

    <!-- Footer -->
    <div class="footer-main">
        <div class="footer-box-main">
            <div class="footer-box">
                <img src="<?php echo BASE_URL; ?>images/logo.png" alt="ToolsBuzz Logo" style="margin-bottom: 15px;">
            </div>
            <div class="footer-box">
                <h3>Visit Links</h3>
                <ul>
                    <li><a href="./home">Home</a></li>
                    <li><a href="./tools">Tools</a></li>
                    <li><a href="./blogs">Blogs</a></li>
                    <li><a href="./contact">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-box">
                <h3>Popular Tools</h3>
                <ul>
                    <li><a href="./wordcounter">Word Counter</a></li>
                    <li><a href="./privacy">Spell Checker</a></li>
                    <li><a href="./affiliate">JPG to PNG</a></li>
                    <li><a href="./blogs">PNG to PDF</a></li>
                    <li><a href="./privacy">PDF to JPG</a></li>
                    <li><a href="./affiliate">JPG to PDF</a></li>
                    <li><a href="./blogs">Add Watermark</a></li>
                </ul>
            </div>
            <div class="footer-box">
                <h3>Contact Us</h3>
                <ul class="contact-info">
                    <li><p><i class="fa-solid fa-location-dot"></i> Nischintapur, Budge Budge, Kolkata - 700 137, West Bengal, India</p></li>
                    <li><p><i class="fa-solid fa-envelope"></i> support@toolsbuzz.com</p></li>
                    <li><p><i class="fa-solid fa-phone"></i> +91 91287 78319</p></li>
                </ul>
            </div>
        </div>
        <div class="footer-end">
            <p>Copyright © 2025 by ToolsBuzz.com All Rights Reserved.</p>
            <div class="footer-tel-icons">
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-square-twitter"></i>
            </div>
        </div>
        <div class="footer-tel-quicklink">
            <ul>
                <li><a href="./terms">Terms of Services</a></li>
                <li><a href="./privacy">Privacy Policy</a></li>
                <li><a href="./affiliate">Disclaimer</a></li>
                <li><a href="./toolsbuzzfaq">FAQs</a></li>
            </ul>
        </div>
    </div>

    <button id="scrollBtn" onclick="scrollToTop()">↑</button>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const preview = document.getElementById('preview');
            const pagesToRemoveInput = document.getElementById('pagesToRemove');
            const totalPagesDisplay = document.getElementById('total-pages');
            const btn = document.getElementById('btn');
            const viewer = document.getElementById('viewer');
            let pdfDoc = null;
            let blobURL = null;
            const menuIcon = document.querySelector('.nav-menu-icon');
            const navMenuPhone = document.querySelector('.nav-menu-icon-phone');

            // Toggle Mobile Menu
            menuIcon.addEventListener('click', () => {
                navMenuPhone.classList.toggle('active');
            });

            // Get the PDF data from PHP (base64 encoded)
            const pdfData = '<?php echo $pdfData; ?>';
            if (pdfData) {
                const binary = atob(pdfData);
                const len = binary.length;
                const bytes = new Uint8Array(len);
                for (let i = 0; i < len; i++) {
                    bytes[i] = binary.charCodeAt(i);
                }
                pdfjsLib.getDocument(bytes).promise.then(doc => {
                    pdfDoc = doc;
                    totalPagesDisplay.textContent = `Total Pages: ${pdfDoc.numPages}`;
                    renderThumbnails();
                }).catch(err => {
                    console.error('Error loading PDF:', err);
                    alert('Failed to load the PDF.');
                });
            } else {
                alert('No PDF data available. Please upload a PDF.');
            }

            function renderThumbnails() {
                preview.innerHTML = '';
                for (let p = 1; p <= pdfDoc.numPages; p++) {
                    pdfDoc.getPage(p).then(page => {
                        const canvas = document.createElement('canvas');
                        const vp = page.getViewport({ scale: 0.4 });
                        canvas.width = vp.width;
                        canvas.height = vp.height;
                        page.render({ canvasContext: canvas.getContext('2d'), viewport: vp });

                        const thumbnail = document.createElement('div');
                        thumbnail.className = 'thumbnail';
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = p;
                        checkbox.id = `page_${p}`;
                        checkbox.addEventListener('change', updateInputFromCheckboxes);
                        const label = document.createElement('label');
                        label.htmlFor = `page_${p}`;
                        label.append(canvas);
                        const link = document.createElement('a');
                        link.href = '#';
                        link.textContent = `Page ${p}`;
                        link.onclick = (e) => {
                            e.preventDefault();
                            page.getViewport({ scale: 1.5 }).then(viewport => {
                                const canvasView = document.createElement('canvas');
                                canvasView.width = viewport.width;
                                canvasView.height = viewport.height;
                                page.render({ canvasContext: canvasView.getContext('2d'), viewport: viewport });
                                const img = canvasView.toDataURL('image/png');
                                window.open(img, '_blank');
                            });
                        };
                        thumbnail.append(checkbox, label, link);
                        preview.append(thumbnail);
                    });
                }
                updateInputFromCheckboxes();
            }

            function parsePageRange(range) {
                if (!range) return [];
                const pages = [];
                const totalPages = pdfDoc ? pdfDoc.numPages : 0;
                range.split(',').forEach(part => {
                    part = part.trim();
                    if (!part) return;
                    if (part.includes('-')) {
                        const [start, end] = part.split('-').map(num => parseInt(num.trim()));
                        if (isNaN(start) || isNaN(end)) return;
                        if (start < 1 || end > totalPages || start > end) return;
                        for (let i = start; i <= end; i++) pages.push(i);
                    } else {
                        const pageNum = parseInt(part);
                        if (isNaN(pageNum) || pageNum < 1 || pageNum > totalPages) return;
                        pages.push(pageNum);
                    }
                });
                return [...new Set(pages)].sort((a, b) => a - b);
            }

            function updateInputFromCheckboxes() {
                const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
                const selectedPages = Array.from(checkboxes).map(cb => parseInt(cb.value));
                pagesToRemoveInput.value = selectedPages.length > 0 ? selectedPages.join(', ') : '';
            }

            function updateCheckboxesFromInput() {
                const range = pagesToRemoveInput.value.trim();
                const pagesToCheck = parsePageRange(range);
                console.log('Parsed pages from input:', pagesToCheck); // Line 659
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    const pageNum = parseInt(checkbox.value);
                    checkbox.checked = pagesToCheck.includes(pageNum);
                });
            }

            pagesToRemoveInput.addEventListener('input', updateCheckboxesFromInput);

            btn.onclick = () => {
                btn.disabled = true;
                btn.textContent = 'Processing...';
                const range = pagesToRemoveInput.value.trim();
                const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
                let pagesToRemove = [];
                if (checkboxes.length > 0) pagesToRemove = Array.from(checkboxes).map(cb => parseInt(cb.value));
                if (range) {
                    const rangePages = parsePageRange(range);
                    pagesToRemove = [...new Set([...pagesToRemove, ...rangePages])];
                }
                if (pagesToRemove.length === 0) {
                    alert('Please select pages or enter a page range (e.g., 1, 5-10).');
                    btn.disabled = false;
                    btn.textContent = 'Remove Pages';
                    return;
                }
                if (!pdfData) {
                    alert('No PDF data available. Please upload a PDF.');
                    btn.disabled = false;
                    btn.textContent = 'Remove Pages';
                    return;
                }
                console.log('Sending data to process_remove.php:', { pdfDataLength: pdfData.length, pagesToRemove }); // Line 691
                const fd = new FormData();
                fd.append('pdfData', pdfData);
                fd.append('pagesToRemove', pagesToRemove.join(','));

                fetch('process_remove.php', { method: 'POST', body: fd })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.blob();
                    })
                    .then(b => {
                        if (blobURL) URL.revokeObjectURL(blobURL);
                        blobURL = URL.createObjectURL(b);
                        viewer.src = blobURL;
                        viewer.style.display = 'block';
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert('An error occurred while processing the PDF: ' + err.message);
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.textContent = 'Remove Pages';
                    });
            };

            // Scroll to Top
            window.onscroll = function() {
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    document.getElementById("scrollBtn").style.display = "block";
                } else {
                    document.getElementById("scrollBtn").style.display = "none";
                }
            };

            function scrollToTop() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }
        });
    </script>
</body>
</html>