<?php
include_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toolsbuzz - PDF Page Remover</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>PDF Page Remover</h1>
        <h3>Remove Unwanted Pages from Your PDF Instantly</h3>
        <p>Quickly delete specific pages from your PDF without installing any software.</p>
    </div>

    <div class="box">
        <div class="upload-area" id="uploadArea">
            <i class="fa-solid fa-file-pdf pdf-icon"></i>
            <p class="file-info">Ensure your PDF does not exceed 10MB!</p>
            <form id="frm" enctype="multipart/form-data" action="remove_pageDetails.php" method="post">
                <input type="file" id="file" name="pdfFile" accept="application/pdf" required>
                <div class="button-group">
                    <label for="file">Select PDF File</label>
                </div>
                <p>or drop PDF here</p>
            </form>
            <div id="pdfPreview" class="preview-container"></div>
        </div>
    </div>

    <!-- Detail Section -->
    <div class="detail-section">
        <div class="left-section">
            <h2>PDF Page Remover Tool</h2> <!-- Changed title to match functionality -->
            <p>Our PDF Page Remover allows you to easily delete specific pages from your PDF documents. Simply upload your file and select which pages to remove.</p>
            <p>This tool is perfect for when you need to extract specific content, remove sensitive information, or reduce file size by eliminating unnecessary pages.</p>
        </div>

        <div class="right-section">
            <div class="image-container">
                <img src="./images/image.png" alt="PDF Page Remover Illustration" class="feature-image">
                <div class="blue-rectangle"></div>
            </div>
        </div>
    </div>

    <!-- Calculator Work Section -->
    <div class="calculator-work">
        <div class="work-intro">
            <h2>How Does PDF Page Remover Work?</h2> 
            <p>Upload your PDF and follow these simple steps to remove unwanted pages.</p>
        </div>
        <div class="work-box">
            <div class="work-item">
                <i class="fa-solid fa-check"></i>
                <h4>Upload Your PDF</h4> 
                <p>Select your PDF file by clicking the upload button or dragging and dropping it into the designated area.</p>
            </div>
            <div class="work-item">
                <i class="fa-solid fa-check"></i>
                <h4>Select Pages to Remove</h4> 
                <p>Choose which pages you want to delete from your document using our intuitive interface.</p>
            </div>
            <div class="work-item">
                <i class="fa-solid fa-check"></i>
                <h4>Download Your File</h4> 
                <p>Get your modified PDF instantly, with the selected pages removed and ready for use.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

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