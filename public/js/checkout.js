// Checkout Management System
class CheckoutManager {
    constructor() {
        this.cartItems = [];
        this.init();
    }

    init() {
        this.loadCartData();
        this.setupEventListeners();
        this.renderOrderSummary();
        this.updateProgressBar();
        this.populateFormData();
    }

    loadCartData() {
        // Load cart data from session storage
        const checkoutCart = sessionStorage.getItem('checkoutCart');
        if (checkoutCart) {
            try {
                this.cartItems = JSON.parse(checkoutCart);
            } catch (error) {
                console.error('Failed to load checkout cart:', error);
                this.redirectToCart();
                return;
            }
        } else {
            // If no checkout data, redirect to cart
            this.redirectToCart();
            return;
        }

        // Validate cart is not empty
        if (!this.cartItems || this.cartItems.length === 0) {
            this.redirectToCart();
            return;
        }
    }

    redirectToCart() {
        alert('購物車是空的，請先添加商品');
        window.location.href = 'cart.html';
    }

    setupEventListeners() {
        // Form submission
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', (e) => {
                this.handleFormSubmission(e);
            });
        }

        // Input formatting - Credit Card
        const cardNumber = document.getElementById('cardNumber');
        if (cardNumber) {
            cardNumber.addEventListener('input', (e) => {
                e.target.value = this.formatCardNumber(e.target.value);
            });
        }

        const cardExpiry = document.getElementById('cardExpiry');
        if (cardExpiry) {
            cardExpiry.addEventListener('input', (e) => {
                e.target.value = this.formatExpiry(e.target.value);
            });
        }

        const cardCVV = document.getElementById('cardCVV');
        if (cardCVV) {
            cardCVV.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
            });
        }

        const phone = document.getElementById('phone');
        if (phone) {
            phone.addEventListener('input', (e) => {
                e.target.value = this.formatPhoneNumber(e.target.value);
            });
        }
    }


    populateFormData() {
        // Load saved user data if available
        const savedData = localStorage.getItem('userData');
        if (savedData) {
            try {
                const userData = JSON.parse(savedData);
                this.fillFormWithUserData(userData);
            } catch (error) {
                console.error('Failed to load user data:', error);
            }
        }
    }

    fillFormWithUserData(userData) {
        const fields = ['email', 'phone'];
        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element && userData[field]) {
                element.value = userData[field];
            }
        });
    }

    renderOrderSummary() {
        const checkoutItems = document.getElementById('checkoutItems');
        if (!checkoutItems) return;

        checkoutItems.innerHTML = '';

        this.cartItems.forEach(item => {
            const itemElement = this.createCheckoutItemElement(item);
            checkoutItems.appendChild(itemElement);
        });

        this.updateOrderTotals();
    }

    createCheckoutItemElement(item) {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'checkout-item';

        itemDiv.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="checkout-item-image">
            <div class="checkout-item-details">
                <h4>${item.name}</h4>
                <div class="checkout-item-meta">
                    <span><i class="fas fa-gamepad"></i> ${item.game}</span>
                    <span><i class="fas fa-tag"></i> ${item.category}</span>
                    <span><i class="fas fa-shopping-cart"></i> 數量: ${item.quantity}</span>
                </div>
            </div>
            <div class="checkout-item-price">NT$ ${(item.price * item.quantity).toLocaleString()}</div>
        `;

        return itemDiv;
    }

    updateOrderTotals() {
        const totalItems = this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        const subtotal = this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        // Virtual items - no shipping cost
        const total = subtotal;

        // Update summary display
        const elements = {
            checkoutTotalItems: document.getElementById('checkoutTotalItems'),
            checkoutTotal: document.getElementById('checkoutTotal'),
            finalTotal: document.getElementById('finalTotal')
        };

        if (elements.checkoutTotalItems) elements.checkoutTotalItems.textContent = totalItems;
        if (elements.checkoutTotal) elements.checkoutTotal.textContent = `NT$ ${total.toLocaleString()}`;
        if (elements.finalTotal) elements.finalTotal.textContent = total.toLocaleString();
    }

    updateProgressBar() {
        // Update progress bar to show current step
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach((step, index) => {
            if (index <= 1) { // Cart and Checkout steps
                step.classList.add('active');
                if (index < 1) {
                    step.classList.add('completed');
                }
            }
        });
    }

    async handleFormSubmission(e) {
        e.preventDefault();

        // Validate form
        if (!this.validateForm()) {
            return;
        }

        // Show loading state
        this.setLoadingState(true);

        try {
            // Submit order
            const orderData = await this.submitOrder();

            // Save order data to session storage for success page
            sessionStorage.setItem('lastOrder', JSON.stringify(orderData));

            // Redirect to success page
            window.location.href = 'checkout-success.html';

        } catch (error) {
            console.error('Order submission failed:', error);
            alert('訂單提交失敗，請稍後再試');
        } finally {
            this.setLoadingState(false);
        }
    }

    validateForm() {
        // Check required contact fields
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const terms = document.getElementById('terms');

        // Validate email
        if (!email || !email.value.trim()) {
            alert('請填寫電子郵件');
            if (email) email.focus();
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            alert('請輸入有效的電子郵件地址');
            email.focus();
            return false;
        }

        // Validate phone
        if (!phone || !phone.value.trim()) {
            alert('請填寫手機號碼');
            if (phone) phone.focus();
            return false;
        }

        // Check terms acceptance
        if (!terms || !terms.checked) {
            alert('請同意服務條款');
            if (terms) terms.focus();
            return false;
        }

        return true;
    }

    setLoadingState(loading) {
        const submitBtn = document.querySelector('#checkoutForm button[type="submit"]');
        if (!submitBtn) return;

        if (loading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 處理中...';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-lock"></i> 確認付款 NT$ <span id="finalTotal">' + this.getTotal() + '</span>';
        }
    }

    async submitOrder() {
        // Simulate API call delay
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Collect form data
        const formData = this.collectFormData();

        // Generate order number
        const orderNumber = this.generateOrderNumber();

        // Save order data
        const orderData = {
            orderNumber,
            items: this.cartItems,
            customerInfo: formData,
            totals: {
                subtotal: this.getSubtotal(),
                total: this.getTotal()
            },
            orderDate: new Date().toISOString(),
            status: 'confirmed'
        };

        // Save to localStorage (in real app, this would be sent to server)
        const orders = JSON.parse(localStorage.getItem('userOrders') || '[]');
        orders.push(orderData);
        localStorage.setItem('userOrders', JSON.stringify(orders));

        // Save user data for future use
        const userData = {
            email: formData.email,
            phone: formData.phone
        };
        localStorage.setItem('userData', JSON.stringify(userData));

        return orderData;
    }

    collectFormData() {
        const formData = {};
        const fields = [
            'email', 'phone', 'orderNotes', 'cardNumber', 'cardExpiry', 'cardCVV', 'cardName'
        ];

        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                formData[field] = element.value;
            }
        });

        formData.terms = document.getElementById('terms').checked;
        formData.newsletter = document.getElementById('newsletter').checked;

        return formData;
    }

    generateOrderNumber() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000);
        return `ORD-${timestamp}-${random}`;
    }

    getSubtotal() {
        return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    getTotal() {
        // Virtual items - no shipping, just return subtotal
        return this.getSubtotal();
    }


    // Utility methods
    formatCardNumber(value) {
        // Remove all non-digit characters
        const digits = value.replace(/\D/g, '');
        // Add spaces every 4 digits
        return digits.replace(/(\d{4})(?=\d)/g, '$1 ');
    }

    formatExpiry(value) {
        // Remove all non-digit characters
        const digits = value.replace(/\D/g, '');
        // Add slash after 2 digits
        if (digits.length >= 2) {
            return digits.slice(0, 2) + '/' + digits.slice(2, 4);
        }
        return digits;
    }

    formatPhoneNumber(value) {
        // Remove all non-digit characters
        const digits = value.replace(/\D/g, '');
        // Format as 09xx-xxx-xxx
        if (digits.length >= 10) {
            return digits.slice(0, 4) + '-' + digits.slice(4, 7) + '-' + digits.slice(7, 10);
        }
        return digits;
    }
}

// Global functions for HTML onclick handlers
function viewOrderDetails() {
    alert('訂單詳情頁面即將推出！');
}

function continueShopping() {
    window.location.href = 'market.html';
}

function showTerms() {
    const modal = document.getElementById('termsModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function showPrivacy() {
    alert('隱私政策頁面即將推出！');
}

// Initialize checkout manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.checkoutManager = new CheckoutManager();
});

// Export for use in other files
window.CheckoutManager = CheckoutManager;
