// Holo E-Commerce JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initQuantityInputs();
    initSmoothScroll();
    initMobileMenu();
    initProductCards();
});

/**
 * Quantity input validation and enhancement
 */
function initQuantityInputs() {
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.getAttribute('min')) || 1;
            const max = parseInt(this.getAttribute('max')) || 99;
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    });
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Mobile menu toggle (for future enhancement)
 */
function initMobileMenu() {
    // Mobile menu functionality can be added here
    // when a mobile menu button is implemented
}

function initProductCards() {
    const cards = document.querySelectorAll('.product-card[data-href]');

    cards.forEach(card => {
        card.addEventListener('click', e => {
            const interactive = e.target.closest('a, button, input, select, textarea, label, form');
            if (interactive) return;
            window.location.href = card.dataset.href;
        });

        card.addEventListener('keydown', e => {
            const key = e.key;
            if (key !== 'Enter' && key !== ' ') return;
            const interactive = e.target.closest('a, button, input, select, textarea, label, form');
            if (interactive) return;
            e.preventDefault();
            window.location.href = card.dataset.href;
        });
    });
}

/**
 * Format price with currency
 */
function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        background: ${type === 'success' ? '#22c55e' : '#ef4444'};
        color: white;
        font-weight: 500;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    
    // Add animation styles
    if (!document.querySelector('#notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(styles);
    }
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add click handlers for add to cart buttons
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        // Form will submit normally, but we could add AJAX here
        // For now, just ensure the form submits properly
    });
});
