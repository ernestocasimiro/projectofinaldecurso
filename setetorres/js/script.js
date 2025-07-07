// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Preloader
    const preloader = document.querySelector('.preloader');
    
    window.addEventListener('load', function() {
        setTimeout(() => {
            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
            document.body.style.overflow = 'visible';
        }, 1500);
    });

    // Mobile Navigation Toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
            
            // Toggle hamburger animation
            const bars = hamburger.querySelectorAll('.bar');
            if (hamburger.classList.contains('active')) {
                bars[0].style.transform = 'rotate(-45deg) translate(-5px, 6px)';
                bars[1].style.opacity = '0';
                bars[2].style.transform = 'rotate(45deg) translate(-5px, -6px)';
            } else {
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            }
        });
    }

    // Close mobile menu when a link is clicked
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
            
            // Reset hamburger animation
            const bars = hamburger.querySelectorAll('.bar');
            bars[0].style.transform = 'none';
            bars[1].style.opacity = '1';
            bars[2].style.transform = 'none';
        });
    });

    // Sticky Header
    const header = document.querySelector('header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            header.style.background = 'rgba(15, 15, 15, 0.95)';
            header.style.padding = '15px 0';
        } else {
            header.style.background = 'rgba(15, 15, 15, 0.9)';
            header.style.padding = '20px 0';
        }
        
        lastScrollTop = scrollTop;
    });

    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Menu Carousel
    const menuSlides = document.querySelectorAll('.menu-slide');
    const menuDots = document.querySelectorAll('.carousel-dots .dot');
    const prevBtn = document.querySelector('.menu-preview .prev-btn');
    const nextBtn = document.querySelector('.menu-preview .next-btn');
    let currentSlide = 0;

    function showMenuSlide(index) {
        const carousel = document.querySelector('.menu-carousel');
        carousel.style.transform = `translateX(-${index * 100}%)`;
        
        menuDots.forEach(dot => dot.classList.remove('active'));
        menuDots[index].classList.add('active');
        
        currentSlide = index;
    }

    if (menuDots.length > 0) {
        menuDots.forEach((dot, index) => {
            dot.addEventListener('click', () => showMenuSlide(index));
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentSlide = (currentSlide - 1 + menuSlides.length) % menuSlides.length;
            showMenuSlide(currentSlide);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentSlide = (currentSlide + 1) % menuSlides.length;
            showMenuSlide(currentSlide);
        });
    }

    // Auto-rotate menu slides
    if (menuSlides.length > 0) {
        setInterval(() => {
            currentSlide = (currentSlide + 1) % menuSlides.length;
            showMenuSlide(currentSlide);
        }, 6000);
    }

    // Testimonial Slider
    const testimonialSlides = document.querySelectorAll('.testimonial-slide');
    const testimonialDots = document.querySelectorAll('.testimonial-dots .dot');
    const testimonialPrev = document.querySelector('.testimonials .prev-btn');
    const testimonialNext = document.querySelector('.testimonials .next-btn');
    let currentTestimonial = 0;

    function showTestimonial(index) {
        testimonialSlides.forEach(slide => slide.classList.remove('active'));
        testimonialDots.forEach(dot => dot.classList.remove('active'));
        
        testimonialSlides[index].classList.add('active');
        testimonialDots[index].classList.add('active');
        
        currentTestimonial = index;
    }

    if (testimonialDots.length > 0) {
        testimonialDots.forEach((dot, index) => {
            dot.addEventListener('click', () => showTestimonial(index));
        });
    }

    if (testimonialPrev) {
        testimonialPrev.addEventListener('click', function() {
            currentTestimonial = (currentTestimonial - 1 + testimonialSlides.length) % testimonialSlides.length;
            showTestimonial(currentTestimonial);
        });
    }

    if (testimonialNext) {
        testimonialNext.addEventListener('click', function() {
            currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length;
            showTestimonial(currentTestimonial);
        });
    }

    // Auto-rotate testimonials
    if (testimonialSlides.length > 0) {
        setInterval(() => {
            currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length;
            showTestimonial(currentTestimonial);
        }, 7000);
    }

    // Accolades Slider
    const accoladeSlides = document.querySelectorAll('.accolade-slide');
    const accoladeDots = document.querySelectorAll('.accolade-dot');
    const accoladePrev = document.querySelector('.accolade-prev');
    const accoladeNext = document.querySelector('.accolade-next');
    let currentAccolade = 0;

    function showAccolade(index) {
        accoladeSlides.forEach(slide => slide.classList.remove('active'));
        accoladeDots.forEach(dot => dot.classList.remove('active'));
        
        accoladeSlides[index].classList.add('active');
        accoladeDots[index].classList.add('active');
        
        currentAccolade = index;
    }

    if (accoladeDots.length > 0) {
        accoladeDots.forEach((dot, index) => {
            dot.addEventListener('click', () => showAccolade(index));
        });
    }

    if (accoladePrev) {
        accoladePrev.addEventListener('click', function() {
            currentAccolade = (currentAccolade - 1 + accoladeSlides.length) % accoladeSlides.length;
            showAccolade(currentAccolade);
        });
    }

    if (accoladeNext) {
        accoladeNext.addEventListener('click', function() {
            currentAccolade = (currentAccolade + 1) % accoladeSlides.length;
            showAccolade(currentAccolade);
        });
    }

    // Auto-rotate accolades
    if (accoladeSlides.length > 0) {
        setInterval(() => {
            currentAccolade = (currentAccolade + 1) % accoladeSlides.length;
            showAccolade(currentAccolade);
        }, 8000);
    }

    // Reservation Form Submission
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically send the form data to a server
            // For demo purposes, we'll just show an alert
            alert('Thank you for your reservation request. Our team will contact you within 24 hours to confirm your booking.');
            bookingForm.reset();
        });
    }

    // Back to Top Button
    const backToTopBtn = document.querySelector('.back-to-top');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('active');
        } else {
            backToTopBtn.classList.remove('active');
        }
    });
    
    backToTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Cookie Consent
    const cookieConsent = document.querySelector('.cookie-consent');
    const cookieAccept = document.querySelector('.cookie-accept');
    const cookieSettings = document.querySelector('.cookie-settings');
    
    // Check if user has already accepted cookies
    if (!localStorage.getItem('cookiesAccepted')) {
        setTimeout(() => {
            cookieConsent.classList.add('active');
        }, 2000);
    }
    
    cookieAccept.addEventListener('click', function() {
        localStorage.setItem('cookiesAccepted', 'true');
        cookieConsent.classList.remove('active');
    });
    
    cookieSettings.addEventListener('click', function() {
        // Here you would typically open cookie settings modal
        // For demo purposes, we'll just accept all cookies
        localStorage.setItem('cookiesAccepted', 'true');
        cookieConsent.classList.remove('active');
    });

    // Animate elements on scroll
    const animateElements = document.querySelectorAll('.section-header, .experience-content, .menu-carousel, .spaces-grid, .accolades-slider, .testimonial-slider, .reservation-content');
    
    function checkIfInView() {
        const windowHeight = window.innerHeight;
        const windowTopPosition = window.scrollY;
        const windowBottomPosition = windowTopPosition + windowHeight;
        
        animateElements.forEach(element => {
            const elementHeight = element.offsetHeight;
            const elementTopPosition = element.offsetTop;
            const elementBottomPosition = elementTopPosition + elementHeight;
            
            // Check if element is in viewport
            if ((elementBottomPosition >= windowTopPosition) && (elementTopPosition <= windowBottomPosition)) {
                element.classList.add('animate');
            }
        });
    }
    
    // Set initial styles for animation
    animateElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    });
    
    // Add animate class when element is in view
    window.addEventListener('scroll', checkIfInView);
    window.addEventListener('load', checkIfInView);
    
    // Add animation class to elements that are already in view on page load
    document.addEventListener('DOMContentLoaded', checkIfInView);
    
    // Custom animation for elements with animate class
    document.addEventListener('scroll', function() {
        document.querySelectorAll('.animate').forEach(element => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        });
    });
});