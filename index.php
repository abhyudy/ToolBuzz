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
    <style>
        /* Navbar Styles */
        .nav {
            background: rgba(238, 248, 251, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 109px;
            max-width: 1240px;
            margin: auto;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .nav-bar {
            width: 1240px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .nav-logo img {
            height: 40px;
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
            margin-left: 50px;
        }

        .nav-menu li a {
            color: #2c3e50;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            text-transform: capitalize;
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

        @media (max-width: 1240px) {
            .nav-bar {
                width: 100%;
                padding: 0 20px;
            }
        }

        @media (max-width: 768px) {
            .nav {
                height: 80px;
            }

            .nav-menu {
                display: none;
            }

            .nav-menu-icon {
                display: block;
            }

            .nav-menu-icon-phone.active .nav-menu-phone {
                display: block;
                position: absolute;
                top: 80px;
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
                font-weight: 500;
                text-transform: capitalize;
            }

            .nav-menu-phone a:hover {
                color: #00aaff;
            }
        }

        /* Main Content Styles */
        body {
            background: linear-gradient(135deg, #e6f0fa 0%, #f0f7ff 100%);
            position: relative;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            max-width: 1440px;
            font-family: Arial, sans-serif;
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
            max-width: 1240px;
            margin: 40px auto;
            text-align: center;
            padding: 0 20px;
        }

        .container h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .container h3 {
            font-size: 1.2rem;
            color: #666;
            margin: 10px 0;
        }

        .container p {
            font-size: 1rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        /* Upload Box */
        .box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 1040px;
            height: 300px;
            margin: 0 auto 20px;
        }

        .upload-area {
            text-align: center;
            border: none;
            padding: 20px;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .upload-area .button-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .upload-area label {
            background: #00aaff;
            color: white;
            padding: 12px 40px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            margin: 15px 0;
            transition: background 0.3s;
            font-size: 1.1rem;
            display: inline-block;
            white-space: nowrap;
            text-align: center;
        }

        .upload-area label:hover {
            background: #0099e6;
        }

        .upload-area .icon-wrapper {
            display: flex;
            flex-direction: row;
            gap: 10px;
            align-items: center;
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

        .upload-area .pdf-icon {
            font-size: 50px;
            color: #666;
            margin-bottom: 15px;
        }

        .upload-area .file-info {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        /* New Age Calculator Work Section */
        .calculator-work {
            background: rgb(132, 173, 255);
            padding: 20px 0;
            text-align: center;
            margin-bottom: 40px;
            max-width: 1440px;
            height: 508px;
        }

        .calculator-work h2 {
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .calculator-work p {
            color: #666;
            font-size: 1rem;
            margin-bottom: 40px;
        }

        .work-box {
            background: #007bff;
            border-radius: 15px;
            padding: 30px;
            max-width: 1050px;
            margin: 0 auto;
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
        }

        .work-item {
            text-align: center;
            flex: 1;
            max-width: 300px;
            color: white;
        }

        .work-item i {
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
            background: #e6f0fa;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 40px;
            display: inline-block;
        }

        .work-item h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .work-item p {
            font-size: 0.9rem;
            line-height: 1.5;
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
            height: 260px;
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

        .footer-box a,
        .footer-box p {
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

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .container {
                margin-top: 30px;
            }

            .container h1 {
                font-size: 2.2rem;
            }

            .container h3 {
                font-size: 1.1rem;
            }

            .container p {
                font-size: 0.95rem;
            }

            .box {
                padding: 30px;
            }

            .work-box {
                padding: 20px;
            }

            .work-item {
                max-width: 250px;
            }

            .work-item i {
                font-size: 1.3rem;
                width: 35px;
                height: 35px;
                line-height: 35px;
            }

            .work-item h4 {
                font-size: 1.1rem;
            }

            .work-item p {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
            }

            .container h1 {
                font-size: 2rem;
            }

            .container h3 {
                font-size: 1rem;
            }

            .container p {
                font-size: 0.9rem;
            }

            .box {
                padding: 20px;
                margin: 0 10px 20px;
            }

            .upload-area {
                padding: 15px;
            }

            .upload-area label {
                padding: 10px 30px;
                font-size: 1rem;
            }

            .upload-area .button-group {
                gap: 15px;
            }

            .upload-area .icon-wrapper svg {
                width: 20px;
                height: 20px;
            }

            .upload-area .pdf-icon {
                font-size: 40px;
            }

            .upload-area .file-info {
                font-size: 0.85rem;
            }

            .calculator-work h2 {
                font-size: 1.3rem;
            }

            .calculator-work p {
                font-size: 0.9rem;
            }

            .work-box {
                flex-direction: column;
                padding: 15px;
            }

            .work-item {
                margin-bottom: 20px;
                max-width: 100%;
            }

            .work-item i {
                font-size: 1.2rem;
                width: 30px;
                height: 30px;
                line-height: 30px;
            }

            .work-item h4 {
                font-size: 1rem;
            }

            .work-item p {
                font-size: 0.8rem;
            }

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
            .container {
                margin-top: 15px;
            }

            .container h1 {
                font-size: 1.8rem;
            }

            .container h3 {
                font-size: 0.9rem;
            }

            .container p {
                font-size: 0.85rem;
            }

            .box {
                padding: 15px;
                margin: 0 5px 15px;
            }

            .upload-area {
                padding: 10px;
            }

            .upload-area label {
                padding: 8px 20px;
                font-size: 0.9rem;
            }

            .upload-area .button-group {
                gap: 10px;
                flex-direction: row;
            }

            .upload-area .icon-wrapper svg {
                width: 18px;
                height: 18px;
            }

            .upload-area .pdf-icon {
                font-size: 30px;
            }

            .upload-area .file-info {
                font-size: 0.8rem;
            }

            .calculator-work h2 {
                font-size: 1.2rem;
            }

            .calculator-work p {
                font-size: 0.85rem;
            }

            .work-item i {
                font-size: 1rem;
                width: 25px;
                height: 25px;
                line-height: 25px;
            }

            .work-item h4 {
                font-size: 0.9rem;
            }

            .work-item p {
                font-size: 0.75rem;
            }

            .footer-box h3 {
                font-size: 1rem;
            }

            .footer-box a,
            .footer-box p {
                font-size: 0.85rem;
            }

            .footer-tel-icons i {
                font-size: 1rem;
            }

            .footer-tel-quicklink a {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 360px) {
            .container h1 {
                font-size: 1.5rem;
            }

            .container h3 {
                font-size: 0.8rem;
            }

            .container p {
                font-size: 0.8rem;
            }

            .upload-area label {
                padding: 6px 15px;
                font-size: 0.8rem;
            }

            .calculator-work h2 {
                font-size: 1.1rem;
            }

            .calculator-work p {
                font-size: 0.8rem;
            }

            .work-item i {
                font-size: 0.9rem;
                width: 20px;
                height: 20px;
                line-height: 20px;
            }

            .work-item h4 {
                font-size: 0.8rem;
            }

            .work-item p {
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="nav">
        <div class="nav-bar">
            <div class="nav-logo">
                <img src="https://via.placeholder.com/150x40?text=ToolsBuzz+Logo" alt="ToolsBuzz Logo">
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
        <h1>PDF Page Remover</h1>
        <h3>Remove Unwanted Pages from Your PDF Instantly</h3>
        <p>Quickly delete specific pages from your PDF without installing any software.</p>
    </div>
    <div class="box">
        <div class="upload-area">
            <i class="fa-solid fa-file-pdf pdf-icon"></i>
            <p class="file-info">Ensure your PDF does not exceed 10MB!</p>
            <form id="frm" enctype="multipart/form-data" action="remove_pageDetails.php" method="post">
                <input type="file" id="file" name="pdfFile" accept="application/pdf" required>
                <div class="button-group">
                    <label for="file">Select PDF File</label>
                </div>
                <p>or drop PDF here</p>
            </form>
        </div>
    </div>

    <!-- New Age Calculator Work Section -->
    <div class="calculator-work">
        <h2>How Does Age Calculator Work?</h2>
        <p>The Word Counter tool instantly analyzes your text, providing word, character, <br> and sentence counts to improve readability and SEO.</p>
        <div class="work-box">
            <div class="work-item">
                <i class="fa-solid fa-check"></i>
                <h4>Enter Your Text</h4>
                <p>You can type your text directly into the input box on our website. <br>
                Alternatively, you can copy text from another document and paste it into the input box.</p>
            </div>
            <div class="work-item">
                <i class="fa-solid fa-check"></i>
                <h4>Real-Time Updates</h4>
                <p>See your word and character counts update instantly as you type. <br>
                This real-time feature helps you keep track of your progress as you stay on target, whether you're writing a report, online post or essay.</p>
            </div>
            <div class="work-item">
                <i class="fa-solid fa-check"></i>
                <h4>Detailed Analysis</h4>
                <p>Word Counter provides detailed statistics on your text. Analyze sentence length, paragraph length, and average word length to gain insights into your writing style and improve your work.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-main">
        <div class="footer-box-main">
            <div class="footer-box">
                <img src="https://via.placeholder.com/100x40?text=ToolsBuzz+Logo" alt="ToolsBuzz Logo" style="margin-bottom: 15px;">
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
                    <li>
                        <p><i class="fa-solid fa-location-dot"></i> Nischintapur, Budge Budge, Kolkata - 700 137, West Bengal, India</p>
                    </li>
                    <li>
                        <p><i class="fa-solid fa-envelope"></i> support@toolsbuzz.com</p>
                    </li>
                    <li>
                        <p><i class="fa-solid fa-phone"></i> +91 91287 78319</p>
                    </li>
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

   <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>script.js"></script>
    <script>
        const file = document.getElementById('file');
        const menuIcon = document.querySelector('.nav-menu-icon');
        const navMenuPhone = document.querySelector('.nav-menu-icon-phone');

        // Toggle Mobile Menu
        menuIcon.addEventListener('click', () => {
            navMenuPhone.classList.toggle('active');
        });

        // Drag and drop support
        const uploadArea = document.querySelector('.upload-area');
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
        });
        uploadArea.addEventListener('dragleave', () => {});
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
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