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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #e6f0fa 0%, #f0f7ff 100%);
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
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
        }
        .container h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
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
        }
        .thumbnail canvas {
            max-width: 100%;
            max-height: 100%;
        }
        .thumbnail input[type="checkbox"] {
            position: absolute;
            top: 5px;
            left: 5px;
        }
        .thumbnail a {
            text-decoration: none;
            color: #333;
            font-size: 0.9rem;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
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

        @media (max-width: 768px) {
            .container {
                margin-top: 100px;
            }
            .container h1 {
                font-size: 2rem;
            }
            .preview-result {
                flex-direction: column;
                align-items: center;
                padding: 15px;
            }
            .preview-thumbnails {
                justify-content: center;
                max-width: 100%;
            }
            .thumbnail {
                width: 120px;
                height: 160px;
            }
            .remove-section {
                text-align: center;
                margin-top: 20px;
                min-width: 0;
                padding-left: 0;
            }
            .remove-section input[type="text"] {
                width: 100%;
            }
            #viewer {
                height: 400px;
            }
        }
        @media (max-width: 480px) {
            .thumbnail {
                width: 100px;
                height: 130px;
            }
            .remove-section button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="nav">
        <div class="nav-bar">
            <div class="nav-logo">
                <a href="./home"><span class="logo-text">ToolsBuzz</span></a>
            </div>
            <div class="nav-menu">
                <li><a href="./home" id="nav-menu-homemain">Home</a></li>
                <li><a href="./tools" id="tools">Tools</a> <button onclick="on_of_button(-1)"> <i class="fa-solid fa-chevron-down"></i></button></li>
                <li><a href="./blogs" id="nav-menu-blogs">Blogs</a></li>
                <li><a href="./contact" id="nav-menu-contact">Contact</a></li>
            </div>
            <div class="nav-menu-icon">
                <i class="fa-solid fa-bars" id="nav-menu-icon"></i>
                <i class="fa-solid fa-xmark" id="nav-menu-icon-off"></i>
            </div>
        </div>
        <div class="nav-menu-icon-phone">
            <div class="nav-menu-phone">
                <ul>
                    <li><a href="./home"><p>Home</p></a></li>
                    <li><a href="./tools"><p>Tools</p></a></li>
                    <li><a href="./blogs"><p>Blogs</p></a></li>
                    <li><a href="./about"><p>About Us</p></a></li>
                    <li><a href="./contact"><p>Contact Us</p></a></li>
                </ul>
            </div>
        </div>
        <div class="nav-drop">
            <div class="nav-drop-tools">
                <div class="nav-drop-tools-card">
                    <div class="nav-drop-tools-card-0">
                        <i class="fa-solid fa-tools"></i>
                        <h2>Tools</h2>
                    </div>
                    <div class="nav-drop-tools-card-1">
                        <ul>
                            <li><p>Word Tool</p></li>
                            <li><p>Image Tools</p></li>
                            <li><p>Title Generator Tool</p></li>
                            <li><p>CONVERT TO PDF</p></li>
                            <li><p>CONVERT FROM PDF</p></li>
                            <li><p>Instagram Tool</p></li>
                            <li><p>Facebook Tools</p></li>
                        </ul>
                    </div>
                    <div class="nav-drop-tools-card-2">
                        <div class="nav-drop-tools-card-2-sub">
                            <div class="nav-drop-tools-card-2-sub-tool">
                                <h2>Word Tools</h2>
                                <div class="nav-drop-tools-card-2-sub-tool-box">
                                    <a href="./wordcounter">Word Counter</a>
                                    <a href="./comingsoon">Analyze Text</a>
                                    <a href="./comingsoon">Word Density</a>
                                    <a href="./comingsoon">Unique Words</a>
                                    <a href="./tools"><p>All Tools</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

    <!-- Footer (Unchanged) -->
    <div class="footer-main">
        <footer>
            <button id="scrollBtn" onclick="scrollToTop()"> <i class="fa-solid fa-arrow-up fa-bounce"></i></button>
        </footer>
        <div class="footer-box-main">
            <div class="footer-box">
                <div class="footer-box-1">
                    <div class="footer-box-1-title">
                        <span class="logo-text">ToolsBuzz</span>
                    </div>
                </div>
                <div class="footer-box-2">
                    <ul>
                        <li><h3>Visit Links</h3></li>
                        <li><a href="./home">Home</a></li>
                        <li><a href="./about">About us</a></li>
                        <li><a href="./contact">Contact Us</a></li>
                        <li><a href="./tools">Tools</a></li>
                        <li><a href="./blogs">Blogs</a></li>
                    </ul>
                    <ul>
                        <li><h3>Popular Tools</h3></li>
                        <li><a href="./wordcounter">Word Counter</a></li>
                        <li><a href="./privacy">Spell Checker</a></li>
                        <li><a href="./affiliate">JPG to PNG</a></li>
                        <li><a href="./blogs">PNG to PDF</a></li>
                        <li><a href="./privacy">PDF to JPG</a></li>
                        <li><a href="./affiliate">JPG to PDF</a></li>
                        <li><a href="./blogs">Add Watermark</a></li>
                    </ul>
                </div>
                <div class="footer-box-1">
                    <div class="footer-box-1-content">
                        <ul>
                            <li><h3>Get in Touch</h3></li>
                            <li><p><i class="fa-solid fa-phone"></i> +91 91287 78319</p></li>
                            <li><p><i class="fa-solid fa-envelope"></i> support@toolsbuzz.com</p></li>
                            <li><p><i class="fa-sharp fa-solid fa-location-dot"></i> Nischintapur, Budge Budge, Kolkata - 700 137, West Bengal, India</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-end">
            <div class="footer-tel">
                <div class="footer-tel-text">
                    <p>Copyright © 2025 by ToolsBuzz.com All Rights Reserved.</p>
                </div>
                <div class="footer-tel-icons">
                    <i class="fa-brands fa-instagram"></i>
                    <i class="fa-brands fa-facebook"></i>
                    <i class="fa-brands fa-square-twitter"></i>
                </div>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>script.js"></script>
    <script>
        const preview = document.getElementById('preview');
        const pagesToRemoveInput = document.getElementById('pagesToRemove');
        const totalPagesDisplay = document.getElementById('total-pages');
        const btn = document.getElementById('btn');
        const viewer = document.getElementById('viewer');
        let pdfDoc = null;
        let blobURL = null;

        // Get the PDF data from PHP (base64 encoded)
        const pdfData = '<?php echo $pdfData; ?>';
        if (pdfData) {
            // Decode base64 to binary
            const binary = atob(pdfData);
            const len = binary.length;
            const bytes = new Uint8Array(len);
            for (let i = 0; i < len; i++) {
                bytes[i] = binary.charCodeAt(i);
            }

            // Load the PDF using pdfjsLib
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

        // Render thumbnails with checkboxes
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
        }

        // Parse page range input (e.g., "1", "5-10")
        function parsePageRange(range) {
            const pages = [];
            range.split(',').forEach(part => {
                part = part.trim();
                if (part.includes('-')) {
                    const [start, end] = part.split('-').map(Number);
                    for (let i = start; i <= end; i++) pages.push(i);
                } else {
                    pages.push(Number(part));
                }
            });
            return [...new Set(pages)].sort((a, b) => a - b); // Remove duplicates and sort
        }

        // Submit to server with selected pages
        btn.onclick = () => {
            btn.disabled = true;
            btn.textContent = 'Processing...';
            const range = pagesToRemoveInput.value.trim();
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            let pagesToRemove = [];

            // Handle checkbox selection
            if (checkboxes.length > 0) {
                pagesToRemove = Array.from(checkboxes).map(cb => parseInt(cb.value));
            }
            // Handle page range input
            if (range) {
                const rangePages = parsePageRange(range);
                pagesToRemove = [...new Set([...pagesToRemove, ...rangePages])]; // Merge and remove duplicates
            }

            // Validate pagesToRemove
            if (pagesToRemove.length === 0) {
                alert('Please select pages or enter a page range (e.g., 1, 5-10).');
                btn.disabled = false;
                btn.textContent = 'Remove Pages';
                return;
            }

            // Validate pdfData
            if (!pdfData) {
                alert('No PDF data available. Please upload a PDF.');
                btn.disabled = false;
                btn.textContent = 'Remove Pages';
                return;
            }

            // Log the data being sent
            console.log('Sending data to process_remove.php:', {
                pdfDataLength: pdfData.length,
                pagesToRemove: pagesToRemove
            });

            const fd = new FormData();
            fd.append('pdfData', pdfData);
            fd.append('pagesToRemove', pagesToRemove.join(','));

            fetch('process_remove.php', { method: 'POST', body: fd })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
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
    </script>
</body>
</html>