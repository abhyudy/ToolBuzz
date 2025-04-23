$(document).ready(function () {
    // Navbar Menu Toggle
    const $menuIcon = $('#nav-menu-icon');
    const $menuIconOff = $('#nav-menu-icon-off');
    const $menuPhone = $('.nav-menu-icon-phone');

    $menuIcon.click(() => {
        $menuPhone.show();
        $menuIcon.hide();
        $menuIconOff.show();
    });

    $menuIconOff.click(() => {
        $menuPhone.hide();
        $menuIcon.show();
        $menuIconOff.hide();
    });

    //12: // Navbar Dropdown Data
    const dropdownData = [
        { title: 'Word Tool', links: ['Word Counter', 'Analyze Text', 'Word Density', 'Unique Words'] },
        { title: 'Image Tool', links: ['Png To Jpg', 'Jpg to Png', 'Jpg to Svg', 'Png To Svg', 'Crop Image', 'Background Remove', 'Resize Image'] },
        { title: 'Title Generator Tool', links: ['Generate Titles', 'Create Ideas', 'SEO Titles', 'Custom Titles'] },
        { title: 'CONVERT TO PDF', links: ['PNG To PDF', 'JPG to PDF', 'WORD to PDF', 'Powerpoint to pdf'] },
        { title: 'Paraphrasing Tool', links: ['Paraphrase', 'Avoid Plagiarism', 'Improve Flow', 'Rephrase Text'] },
        { title: 'Paragraph Rewriter Tool', links: ['Rewrite Paragraphs', 'Structure Text', 'Enhance Readability', 'Custom Edits'] },
        { title: 'View More Tool', links: ['Discover Tools', 'Expand Options', 'More Features', 'Explore Tools'] },
    ];

    // Dropdown Menu Interaction
    $('.nav-drop-tools-card-1 ul li').click(function () {
        const index = $(this).index();
        $('.nav-drop-tools-card-1 ul li').css('color', 'black');
        $(this).css('color', 'deepskyblue');

        const $toolBox = $('.nav-drop-tools-card-2-sub-tool-box');
        $('.nav-drop-tools-card-2-sub-tool h2').text(dropdownData[index].title);
        $toolBox.empty();
        dropdownData[index].links.forEach(link => {
            $toolBox.append(`<a href="./comingsoon">${link}</a>`);
        });
    });

    // Navbar Dropdown Toggle
    let isDropdownActive = false;
    $('.nav-menu li button').click(() => {
        const $dropdown = $('.nav-drop');
        const $icon = $('.nav-menu li button i');
        isDropdownActive = !isDropdownActive;

        $dropdown.toggle(isDropdownActive);
        $icon.toggleClass('fa-chevron-down', !isDropdownActive).toggleClass('fa-chevron-up', isDropdownActive);
    });

    // Upload Area Functionality
    const $uploadArea = $('.upload-area');
    const $pdfUpload = $('#pdfUpload');
    const $uploadForm = $('#upload-form');
    const $uploadError = $('#upload-error');

    $uploadArea.on('dragover', e => {
        e.preventDefault();
        $uploadArea.addClass('dragover');
    });

    $uploadArea.on('dragleave', () => {
        $uploadArea.removeClass('dragover');
    });

    $uploadArea.on('drop', async e => {
        e.preventDefault();
        $uploadArea.removeClass('dragover');
        const file = e.originalEvent.dataTransfer.files[0];
        if (file && file.type === 'application/pdf') {
            await handlePdfUpload(file);
        } else {
            $uploadError.text('Please drop a valid PDF file.').show();
        }
    });

    $uploadForm.on('submit', async e => {
        e.preventDefault();
        const file = $pdfUpload[0].files[0];
        if (file) {
            await handlePdfUpload(file);
        } else {
            $uploadError.text('Please select a PDF file.').show();
        }
    });

    async function handlePdfUpload(file) {
        const formData = new FormData();
        formData.append('pdfFile', file);

        try {
            const response = await fetch('<?php echo BASE_URL; ?>upload_temp.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = `<?php echo BASE_URL; ?>remove_pages.php?token=${encodeURIComponent(data.token)}`;
            } else {
                $uploadError.text(`Error uploading file: ${data.error}`).show();
            }
        } catch (error) {
            $uploadError.text('Error uploading file. Check console for details.').show();
            console.error('Error:', error);
        }
    }

    // Scroll to Top
    const $scrollBtn = $('#scrollBtn');
    $(window).scroll(() => {
        $scrollBtn.toggle(document.body.scrollTop > 100 || document.documentElement.scrollTop > 100);
    });

    $scrollBtn.click(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});