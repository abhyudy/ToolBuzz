<?php
include_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToolsBuzz - PDF Page Remover</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles.css">
    <style>
        /* Background and container styles */
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
        .container h3 {
            font-size: 1.2rem;
            color: #666;
            margin: 10px 0;
        }

        /* Upload box */
        .box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto 30px;
        }
        .upload-area {
            text-align: center;
        }
        .upload-area input[type="file"] {
            display: none;
        }
        .upload-area .button-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .upload-area label {
            background: #00aaff;
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            margin: 15px 0;
            transition: background 0.3s;
            font-size: 1.1rem;
            display: inline-block;
            white-space: nowrap;
        }
        .upload-area label:hover {
            background: #0099e6;
        }
        .upload-area .icon-wrapper {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .upload-area .icon-wrapper svg {
            width: 24px;
            height: 24px;
            transition: transform 0.2s;
        }
        .upload-area .icon-wrapper svg:hover {
            transform: scale(1.1);
        }
        .upload-area p {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .pdf-icon {
            font-size: 40px;
            color: #333;
            margin-bottom: 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                margin-top: 100px;
            }
            .container h1 {
                font-size: 2rem;
            }
            .container h3 {
                font-size: 1rem;
            }
            .box {
                padding: 20px;
            }
            .upload-area label {
                padding: 10px 15px;
                font-size: 1rem;
            }
            .upload-area .button-group {
                gap: 8px;
            }
            .upload-area .icon-wrapper svg {
                width: 20px;
                height: 20px;
            }
        }
        @media (max-width: 480px) {
            .upload-area label {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            .upload-area .button-group {
                gap: 6px;
            }
            .upload-area .icon-wrapper svg {
                width: 18px;
                height: 18px;
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
        <h1>PDF Page Remover</h1>
        <h3>Remove Unwanted Pages from Your PDF Instantly</h3>
    </div>
    <div class="box">
        <div class="upload-area">
            <i class="fa-solid fa-file-pdf pdf-icon"></i>
            <form id="frm" enctype="multipart/form-data" action="remove_pageDetails.php" method="post">
                <input type="file" id="file" name="pdfFile" accept="application/pdf" required>
                <div class="button-group">
                    <label for="file">Select PDF File</label>
                    <div class="icon-wrapper">
                        <!-- Google Drive Icon (SVG) -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11" fill="#00aaff"/>
                            <path d="M12 4L7 10H10.5L9.5 14L12 12L14.5 14L13.5 10H17L12 4Z" fill="white"/>
                        </svg>
                        <!-- Dropbox Icon (SVG) -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11" fill="#00aaff"/>
                            <path d="M7 10L12 4L17 10L14 12L17 14L12 12L7 14L10 12L7 10Z" fill="white"/>
                        </svg>
                    </div>
                </div>
                <p>or drop PDF here</p>
            </form>
        </div>
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
        const file = document.getElementById('file');

        // Drag and drop support
        const uploadArea = document.querySelector('.upload-area');
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            file.files = e.dataTransfer.files;
            document.getElementById('frm').submit();
        });

        // Submit form on file change
        file.addEventListener('change', () => {
            const f = file.files[0];
            if (!f) return;
            if (f.size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit.');
                file.value = '';
                return;
            }
            if (f.type !== 'application/pdf') {
                alert('Please upload a PDF file.');
                file.value = '';
                return;
            }
            document.getElementById('frm').submit();
        });
    </script>
</body>
</html>