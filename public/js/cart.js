// Cart Management System
class ShoppingCart {
    constructor() {
        this.items = [];
        this.loadCart();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.renderCart();
        this.renderRecommendations();
    }

    setupEventListeners() {
        // Use event delegation on cart container for better performance with dynamic elements
        const cartList = document.getElementById('cartList');
        const recommendationsGrid = document.getElementById('recommendationsGrid');

        // Cart list event delegation
        if (cartList) {
            cartList.addEventListener('click', (e) => {
                const target = e.target;

                // Quantity buttons
                if (target.classList.contains('quantity-btn') || target.closest('.quantity-btn')) {
                    e.preventDefault();
                    const button = target.classList.contains('quantity-btn') ? target : target.closest('.quantity-btn');
                    const action = button.getAttribute('data-action');
                    const cartItem = button.closest('.cart-item');
                    const itemId = cartItem.getAttribute('data-id');

                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('Quantity button clicked:', action, itemId);
                    }

                    if (action === 'increase') {
                        this.updateQuantity(itemId, 1);
                    } else if (action === 'decrease') {
                        this.updateQuantity(itemId, -1);
                    }
                }

                // Remove item
                if (target.classList.contains('remove-btn') || target.closest('.remove-btn')) {
                    e.preventDefault();
                    const button = target.classList.contains('remove-btn') ? target : target.closest('.remove-btn');
                    const cartItem = button.closest('.cart-item');
                    const itemId = cartItem.getAttribute('data-id');

                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('Remove button clicked:', itemId);
                    }
                    this.removeItem(itemId);
                }
            });

