/**
 * ROCK STEADY - Premium Fashion Brand JavaScript
 * JUST KEEP ROCKING
 */

(function() {
    'use strict';

    // ============================================
    // GLOBAL STATE
    // ============================================
    window.RockSteady = {
        cart: {},
        wishlist: [],
        isLoading: false
    };

    // ============================================
    // UTILITIES
    // ============================================
    const Utils = {
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        formatPrice(amount) {
            return '$' + parseFloat(amount).toFixed(2);
        },

        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        },

        setCookie(name, value, days = 7) {
            const expires = new Date(Date.now() + days * 864e2).toUTCString();
            document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
        },

        animateCSS(element, animation, prefix = 'animate__') {
            return new Promise(resolve => {
                const animationName = `${prefix}${animation}`;
                element.classList.add(`${prefix}animated`, animationName);
                element.addEventListener('animationend', () => {
                    element.classList.remove(`${prefix}animated`, animationName);
                    resolve();
                }, { once: true });
            });
        }
    };

    // ============================================
    // NAVIGATION
    // ============================================
    const Navigation = {
        init() {
            this.navbar = document.querySelector('.navbar');
            this.mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            this.mobileMenu = document.querySelector('.mobile-menu');
            this.initScrollEffects();
            this.initMobileMenu();
        },

        initScrollEffects() {
            if (!this.navbar) return;

            let lastScroll = 0;
            
            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > 100) {
                    this.navbar.classList.add('scrolled');
                } else {
                    this.navbar.classList.remove('scrolled');
                }
                
                lastScroll = currentScroll;
            }, { passive: true });
        },

        initMobileMenu() {
            if (!this.mobileMenuBtn || !this.mobileMenu) return;

            this.mobileMenuBtn.addEventListener('click', () => {
                this.mobileMenu.classList.toggle('active');
                this.mobileMenuBtn.classList.toggle('active');
            });
        }
    };

    // ============================================
    // CART
    // ============================================
    const Cart = {
        slide: null,
        overlay: null,

        init() {
            this.slide = document.querySelector('.cart-slide');
            this.overlay = document.querySelector('.cart-overlay');
            this.bindEvents();
            this.updateFromServer();
        },

        bindEvents() {
            document.addEventListener('click', (e) => {
                if (e.target.closest('.cart-trigger')) {
                    e.preventDefault();
                    this.open();
                }
                
                if (e.target.closest('.cart-close') || e.target === this.overlay) {
                    this.close();
                }

                if (e.target.closest('.add-to-cart-btn')) {
                    e.preventDefault();
                    const btn = e.target.closest('.add-to-cart-btn');
                    this.addToCart(btn);
                }

                if (e.target.closest('.quick-add-btn')) {
                    e.preventDefault();
                    const productId = e.target.closest('.quick-add-btn').dataset.productId;
                    this.quickAdd(productId);
                }
            });

            document.addEventListener('submit', (e) => {
                if (e.target.closest('.cart-quantity-form')) {
                    e.preventDefault();
                    this.updateQuantity(e.target);
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.cart-item-remove')) {
                    e.preventDefault();
                    const item = e.target.closest('.cart-item');
                    this.removeItem(item);
                }
            });
        },

        open() {
            if (!this.slide) return;
            this.slide.classList.add('active');
            if (this.overlay) this.overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        },

        close() {
            if (!this.slide) return;
            this.slide.classList.remove('active');
            if (this.overlay) this.overlay.classList.remove('active');
            document.body.style.overflow = '';
        },

        async updateFromServer() {
            try {
                const response = await fetch('/cart/data');
                const data = await response.json();
                RockSteady.cart = data;
                this.updateUI(data);
            } catch (error) {
                console.error('Cart update failed:', error);
            }
        },

        updateUI(data) {
            const countElements = document.querySelectorAll('.cart-count');
            countElements.forEach(el => {
                el.textContent = data.count || 0;
            });

            const cartBody = document.querySelector('.cart-body');
            if (!cartBody) return;

            if (!data.items || data.items.length === 0) {
                cartBody.innerHTML = this.getEmptyCartHTML();
                return;
            }

            cartBody.innerHTML = data.items.map(item => this.getCartItemHTML(item)).join('');

            this.updateTotals(data);
        },

        getEmptyCartHTML() {
            return `
                <div class="cart-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 01-8 0"></path>
                    </svg>
                    <p>Your cart is empty</p>
                    <a href="/shop" class="btn btn-primary">Start Shopping</a>
                </div>
            `;
        },

        getCartItemHTML(item) {
            const variantText = [item.size, item.color].filter(Boolean).join(' / ');
            return `
                <div class="cart-item" data-key="${item.product_id}-${item.variant_id || ''}">
                    <div class="cart-item-image">
                        <img src="${item.image_url || '/assets/images/placeholder.jpg'}" alt="${item.name}">
                    </div>
                    <div class="cart-item-content">
                        <h4 class="cart-item-title">${item.name}</h4>
                        ${variantText ? `<p class="cart-item-variant">${variantText}</p>` : ''}
                        <p class="cart-item-price">${Utils.formatPrice(item.price)}</p>
                        <div class="cart-item-actions">
                            <form class="cart-quantity-form" data-product-id="${item.product_id}">
                                <div class="quantity-control">
                                    <button type="submit" name="action" value="decrease" class="quantity-btn">−</button>
                                    <span class="quantity-value">${item.quantity}</span>
                                    <button type="submit" name="action" value="increase" class="quantity-btn">+</button>
                                </div>
                                <input type="hidden" name="product_id" value="${item.product_id}">
                                <input type="hidden" name="variant_id" value="${item.variant_id || ''}">
                            </form>
                            <a href="#" class="cart-item-remove">Remove</a>
                        </div>
                    </div>
                </div>
            `;
        },

        updateTotals(data) {
            const subtotalEl = document.querySelector('.cart-subtotal');
            const discountEl = document.querySelector('.cart-discount');
            const shippingEl = document.querySelector('.cart-shipping');
            const totalEl = document.querySelector('.cart-total');

            if (subtotalEl) subtotalEl.textContent = Utils.formatPrice(data.subtotal || 0);
            if (discountEl) discountEl.textContent = '-' + Utils.formatPrice(data.discount || 0);
            if (shippingEl) shippingEl.textContent = data.shipping === 0 ? 'FREE' : Utils.formatPrice(data.shipping || 0);
            if (totalEl) totalEl.textContent = Utils.formatPrice(data.total || 0);
        },

        async addToCart(btn) {
            const productId = btn.dataset.productId;
            const form = btn.closest('.product-options');
            
            const data = {
                product_id: productId,
                quantity: 1
            };

            if (form) {
                const sizeInput = form.querySelector('input[name="size"]:checked');
                const colorInput = form.querySelector('input[name="color"]:checked');
                
                if (sizeInput) data.size = sizeInput.value;
                if (colorInput) data.color = colorInput.value;
            }

            try {
                btn.disabled = true;
                btn.innerHTML = '<span class="loading"></span>';

                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                const result = await response.json();

                if (result.success) {
                    this.showNotification('Added to cart!', 'success');
                    await this.updateFromServer();
                    this.open();
                } else {
                    this.showNotification(result.message || 'Failed to add to cart', 'error');
                }
            } catch (error) {
                this.showNotification('Something went wrong', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Add to Cart';
            }
        },

        async quickAdd(productId) {
            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ product_id: productId, quantity: 1 })
                });

                const result = await response.json();

                if (result.success) {
                    this.showNotification('Added to cart!', 'success');
                    await this.updateFromServer();
                    this.open();
                }
            } catch (error) {
                this.showNotification('Something went wrong', 'error');
            }
        },

        async updateQuantity(form) {
            const formData = new FormData(form);
            const productId = formData.get('product_id');
            const action = formData.get('action');
            const variantId = formData.get('variant_id');

            const currentQty = parseInt(form.querySelector('.quantity-value').textContent);
            const newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;

            if (newQty < 1) {
                this.removeItem(form.closest('.cart-item'));
                return;
            }

            try {
                await fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        product_id: productId,
                        quantity: newQty,
                        variant_id: variantId
                    })
                });

                await this.updateFromServer();
            } catch (error) {
                this.showNotification('Failed to update quantity', 'error');
            }
        },

        async removeItem(item) {
            const key = item.dataset.key;
            const [productId, variantId] = key.split('-');

            try {
                await fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        product_id: productId,
                        variant_id: variantId
                    })
                });

                item.remove();
                await this.updateFromServer();

                if (document.querySelectorAll('.cart-item').length === 0) {
                    this.updateUI({ items: [], count: 0, subtotal: 0, total: 0 });
                }
            } catch (error) {
                this.showNotification('Failed to remove item', 'error');
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => notification.classList.add('show'), 10);

            const close = () => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            };

            notification.querySelector('.notification-close').addEventListener('click', close);
            setTimeout(close, 3000);
        }
    };

    // ============================================
    // WISHLIST
    // ============================================
    const Wishlist = {
        init() {
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.wishlist-btn');
                if (!btn) return;

                e.preventDefault();
                await this.toggle(btn);
            });
        },

        async toggle(btn) {
            const productId = btn.dataset.productId;

            try {
                const response = await fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ product_id: productId })
                });

                const result = await response.json();

                if (result.requires_login) {
                    window.location.href = '/login';
                    return;
                }

                if (result.success) {
                    btn.classList.toggle('active', result.is_in_wishlist);
                    Cart.showNotification(
                        result.is_in_wishlist ? 'Added to wishlist' : 'Removed from wishlist',
                        'success'
                    );
                }
            } catch (error) {
                Cart.showNotification('Something went wrong', 'error');
            }
        }
    };

    // ============================================
    // PRODUCT GALLERY
    // ============================================
    const ProductGallery = {
        init() {
            const gallery = document.querySelector('.product-gallery');
            if (!gallery) return;

            const mainImage = gallery.querySelector('.product-gallery-main img');
            const thumbs = gallery.querySelectorAll('.product-gallery-thumb');

            thumbs.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    const newSrc = thumb.querySelector('img').src;
                    const newFullSrc = newSrc.replace('?w=800', '?w=1200');
                    mainImage.src = newFullSrc;
                    
                    thumbs.forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                });
            });

            if (mainImage) {
                mainImage.addEventListener('mousemove', (e) => this.zoom(e, mainImage));
                mainImage.addEventListener('mouseleave', () => this.resetZoom(mainImage));
            }
        },

        zoom(e, img) {
            const rect = img.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width * 100;
            const y = (e.clientY - rect.top) / rect.height * 100;
            
            img.style.transformOrigin = `${x}% ${y}%`;
            img.style.transform = 'scale(1.5)';
        },

        resetZoom(img) {
            img.style.transform = 'scale(1)';
        }
    };

    // ============================================
    // PRODUCT OPTIONS
    // ============================================
    const ProductOptions = {
        init() {
            document.addEventListener('change', (e) => {
                if (e.target.name === 'size' || e.target.name === 'color') {
                    this.updateSelected(e.target.form);
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('.product-size')) {
                    const input = e.target.closest('.product-size').querySelector('input');
                    if (input) input.checked = true;
                    this.updateSelected(input.form);
                }
            });
        },

        updateSelected(form) {
            const size = form.querySelector('input[name="size"]:checked');
            const color = form.querySelector('input[name="color"]:checked');
            const addBtn = form.querySelector('.add-to-cart-btn');

            if (addBtn) {
                const productId = addBtn.dataset.productId;
                addBtn.disabled = false;
            }
        }
    };

    // ============================================
    // SEARCH
    // ============================================
    const Search = {
        init() {
            this.input = document.querySelector('.search-input');
            this.results = document.querySelector('.search-results');
            
            if (!this.input) return;

            this.input.addEventListener('input', Utils.debounce((e) => {
                this.search(e.target.value);
            }, 300));

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-container')) {
                    this.close();
                }
            });
        },

        async search(query) {
            if (query.length < 2) {
                this.close();
                return;
            }

            try {
                const response = await fetch(`/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                this.showResults(data.results);
            } catch (error) {
                console.error('Search failed:', error);
            }
        },

        showResults(results) {
            if (!this.results) return;

            if (results.length === 0) {
                this.results.innerHTML = '<div class="search-no-results">No products found</div>';
                this.results.classList.add('active');
                return;
            }

            this.results.innerHTML = results.map(product => `
                <a href="/product/${product.slug}" class="search-result-item">
                    <img src="${product.image_url || '/assets/images/placeholder.jpg'}" alt="${product.name}">
                    <div class="search-result-info">
                        <h4>${product.name}</h4>
                        <p>${product.category_name}</p>
                        <span class="search-result-price">${Utils.formatPrice(product.sale_price || product.price)}</span>
                    </div>
                </a>
            `).join('');

            this.results.classList.add('active');
        },

        close() {
            if (this.results) {
                this.results.classList.remove('active');
            }
        }
    };

    // ============================================
    // FORMS
    // ============================================
    const Forms = {
        init() {
            document.addEventListener('submit', (e) => {
                const form = e.target;
                
                if (form.classList.contains('ajax-form')) {
                    e.preventDefault();
                    this.submitAjax(form);
                }
            });

            document.addEventListener('input', Utils.debounce((e) => {
                if (e.target.classList.contains('validate-on-input')) {
                    this.validateField(e.target);
                }
            }, 500));
        },

        async submitAjax(form) {
            const submitBtn = form.querySelector('[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            const formData = new FormData(form);

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading"></span>';
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success || response.redirected) {
                    if (result.message) {
                        Cart.showNotification(result.message, 'success');
                    }
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                } else {
                    Cart.showNotification(result.message || 'Something went wrong', 'error');
                }
            } catch (error) {
                Cart.showNotification('Something went wrong', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        },

        validateField(field) {
            const value = field.value;
            const isValid = field.checkValidity();
            
            field.classList.toggle('invalid', !isValid && value.length > 0);
            field.classList.toggle('valid', isValid && value.length > 0);
        }
    };

    // ============================================
    // ANIMATIONS (GSAP)
    // ============================================
    const Animations = {
        init() {
            if (typeof gsap === 'undefined') return;

            this.initScrollAnimations();
            this.initStaggerAnimations();
        },

        initScrollAnimations() {
            gsap.registerPlugin(ScrollTrigger);

            gsap.utils.toArray('.animate-on-scroll').forEach(element => {
                gsap.from(element, {
                    opacity: 0,
                    y: 50,
                    duration: 0.8,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });
            });
        },

        initStaggerAnimations() {
            gsap.utils.toArray('.stagger-animation').forEach(container => {
                const items = container.children;
                
                gsap.from(items, {
                    opacity: 0,
                    y: 30,
                    stagger: 0.1,
                    duration: 0.6,
                    ease: 'power2.out'
                });
            });
        }
    };

    // ============================================
    // ADMIN SETTINGS ACCESS
    // ============================================
    const AdminAccess = {
        init() {
            const settingsBtn = document.querySelector('.admin-settings-btn');
            if (!settingsBtn) return;

            settingsBtn.addEventListener('click', () => {
                const password = prompt('Enter admin password:');
                
                if (!password) return;

                this.verifyPassword(password);
            });
        },

        async verifyPassword(password) {
            try {
                const response = await fetch('/admin/authenticate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ password })
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect || '/admin';
                } else {
                    Cart.showNotification('Invalid password', 'error');
                }
            } catch (error) {
                Cart.showNotification('Authentication failed', 'error');
            }
        }
    };

    // ============================================
    // MODALS
    // ============================================
    const Modals = {
        init() {
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('[data-modal]');
                if (trigger) {
                    e.preventDefault();
                    this.open(trigger.dataset.modal);
                }

                if (e.target.closest('.modal-close') || e.target.classList.contains('modal-backdrop')) {
                    this.closeAll();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAll();
                }
            });
        },

        open(modalId) {
            const modal = document.getElementById(modalId) || document.querySelector(`.modal[data-modal="${modalId}"]`);
            if (!modal) return;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        },

        closeAll() {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
            document.body.style.overflow = '';
        }
    };

    // ============================================
    // LAZY LOADING
    // ============================================
    const LazyLoad = {
        init() {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img.lazy').forEach(img => {
                    observer.observe(img);
                });
            }
        }
    };

    // ============================================
    // NEWSLETTER
    // ============================================
    const Newsletter = {
        init() {
            const form = document.querySelector('.newsletter-form');
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const email = form.querySelector('input[type="email"]').value;
                const btn = form.querySelector('button');
                const originalText = btn.textContent;

                try {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="loading"></span>';

                    const response = await fetch('/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({ email })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Cart.showNotification(result.message, 'success');
                        form.reset();
                    } else {
                        Cart.showNotification(result.message, 'error');
                    }
                } catch (error) {
                    Cart.showNotification('Something went wrong', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });
        }
    };

    // ============================================
    // COUNTDOWN TIMER
    // ============================================
    const CountdownTimer = {
        init() {
            document.querySelectorAll('[data-countdown]').forEach(el => {
                const endDate = new Date(el.dataset.countdown).getTime();
                this.updateTimer(el, endDate);
                setInterval(() => this.updateTimer(el, endDate), 1000);
            });
        },

        updateTimer(el, endDate) {
            const now = Date.now();
            const distance = endDate - now;

            if (distance < 0) {
                el.innerHTML = 'EXPIRED';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            el.innerHTML = `
                <span>${days}d</span>
                <span>${hours}h</span>
                <span>${minutes}m</span>
                <span>${seconds}s</span>
            `;
        }
    };

    // ============================================
    // REVIEWS
    // ============================================
    const Reviews = {
        init() {
            document.addEventListener('click', async (e) => {
                const helpfulBtn = e.target.closest('.review-helpful-btn');
                if (helpfulBtn) {
                    e.preventDefault();
                    await this.markHelpful(helpfulBtn);
                }
            });
        },

        async markHelpful(btn) {
            const reviewId = btn.dataset.reviewId;

            try {
                await fetch('/review/helpful', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ review_id: reviewId })
                });

                const countEl = btn.querySelector('span');
                if (countEl) {
                    countEl.textContent = parseInt(countEl.textContent) + 1;
                }
                btn.disabled = true;
            } catch (error) {
                console.error('Failed to mark helpful:', error);
            }
        }
    };

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        Navigation.init();
        Cart.init();
        Wishlist.init();
        ProductGallery.init();
        ProductOptions.init();
        Search.init();
        Forms.init();
        Animations.init();
        AdminAccess.init();
        Modals.init();
        LazyLoad.init();
        Newsletter.init();
        CountdownTimer.init();
        Reviews.init();

        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                easing: 'ease-out',
                once: true
            });
        }

        if (typeof Swiper !== 'undefined') {
            document.querySelectorAll('.swiper-container').forEach(container => {
                new Swiper(container, {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: container.querySelector('.swiper-pagination'),
                        clickable: true
                    },
                    navigation: {
                        nextEl: container.querySelector('.swiper-button-next'),
                        prevEl: container.querySelector('.swiper-button-prev')
                    }
                });
            });
        }
    });

})();
