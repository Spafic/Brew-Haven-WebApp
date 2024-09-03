// main.js

document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('header');
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('nav ul');
    const fadeElements = document.querySelectorAll('.fade-in');
    const parallaxElements = document.querySelectorAll('.parallax');

    // Sticky header
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.classList.add('sticky');
        } else {
            header.classList.remove('sticky');
        }
    });

    // Mobile menu toggle
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Fade-in animation
    const fadeInOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const fadeInObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, fadeInOptions);

    fadeElements.forEach(element => {
        fadeInObserver.observe(element);
    });

    // Parallax effect
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;

        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // Testimonial slider
    const testimonialSlider = document.querySelector('.testimonial-slider');
    let isDown = false;
    let startX;
    let scrollLeft;

    testimonialSlider.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - testimonialSlider.offsetLeft;
        scrollLeft = testimonialSlider.scrollLeft;
    });

    testimonialSlider.addEventListener('mouseleave', () => {
        isDown = false;
    });

    testimonialSlider.addEventListener('mouseup', () => {
        isDown = false;
    });

    testimonialSlider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - testimonialSlider.offsetLeft;
        const walk = (x - startX) * 2;
        testimonialSlider.scrollLeft = scrollLeft - walk;
    });
});


// Testimonial Carousel
const testimonialSlider = document.querySelector('.testimonial-slider');
const testimonialCards = document.querySelectorAll('.testimonial-card');
const sliderControls = document.querySelectorAll('.slider-control');
let currentIndex = 0;

function showTestimonial(index) {
    testimonialSlider.style.transform = `translateX(-${index * 100}%)`;
    sliderControls.forEach((control, i) => {
        control.classList.toggle('active', i === index);
    });
}

function nextTestimonial() {
    currentIndex = (currentIndex + 1) % testimonialCards.length;
    showTestimonial(currentIndex);
}

// Automatic sliding
setInterval(nextTestimonial, 5000);

// Manual controls
sliderControls.forEach((control, index) => {
    control.addEventListener('click', () => {
        currentIndex = index;
        showTestimonial(currentIndex);
    });
});

// Initial display
showTestimonial(currentIndex);



