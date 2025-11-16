// Cart Initialization Script - Shared across all pages
class CartInitializer {
    constructor() {
        this.init();
    }

    init() {
        this.updateCartCount();
        this.setupCartSync();
    }

    updateCartCount() {
        const cartCountElements = document.querySelectorAll('.cart-count');
        let cart = [];

        try {
            cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
        } catch (error) {
            console.error('Failed to load cart data:', error);
            cart = [];
        }

        const count = cart.reduce((total, item) => total + item.quantity, 0);

        cartCountElements.forEach(cartCount => {
            if (count === 0) {
                cartCount.style.display = 'none';
                cartCount.textContent = '0';
            } else {
                cartCount.style.display = 'inline-flex';
                cartCount.textContent = count > 99 ? '99+' : count;
            }
        });
    }

    setupCartSync() {
        // Listen for storage changes to sync cart count across tabs
        window.addEventListener('storage', (e) => {
            if (e.key === 'shoppingCart') {
                this.updateCartCount();
            }
        });

        // Custom event for cart updates within the same tab
        window.addEventListener('cartUpdated', () => {
            this.updateCartCount();
        });
    }

    // Utility method to trigger cart update event
    static triggerCartUpdate() {
        window.dispatchEvent(new CustomEvent('cartUpdated'));
    }

    // Utility method to get cart data
    static getCartData() {
        try {
            return JSON.parse(localStorage.getItem('shoppingCart') || '[]');
        } catch (error) {
            console.error('Failed to get cart data:', error);
            return [];
        }
    }

    // Utility method to save cart data
    static saveCartData(cart) {
        try {
            localStorage.setItem('shoppingCart', JSON.stringify(cart));
            CartInitializer.triggerCartUpdate();
        } catch (error) {
            console.error('Failed to save cart data:', error);
        }
    }

    // Utility method to add item to cart
    static addToCart(item) {
        const cart = CartInitializer.getCartData();
        const existingItem = cart.find(cartItem => cartItem.id === item.id);

        if (existingItem) {
            existingItem.quantity += item.quantity || 1;
        } else {
            cart.push({
                ...item,
                quantity: item.quantity || 1,
                addedAt: new Date().toISOString()
            });
        }

        CartInitializer.saveCartData(cart);
        CartInitializer.showNotification(`${item.name} 已加入購物車`, 'success');
    }

    // Utility method to show notification
    static showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.cartInitializer = new CartInitializer();
});

// Export for use in other files
window.CartInitializer = CartInitializer;
