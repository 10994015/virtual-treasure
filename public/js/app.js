// Virtual Item Market Platform - Main Application
class VirtualMarketApp {
    constructor() {
        this.apiUrl = 'http://localhost:3001/api';
        this.token = localStorage.getItem('token');
        this.user = null;
        this.currentPage = 1;
        this.itemsPerPage = 12;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.checkAuthStatus();
        this.loadInitialData();
    }

    setupEventListeners() {
        // Navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = e.target.getAttribute('href').substring(1);
                this.scrollToSection(target);
                this.updateActiveNavLink(target);
            });
        });

        // Auth buttons
        document.getElementById('loginBtn').addEventListener('click', () => this.showModal('login'));
        document.getElementById('registerBtn').addEventListener('click', () => this.showModal('register'));
        document.getElementById('logoutBtn').addEventListener('click', () => this.logout());

        // Contact form
        document.getElementById('contactForm').addEventListener('submit', (e) => this.handleContactSubmit(e));

        // Modal close
        document.querySelectorAll('.modal-close').forEach(close => {
            close.addEventListener('click', () => this.hideModal());
        });

        // Click outside modal to close
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.hideModal();
                }
            });
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    async checkAuthStatus() {
        if (this.token) {
            try {
                const response = await this.apiRequest('/users/profile');
                if (response.success) {
                    this.user = response.data.user;
                    this.updateUIForAuthenticatedUser();
                } else {
                    this.logout();
                }
            } catch (error) {
                console.error('Error checking auth status:', error);
                this.logout();
            }
        }
    }

    updateUIForAuthenticatedUser() {
        document.getElementById('navUser').style.display = 'flex';
        document.getElementById('userBalance').textContent = `$${this.user.balance.toFixed(2)}`;
        document.querySelector('.nav-auth').style.display = 'none';
    }

    updateUIForUnauthenticatedUser() {
        document.getElementById('navUser').style.display = 'none';
        document.querySelector('.nav-auth').style.display = 'flex';
    }

    async loadInitialData() {
        await this.loadMarketItems();
        this.loadSampleItems(); // Load some sample items for demo

        // Add form event listeners
        this.setupFormListeners();
    }

    setupFormListeners() {
        // Login form
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        // Register form
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }
    }

    loadSampleItems() {
        const sampleItems = [
            {
                id: 1,
                name: '傳說級裝備',
                category: '武器',
                game: 'World of Warcraft',
                current_price: 299,
                seller_username: 'GameMaster',
                image_url: null
            },
            {
                id: 2,
                name: '英雄聯盟皮膚',
                category: '皮膚',
                game: 'League of Legends',
                current_price: 159,
                seller_username: 'ProGamer',
                image_url: null
            },
            {
                id: 3,
                name: 'Dota 2 寶石',
                category: '虛擬貨幣',
                game: 'Dota 2',
                current_price: 89,
                seller_username: 'DotaKing',
                image_url: null
            },
            {
                id: 4,
                name: 'CS:GO 武器箱',
                category: '道具',
                game: 'CS:GO',
                current_price: 45,
                seller_username: 'SkinTrader',
                image_url: null
            }
        ];

        const itemsGrid = document.getElementById('itemsGrid');
        if (itemsGrid && itemsGrid.children.length === 0) { // Only load if empty
            sampleItems.forEach(item => {
                const itemCard = this.createItemCard(item);
                itemsGrid.appendChild(itemCard);
            });
        }
    }

    async loadMarketItems(page = 1, filters = {}) {
        try {
            const params = new URLSearchParams({
                page: page.toString(),
                limit: this.itemsPerPage.toString(),
                ...filters
            });

            const response = await this.apiRequest(`/items?${params}`);
            if (response.success) {
                this.renderItems(response.data.items);
                this.renderPagination(response.data.pagination);
            }
        } catch (error) {
            console.error('Error loading market items:', error);
            this.showAlert('載入商品失敗，請稍後再試', 'error');
        }
    }

    renderItems(items) {
        const grid = document.getElementById('itemsGrid');
        grid.innerHTML = '';

        if (items.length === 0) {
            grid.innerHTML = '<div class="text-center">沒有找到商品</div>';
            return;
        }

        items.forEach(item => {
            const itemCard = this.createItemCard(item);
            grid.appendChild(itemCard);
        });
    }

    createItemCard(item) {
        const card = document.createElement('div');
        card.className = 'item-card';
        card.onclick = () => this.showItemDetail(item.id);

        // Use textContent instead of innerHTML to prevent XSS
        const itemImage = card.appendChild(document.createElement('div'));
        itemImage.className = 'item-image';
        if (item.image_url) {
            const img = document.createElement('img');
            img.src = item.image_url;
            img.alt = item.name;
            itemImage.appendChild(img);
        } else {
            itemImage.innerHTML = '<i class="fas fa-box"></i>';
        }

        const itemContent = card.appendChild(document.createElement('div'));
        itemContent.className = 'item-content';

        const itemName = itemContent.appendChild(document.createElement('div'));
        itemName.className = 'item-name';
        itemName.textContent = item.name;

        const itemCategory = itemContent.appendChild(document.createElement('div'));
        itemCategory.className = 'item-category';
        itemCategory.textContent = `${item.category} - ${item.game}`;

        const itemPrice = itemContent.appendChild(document.createElement('div'));
        itemPrice.className = 'item-price';
        itemPrice.textContent = `$${item.current_price}`;

        const itemSeller = itemContent.appendChild(document.createElement('div'));
        itemSeller.className = 'item-seller';
        itemSeller.textContent = `賣家: ${item.seller_username}`;

        return card;
    }

    renderPagination(pagination) {
        const paginationEl = document.getElementById('pagination');
        paginationEl.innerHTML = '';

        if (pagination.totalPages <= 1) return;

        const { page, totalPages, hasPrevPage, hasNextPage } = pagination;

        // Previous button
        if (hasPrevPage) {
            const prevBtn = document.createElement('button');
            prevBtn.textContent = '上一頁';
            prevBtn.onclick = () => this.loadMarketItems(page - 1);
            paginationEl.appendChild(prevBtn);
        }

        // Page numbers
        for (let i = Math.max(1, page - 2); i <= Math.min(totalPages, page + 2); i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i.toString();
            pageBtn.className = i === page ? 'active' : '';
            pageBtn.onclick = () => this.loadMarketItems(i);
            paginationEl.appendChild(pageBtn);
        }

        // Next button
        if (hasNextPage) {
            const nextBtn = document.createElement('button');
            nextBtn.textContent = '下一頁';
            nextBtn.onclick = () => this.loadMarketItems(page + 1);
            paginationEl.appendChild(nextBtn);
        }
    }

    async showItemDetail(itemId) {
        try {
            const response = await this.apiRequest(`/items/${itemId}`);
            if (response.success) {
                this.showItemDetailModal(response.data.item, response.data.reviews);
            }
        } catch (error) {
            this.showAlert('載入商品詳情失敗', 'error');
        }
    }

    showItemDetailModal(item, reviews) {
        const modal = document.createElement('div');
        modal.className = 'modal show';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${item.name}</h3>
                    <span class="modal-close">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="item-detail">
                        <div class="item-detail-image">
                            ${item.image_url ? `<img src="${item.image_url}" alt="${item.name}">` : '<i class="fas fa-box"></i>'}
                        </div>
                        <div class="item-detail-info">
                            <div class="item-price">$${item.current_price}</div>
                            <div class="item-meta">
                                <span>類別: ${item.category}</span>
                                <span>遊戲: ${item.game}</span>
                                <span>賣家: ${item.seller_username}</span>
                            </div>
                            <div class="item-description">
                                <h4>商品描述</h4>
                                <p>${item.description || '暫無描述'}</p>
                            </div>
                            ${this.user && this.user.id !== item.seller_id ? `
                                <button class="btn btn-primary" onclick="app.purchaseItem(${item.id})">
                                    購買商品
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Close modal
        modal.querySelector('.modal-close').onclick = () => {
            document.body.removeChild(modal);
        };

        modal.onclick = (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        };
    }

    async purchaseItem(itemId) {
        // This would integrate with the backend transaction system
        this.showAlert('購買功能即將推出！', 'info');
    }

    async handleLogin(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await this.apiRequest('/users/login', {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (response.success) {
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                this.updateUIForAuthenticatedUser();
                this.hideModal();
                this.showAlert('登入成功！', 'success');
            } else {
                this.showAlert(response.message || '登入失敗', 'error');
            }
        } catch (error) {
            this.showAlert('登入失敗，請檢查網路連線', 'error');
        }
    }

    async handleRegister(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await this.apiRequest('/users/register', {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (response.success) {
                this.showAlert('註冊成功！請檢查您的郵箱以驗證帳號。', 'success');
                this.hideModal();
                // Optionally switch to login modal
                setTimeout(() => this.showModal('login'), 2000);
            } else {
                this.showAlert(response.message || '註冊失敗', 'error');
            }
        } catch (error) {
            this.showAlert('註冊失敗，請檢查網路連線', 'error');
        }
    }

    showModal(modalType) {
        const modal = document.getElementById(`${modalType}Modal`);
        if (modal) {
            modal.classList.add('show');
        }
    }

    hideModal() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
        });
    }

    scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    updateActiveNavLink(activeId) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        const activeLink = document.querySelector(`[href="#${activeId}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }

    async handleContactSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            // This would send to backend contact endpoint
            this.showAlert('感謝您的訊息，我們會盡快回覆！', 'success');
            e.target.reset();
        } catch (error) {
            this.showAlert('發送失敗，請稍後再試', 'error');
        }
    }

    async apiRequest(endpoint, options = {}) {
        const url = `${this.apiUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        if (this.token) {
            config.headers.Authorization = `Bearer ${this.token}`;
        }

        // Add timeout support
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 seconds timeout

        try {
            config.signal = controller.signal;
            const response = await fetch(url, config);
            clearTimeout(timeoutId);
            
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                throw new Error('請求超時，請檢查網路連線');
            }
            throw error;
        }
    }

    logout() {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        this.updateUIForUnauthenticatedUser();
        this.showAlert('已成功登出', 'success');
    }

    showAlert(message, type = 'info') {
        // Sanitize message to prevent XSS
        const sanitizedType = (['success', 'error', 'warning', 'info'].includes(type)) ? type : 'info';
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${sanitizedType}`;
        alert.textContent = message; // Use textContent instead of innerHTML

        const container = document.querySelector('.container') || document.body;
        container.insertBefore(alert, container.firstChild);

        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 5000);
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new VirtualMarketApp();
});

// Global functions for HTML onclick handlers
function scrollToSection(sectionId) {
    if (window.app) {
        window.app.scrollToSection(sectionId);
    }
}

function showModal(modalType) {
    if (window.app) {
        window.app.showModal(modalType);
    }
}

function searchItems() {
    if (window.app) {
        const searchTerm = document.getElementById('searchInput').value;
        window.app.loadMarketItems(1, { search: searchTerm });
    }
}

function filterItems() {
    if (window.app) {
        const category = document.getElementById('categoryFilter').value;
        const game = document.getElementById('gameFilter').value;
        const filters = {};
        if (category) filters.category = category;
        if (game) filters.game = game;
        window.app.loadMarketItems(1, filters);
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('gameFilter').value = '';
    if (window.app) {
        window.app.loadMarketItems(1);
    }
}
