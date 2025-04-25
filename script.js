// Navbar toggle
document.getElementById('nav-menu-icon').addEventListener('click', function() {
    document.querySelector('.nav-menu-icon-phone').style.display = 'block';
    document.getElementById('nav-menu-icon').style.display = 'none';
    document.getElementById('nav-menu-icon-off').style.display = 'block';
});

document.getElementById('nav-menu-icon-off').addEventListener('click', function() {
    document.querySelector('.nav-menu-icon-phone').style.display = 'none';
    document.getElementById('nav-menu-icon').style.display = 'block';
    document.getElementById('nav-menu-icon-off').style.display = 'none';
});

function on_of_button(index) {
    const navDrop = document.querySelector('.nav-drop');
    navDrop.style.display = navDrop.style.display === 'block' ? 'none' : 'block';
}

// Scroll to top
window.onscroll = function() {
    document.getElementById('scrollBtn').style.display = document.documentElement.scrollTop > 200 ? 'block' : 'none';
};

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}