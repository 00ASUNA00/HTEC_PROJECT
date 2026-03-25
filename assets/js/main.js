/**
 * HTEC — Main JavaScript
 */

(function () {
    'use strict';

    // ============================================================
    // Navbar scroll effect
    // ============================================================
    const navbar = document.getElementById('navbar');
    if (navbar) {
        const onScroll = () => {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ============================================================
    // Mobile menu
    // ============================================================
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // ============================================================
    // Fade-up scroll animations
    // ============================================================
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    // ============================================================
    // Flash message auto-dismiss
    // ============================================================
    const flashMsg = document.getElementById('flash-msg');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.transition = 'opacity 0.4s ease';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 400);
        }, 4000);
    }

    // ============================================================
    // Counter animation (stats section)
    // ============================================================
    const counters = document.querySelectorAll('[data-counter]');
    if (counters.length) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-counter'));
                    const duration = 1500;
                    const step = target / (duration / 16);
                    let current = 0;
                    const timer = setInterval(() => {
                        current = Math.min(current + step, target);
                        el.textContent = Math.floor(current) + (el.getAttribute('data-suffix') || '');
                        if (current >= target) clearInterval(timer);
                    }, 16);
                    countObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(c => countObserver.observe(c));
    }

    // ============================================================
    // Product image gallery (product detail page)
    // ============================================================
    const mainImage = document.getElementById('main-product-image');
    const thumbs = document.querySelectorAll('.product-thumb');
    if (mainImage && thumbs.length) {
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                mainImage.src = thumb.getAttribute('data-src');
                mainImage.style.opacity = '0';
                setTimeout(() => { mainImage.style.opacity = '1'; }, 50);
                thumbs.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });
        mainImage.style.transition = 'opacity 0.2s ease';
    }

    // ============================================================
    // Product filter & search (products.php)
    // ============================================================
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.getAttribute('data-cat');
            const url = new URL(window.location.href);
            if (cat === 'all') {
                url.searchParams.delete('category');
            } else {
                url.searchParams.set('category', cat);
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    });

    // ============================================================
    // Search input debounce
    // ============================================================
    const searchInput = document.getElementById('product-search');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const url = new URL(window.location.href);
                if (searchInput.value.trim()) {
                    url.searchParams.set('search', searchInput.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }, 600);
        });
    }

    // ============================================================
    // Contact form client-side validation
    // ============================================================
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            const errors = [];
            const name = contactForm.querySelector('[name="name"]');
            const email = contactForm.querySelector('[name="email"]');
            const message = contactForm.querySelector('[name="message"]');

            if (!name.value.trim()) errors.push('Name is required');
            if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) errors.push('Valid email is required');
            if (!message.value.trim() || message.value.length < 10) errors.push('Message must be at least 10 characters');

            if (errors.length) {
                e.preventDefault();
                showClientError(errors.join(' · '));
            }
        });
    }

    function showClientError(msg) {
        let existing = document.getElementById('client-error');
        if (!existing) {
            existing = document.createElement('div');
            existing.id = 'client-error';
            existing.className = 'flash-message flash-error';
            document.querySelector('.fixed.top-24')?.prepend(existing);
        }
        existing.innerHTML = `<span class="flash-icon">✕</span><span>${msg}</span>`;
        setTimeout(() => existing?.remove(), 5000);
    }

    // ============================================================
    // Admin: Image preview
    // ============================================================
    const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
    imageInputs.forEach(input => {
        input.addEventListener('change', () => {
            const previewId = input.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            if (preview && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });

    // ============================================================
    // Admin: Confirm delete
    // ============================================================
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.getAttribute('data-confirm') || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // Admin: Sidebar toggle (mobile)
    // ============================================================
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminSidebar = document.getElementById('admin-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', () => adminSidebar.classList.toggle('open'));
        sidebarOverlay?.addEventListener('click', () => adminSidebar.classList.remove('open'));
    }

})();