            // Quantity input change
            cartList.addEventListener('input', (e) => {
                if (e.target.classList.contains('quantity-input')) {
                    const itemId = e.target.closest('.cart-item').getAttribute('data-id');
                    const newQuantity = parseInt(e.target.value);

                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('Quantity input changed:', itemId, newQuantity);
                    }

                    if (newQuantity > 0) {
                        this.setQuantity(itemId, newQuantity);
                    } else if (newQuantity === 0) {
                        this.removeItem(itemId);
                    }
                }
            });

            // Handle input blur to validate final value
            cartList.addEventListener('blur', (e) => {
                if (e.target.classList.contains('quantity-input')) {
                    const itemId = e.target.closest('.cart-item').getAttribute('data-id');
                    const newQuantity = parseInt(e.target.value);

                    if (isNaN(newQuantity) || newQuantity < 1) {
                        // Reset to current quantity
                        const item = this.items.find(item => item.id === itemId);
                        if (item) {
                            e.target.value = item.quantity;
                        }
                    }
                }
            }, true);
        }

        // Recommendations event delegation
        if (recommendationsGrid) {
            recommendationsGrid.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-to-cart-btn') || e.target.closest('.add-to-cart-btn')) {
                    e.preventDefault();
                    const button = e.target.classList.contains('add-to-cart-btn') ? e.target : e.target.closest('.add-to-cart-btn');
                    const itemId = button.getAttribute('data-id');

                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('Add to cart from recommendations:', itemId);
                    }
                    this.addItemById(itemId);
                }
            });
        }
    }

    loadCart() {
        const savedCart = localStorage.getItem('shoppingCart');
        if (savedCart) {
            try {
                this.items = JSON.parse(savedCart);
            } catch (error) {
                console.error('Failed to load cart:', error);
                this.items = [];
            }
        }
    }

    saveCart() {
        try {
            localStorage.setItem('shoppingCart', JSON.stringify(this.items));
        } catch (error) {
            console.error('Failed to save cart:', error);
        }
    }

    addItem(item) {
        // Loose comparison of string/numeric IDs to avoid duplicate items
        const existingItem = this.items.find(cartItem => String(cartItem.id) === String(item.id));

        if (existingItem) {
            existingItem.quantity += item.quantity || 1;
        } else {
            this.items.push({
                ...item,
                quantity: item.quantity || 1,
                addedAt: new Date().toISOString()
            });
        }

        this.saveCart();
        this.renderCart();
        this.showNotification(`${item.name} 已添加到購物車`);
        this.triggerCartUpdate();
    }

    addItemById(itemId) {
        // This would typically fetch item data from an API
        // For now, we'll use sample data
        const sampleItems = this.getSampleItems();
        const item = sampleItems.find(item => item.id === itemId);

        if (item) {
            this.addItem(item);
        }
    }

    removeItem(itemId) {
        // Use string comparison to avoid '1' vs 1 issues
        this.items = this.items.filter(item => String(item.id) !== String(itemId));
        this.saveCart();
        this.renderCart();
        this.showNotification('商品已從購物車移除');
        this.triggerCartUpdate();
    }

    updateQuantity(itemId, change) {
        const item = this.items.find(item => String(item.id) === String(itemId));
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                this.removeItem(itemId);
                return;
            }
            this.saveCart();
            this.renderCart();
            this.triggerCartUpdate();
        }
    }

    setQuantity(itemId, quantity) {
        const item = this.items.find(item => String(item.id) === String(itemId));
        if (item) {
            item.quantity = quantity;
            if (item.quantity <= 0) {
                this.removeItem(itemId);
                return;
            }
            this.saveCart();
            this.renderCart();
            this.triggerCartUpdate();
        }
    }

    getTotalItems() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }

    getSubtotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    getTotal() {
        // Virtual items - no shipping cost, just return subtotal
        return this.getSubtotal();
    }

    renderCart() {
        const cartList = document.getElementById('cartList');
        const emptyCart = document.getElementById('emptyCart');
        const cartItems = document.getElementById('cartItems');
        const cartSummary = document.getElementById('cartSummary');
        const cartRecommendations = document.getElementById('cartRecommendations');

        if (this.items.length === 0) {
            emptyCart.style.display = 'block';
            cartItems.style.display = 'none';
            cartSummary.style.display = 'none';
            cartRecommendations.style.display = 'none';
            return;
        }

        emptyCart.style.display = 'none';
        cartItems.style.display = 'block';
        cartSummary.style.display = 'block';
        cartRecommendations.style.display = 'block';

        cartList.innerHTML = '';

        this.items.forEach(item => {
            const itemElement = this.createCartItemElement(item);
            cartList.appendChild(itemElement);
        });

        this.updateCartSummary();
    }

    createCartItemElement(item) {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'cart-item';
        itemDiv.setAttribute('data-id', item.id);

        const infoDiv = document.createElement('div');
        infoDiv.className = 'cart-item-info';
        const img = document.createElement('img');
        img.src = item.image;
        img.alt = item.name;
        img.className = 'cart-item-image';
        infoDiv.appendChild(img);

        const detailsDiv = document.createElement('div');
        detailsDiv.className = 'cart-item-details';
        const h4 = document.createElement('h4');
        h4.textContent = item.name;
        detailsDiv.appendChild(h4);

        const metaDiv = document.createElement('div');
        metaDiv.className = 'cart-item-meta';
        const gameSpan = document.createElement('span');
        gameSpan.innerHTML = '<i class="fas fa-gamepad"></i>';
        gameSpan.appendChild(document.createTextNode(` ${item.game}`));
        const categorySpan = document.createElement('span');
        categorySpan.innerHTML = '<i class="fas fa-tag"></i>';
        categorySpan.appendChild(document.createTextNode(` ${item.category}`));
        metaDiv.appendChild(gameSpan);
        metaDiv.appendChild(categorySpan);
        detailsDiv.appendChild(metaDiv);
        infoDiv.appendChild(detailsDiv);
        itemDiv.appendChild(infoDiv);

        const priceDiv = document.createElement('div');
        priceDiv.className = 'cart-item-price';
        priceDiv.textContent = `NT$ ${item.price.toLocaleString()}`;
        itemDiv.appendChild(priceDiv);

        const quantityDiv = document.createElement('div');
        quantityDiv.className = 'cart-item-quantity';
        const decreaseBtn = document.createElement('button');
        decreaseBtn.className = 'quantity-btn';
        decreaseBtn.setAttribute('data-action', 'decrease');
        decreaseBtn.innerHTML = '<i class="fas fa-minus"></i>';
        const input = document.createElement('input');
        input.type = 'number';
        input.className = 'quantity-input';
        input.value = item.quantity;
        input.min = '1';
        const increaseBtn = document.createElement('button');
        increaseBtn.className = 'quantity-btn';
        increaseBtn.setAttribute('data-action', 'increase');
        increaseBtn.innerHTML = '<i class="fas fa-plus"></i>';
        quantityDiv.appendChild(decreaseBtn);
        quantityDiv.appendChild(input);
        quantityDiv.appendChild(increaseBtn);
        itemDiv.appendChild(quantityDiv);

        const totalDiv = document.createElement('div');
        totalDiv.className = 'cart-item-total';
        totalDiv.textContent = `NT$ ${(item.price * item.quantity).toLocaleString()}`;
        itemDiv.appendChild(totalDiv);

        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'cart-item-actions';
        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '<i class="fas fa-trash"></i> 移除';
        actionsDiv.appendChild(removeBtn);
        itemDiv.appendChild(actionsDiv);

        return itemDiv;
    }

    updateCartSummary() {
        const totalItems = document.getElementById('totalItems');
        const totalPrice = document.getElementById('totalPrice');

        // Virtual items - no shipping, so total = subtotal
        if (totalItems) {
            totalItems.textContent = this.getTotalItems();
        }
        if (totalPrice) {
            totalPrice.innerHTML = `<strong>NT$ ${this.getTotal().toLocaleString()}</strong>`;
        }
    }

    renderRecommendations() {
        const recommendationsGrid = document.getElementById('recommendationsGrid');
        if (!recommendationsGrid) return;

        const sampleItems = this.getSampleItems();
        const recommendations = sampleItems.slice(0, 4); // Show 4 recommendations

        recommendationsGrid.innerHTML = '';

        recommendations.forEach(item => {
            const recommendationElement = this.createRecommendationElement(item);
            recommendationsGrid.appendChild(recommendationElement);
        });
    }

    createRecommendationElement(item) {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'recommendation-card';

        itemDiv.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="recommendation-image">
            <div class="recommendation-content">
                <h4>${item.name}</h4>
                <div class="recommendation-price">NT$ ${item.price.toLocaleString()}</div>
                <div class="recommendation-meta">
                    <span class="recommendation-game">${item.game}</span>
                    <button class="add-to-cart-btn" data-id="${item.id}">
                        <i class="fas fa-cart-plus"></i> 加入購物車
                    </button>
                </div>
            </div>
        `;

        return itemDiv;
    }

    getSampleItems() {
        return [
            {
                id: 'sample-1',
                name: '無盡之劍 - 傳說級武器',
                price: 2500,
                image: 'https://via.placeholder.com/300x200/667eea/white?text=Endless+Sword',
                game: '魔戒：中土世界',
                category: '武器'
            },
            {
                id: 'sample-2',
                name: '龍之盔甲 - 史詩級防具',
                price: 1800,
                image: 'https://via.placeholder.com/300x200/764ba2/white?text=Dragon+Armor',
                game: '上古捲軸5',
                category: '防具'
            },
            {
                id: 'sample-3',
                name: '魔法藥水組合包',
                price: 450,
                image: 'https://via.placeholder.com/300x200/f093fb/white?text=Potion+Pack',
                game: '巫師3',
                category: '消耗品'
            },
            {
                id: 'sample-4',
                name: '稀有坐騎 - 天馬',
                price: 3200,
                image: 'https://via.placeholder.com/300x200/4facfe/white?text=Pegasus',
                game: '魔戒：中土世界',
                category: '坐騎'
            }
        ];
    }

    clearCart() {
        if (confirm('確定要清空購物車嗎？')) {
            this.items = [];
            this.saveCart();
            this.renderCart();
            this.showNotification('購物車已清空');
            this.triggerCartUpdate();
        }
    }

    showNotification(message, type = 'success') {
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

    triggerCartUpdate() {
        // Trigger custom event for cart updates
        const event = new CustomEvent('cartUpdated', {
            detail: {
                totalItems: this.getTotalItems(),
                total: this.getTotal()
            }
        });
        document.dispatchEvent(event);
        
        // Update cart count in navbar if it exists
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const count = this.getTotalItems();
            cartCount.textContent = count > 99 ? '99+' : count;
            cartCount.style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }
}

// Global cart instance
let shoppingCart;

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Initializing shopping cart...');
    }
    shoppingCart = new ShoppingCart();
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Shopping cart initialized successfully');
    }

    // Show debug panel in development
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        const debugPanel = document.getElementById('debugPanel');
        if (debugPanel) {
            debugPanel.style.display = 'block';
        }

        // Add some test items for development (remove in production)
        if (shoppingCart.items.length === 0) {
            console.log('Adding test items for development...');
            shoppingCart.addItem({
                id: 'test-1',
                name: '測試商品1',
                price: 100,
                image: 'https://via.placeholder.com/150x150/4facfe/white?text=Test+1',
                game: '測試遊戲',
                category: '測試分類',
                quantity: 2
            });
            shoppingCart.addItem({
                id: 'test-2',
                name: '測試商品2',
                price: 200,
                image: 'https://via.placeholder.com/150x150/f093fb/white?text=Test+2',
                game: '測試遊戲',
                category: '測試分類',
                quantity: 1
            });
        }
    }
});

// Global functions for HTML onclick handlers
function addToCart(itemId) {
    if (shoppingCart) {
        shoppingCart.addItemById(itemId);
    }
}

function proceedToCheckout() {
    if (shoppingCart && shoppingCart.items.length > 0) {
        // Save cart data to session storage for checkout page
        sessionStorage.setItem('checkoutCart', JSON.stringify(shoppingCart.items));
        window.location.href = 'checkout.html';
    } else {
        if (shoppingCart) {
            shoppingCart.showNotification('購物車是空的，請先添加商品', 'error');
        } else {
            alert('購物車是空的，請先添加商品');
        }
    }
}

function clearCart() {
    if (shoppingCart) {
        shoppingCart.clearCart();
    }
}

function addTestItems() {
    if (shoppingCart) {
        shoppingCart.addItem({
            id: 'test-1',
            name: '測試商品1',
            price: 100,
            image: 'https://via.placeholder.com/150x150/4facfe/white?text=Test+1',
            game: '測試遊戲',
            category: '測試分類',
            quantity: 2
        });
        shoppingCart.addItem({
            id: 'test-2',
            name: '測試商品2',
            price: 200,
            image: 'https://via.placeholder.com/150x150/f093fb/white?text=Test+2',
            game: '測試遊戲',
            category: '測試分類',
            quantity: 1
        });
    }
}

// Show debug panel in development (this will be called in the existing DOMContentLoaded handler)

// Export for use in other files
window.ShoppingCart = ShoppingCart;
window.shoppingCart = shoppingCart;
