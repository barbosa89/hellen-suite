"use strict"; // Start of use strict

import './common.js'

// Toggle the side navigation
document.getElementById("sidebarToggle").addEventListener('click', function (e) {
    e.preventDefault();
    document.body.classList.toggle("sidebar-toggled");
    document.querySelector(".sidebar").classList.toggle("toggled");
});

// Prevent the content wrapper from scrolling when the fixed side navigation is hovered over
document.querySelector('.sidebar').addEventListener('wheel', function (e) {
    if (window.innerWidth > 768) {
        const delta = e.deltaY || -e.wheelDelta;
        this.scrollTop += (delta > 0 ? 1 : -1) * 30;
        e.preventDefault();
    }
});

// Scroll to top button appear
document.addEventListener('scroll', function () {
    const scrollDistance = document.documentElement.scrollTop || document.body.scrollTop;
    const scrollToTopButton = document.querySelector('.scroll-to-top');
    if (scrollDistance > 100) {
        scrollToTopButton.style.display = 'block';
        scrollToTopButton.style.opacity = '1';
    } else {
        scrollToTopButton.style.display = 'none';
        scrollToTopButton.style.opacity = '0';
    }
});

// Smooth scrolling
document.querySelectorAll('a.scroll-to-top').forEach(anchor => {
    anchor.addEventListener('click', function (event) {
        event.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);

        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop,
                behavior: 'smooth'
            });
        }
    });
});