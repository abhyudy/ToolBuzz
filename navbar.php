<?php
include_once 'config.php';
?>
<!-- Navbar -->
<div class="nav">
    <div class="nav-bar">
        <div class="nav-logo">
            <h2>Toolsbuzz</h2>
        </div>
        <ul class="nav-menu">
            <li><a href="./home">Home</a></li>
            <li class="dropdown">
                <a href="./tools">Tools <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="./pdf-tools">PDF Tools</a></li>
                    <li><a href="./image-tools">Image Tools</a></li>
                    <li><a href="./seo-tools">SEO Tools</a></li>
                </ul>
            </li>
            <li><a href="./blogs">Blogs</a></li>
            <li><a href="./contact">Contact</a></li>
        </ul>
        <div class="nav-menu-icon">
            <i class="fa-solid fa-bars"></i>
        </div>
    </div>
    <div class="nav-menu-mobile">
        <ul>
            <li><a href="./home">Home</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)">Tools <i class="fa-solid fa-chevron-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="./pdf-tools">PDF Tools</a></li>
                    <li><a href="./image-tools">Image Tools</a></li>
                    <li><a href="./seo-tools">SEO Tools</a></li>
                </ul>
            </li>
            <li><a href="./blogs">Blogs</a></li>
            <li><a href="./contact">Contact</a></li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Safely initialize navbar functionality only if elements exist
    try {
        // Mobile menu toggle
        const menuIcon = document.querySelector('.nav-menu-icon');
        const mobileMenu = document.querySelector('.nav-menu-mobile');
        
        if (menuIcon && mobileMenu) {
            menuIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle('active');
                // Toggle icon between bars and times
                const icon = this.querySelector('i');
                if (icon) {
                    if (mobileMenu.classList.contains('active')) {
                        icon.classList.replace('fa-bars', 'fa-times');
                    } else {
                        icon.classList.replace('fa-times', 'fa-bars');
                    }
                }
            });

            // Mobile dropdown toggle
            const dropdownToggles = document.querySelectorAll('.nav-menu-mobile .dropdown > a');
            
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const dropdown = this.parentElement;
                    if (!dropdown) return;
                    
                    const chevron = this.querySelector('i');
                    
                    dropdown.classList.toggle('active');
                    
                    // Rotate chevron icon
                    if (chevron) {
                        chevron.style.transform = dropdown.classList.contains('active') ? 
                            'rotate(180deg)' : 'rotate(0deg)';
                    }
                    
                    // Close other open dropdowns
                    document.querySelectorAll('.nav-menu-mobile .dropdown')
                        .forEach(item => {
                            if (item !== dropdown && item.classList) {
                                item.classList.remove('active');
                                const otherChevron = item.querySelector('i');
                                if (otherChevron) otherChevron.style.transform = 'rotate(0deg)';
                            }
                        });
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-bar') && !e.target.closest('.nav-menu-mobile')) {
                    mobileMenu.classList.remove('active');
                    const icon = document.querySelector('.nav-menu-icon i');
                    if (icon) {
                        icon.classList.replace('fa-times', 'fa-bars');
                    }
                    // Close all dropdowns
                    document.querySelectorAll('.nav-menu-mobile .dropdown')
                        .forEach(item => {
                            if (item.classList) {
                                item.classList.remove('active');
                                const chevron = item.querySelector('i');
                                if (chevron) chevron.style.transform = 'rotate(0deg)';
                            }
                        });
                }
            });
        }
    } catch (error) {
        console.error('Navbar initialization error:', error);
    }
});
</script>