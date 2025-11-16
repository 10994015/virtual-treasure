// Market Page JavaScript
class MarketPage {
    constructor() {
        this.apiUrl = 'http://localhost:3001/api';
        this.token = localStorage.getItem('token');
        this.currentPage = 1;
        this.itemsPerPage = 12;
        this.totalItems = 0;
        this.totalPages = 0;
        this.currentView = 'grid'; // 'grid' or 'list'
        this.currentSort = 'newest';
        this.filters = {
            search: '',
            categories: [],
            games: [],
            rarities: [],
            minPrice: '',
            maxPrice: ''
        };
        this.allItems = [];

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadItems();
    }

    setupEventListeners() {
        // View toggle
        document.getElementById('gridView').addEventListener('click', () => this.setView('grid'));
        document.getElementById('listView').addEventListener('click', () => this.setView('list'));

        // Sort change
        document.getElementById('sortSelect').addEventListener('change', (e) => {
            this.currentSort = e.target.value;
            this.sortAndDisplayItems();
        });

        // Search with debounce
        this.setupSearchDebounce();
    }

    // Debounce helper function
    debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    setupSearchDebounce() {
        const searchInput = document.getElementById('mainSearch');
        const debouncedSearch = this.debounce(() => this.searchItems(), 500);
        
        searchInput.addEventListener('keyup', debouncedSearch);
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                this.searchItems();
            }
        });
    }

    async loadItems() {
        try {
            this.showLoading();

            // Load sample items for demo
            this.allItems = this.generateSampleItems();
            this.totalItems = this.allItems.length;
            this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

            this.sortAndDisplayItems();
            this.updatePagination();
            this.updateItemsCount();
        } catch (error) {
            console.error('Failed to load items:', error);
            this.showError('載入商品失敗，請稍後再試');
        }
    }

    generateSampleItems() {
        const items = [];
        const categories = ['武器', '防具', '消耗品', '材料', '皮膚', '點數卡'];
        const games = ['World of Warcraft', 'League of Legends', 'Dota 2', 'CS:GO'];
        const rarities = ['普通', '優秀', '精良', '史詩', '傳說'];
        const sellers = ['GameMaster', 'ProGamer', 'SkinTrader', 'ItemDealer', 'GameShop'];

        for (let i = 1; i <= 48; i++) {
            const category = categories[Math.floor(Math.random() * categories.length)];
            const game = games[Math.floor(Math.random() * games.length)];
            const rarity = rarities[Math.floor(Math.random() * rarities.length)];
            const seller = sellers[Math.floor(Math.random() * sellers.length)];

            // Generate price based on rarity
            let basePrice = 10;
            switch (rarity) {
                case '優秀': basePrice = 25; break;
                case '精良': basePrice = 50; break;
                case '史詩': basePrice = 100; break;
                case '傳說': basePrice = 200; break;
            }
            const price = basePrice + Math.floor(Math.random() * basePrice * 0.5);

            items.push({
                id: i,
                name: this.generateItemName(category, game, rarity),
                category,
                game,
                rarity,
                price,
                seller_username: seller,
                image_url: null,
                description: this.generateDescription(category, game, rarity),
                created_at: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000),
                is_hot: Math.random() > 0.8
            });
        }

        return items;
    }

    generateItemName(category, game, rarity) {
        const prefixes = {
            '武器': ['無敵', '傳說', '神聖', '兇猛', '鋒利'],
            '防具': ['堅固', '魔法', '防護', '輕盈', '華麗'],
            '消耗品': ['神奇', '恢復', '強化', '治療', '補充'],
            '材料': ['稀有', '精煉', '優質', '純淨', '珍貴'],
            '皮膚': ['炫酷', '時尚', '炫彩', '夢幻', '經典'],
            '點數卡': ['超值', '豪華', '限定', '專屬', '尊貴']
        };

        const gameNames = {
            'World of Warcraft': ['艾澤拉斯', '奧格瑞瑪', '暴風城', '德萊尼', '血精靈'],
            'League of Legends': ['德瑪西亞', '諾克薩斯', '班德爾城', '皮爾托福', '祖安'],
            'Dota 2': ['守衛', '天災', '遠古', '虛空', '元素'],
            'CS:GO': ['沙漠', '森林', '海洋', '城市', '極地']
        };

        const suffix = {
            '武器': ['之劍', '之斧', '之弓', '之杖', '之槍'],
            '防具': ['護甲', '頭盔', '手套', '靴子', '披風'],
            '消耗品': ['藥水', '卷軸', '水晶', '精華', '符文'],
            '材料': ['礦石', '皮革', '木材', '布料', '金屬'],
            '皮膚': ['外觀', '主題', '風格', '造型', '配色'],
            '點數卡': ['點數包', '儲值卡', '禮包卡', '充值卡', '遊戲幣']
        };

        const prefix = prefixes[category][Math.floor(Math.random() * prefixes[category].length)];
        const gameName = gameNames[game][Math.floor(Math.random() * gameNames[game].length)];
        const itemSuffix = suffix[category][Math.floor(Math.random() * suffix[category].length)];

        return `${rarity}${prefix}${gameName}${itemSuffix}`;
    }

    generateDescription(category, game, rarity) {
        const descriptions = {
            '武器': [
                '這把武器擁有卓越的攻擊力，適合各種戰鬥場景。',
                '精工製作的武器，具有出色的耐用性和殺傷力。',
                '傳說中的武器，蘊含著強大的魔法力量。'
            ],
            '防具': [
                '堅固耐用的防具，能夠有效保護穿戴者。',
                '輕便舒適的裝備，同時提供優異的防護效果。',
                '附魔防具，具有特殊的魔法防護能力。'
            ],
            '消耗品': [
                '實用的消耗品，能夠在關鍵時刻發揮作用。',
                '高品質的恢復道具，快速恢復體力和魔力。',
                '稀有的魔法物品，具有多種特殊效果。'
            ],
            '材料': [
                '優質的製作材料，適合製作高級裝備。',
                '珍貴的稀有材料，具有特殊的魔法屬性。',
                '精煉過的材料，品質上乘，價值不菲。'
            ],
            '皮膚': [
                '炫酷的外觀設計，讓您的角色更加與眾不同。',
                '精美的美術設計，展現獨特的個性和風格。',
                '限量版皮膚，極具收藏價值。'
            ],
            '點數卡': [
                '官方正版點數卡，可直接儲值到遊戲帳號。',
                '超值點數包，充值即享額外加成獎勵。',
                '限時優惠點數卡，買越多送越多。'
            ]
        };

        const desc = descriptions[category][Math.floor(Math.random() * descriptions[category].length)];
        return `${desc} 這是來自${game}的${rarity}級${category}。`;
    }

    sortAndDisplayItems() {
        let filteredItems = this.filterItems(this.allItems);

        // Sort items
        filteredItems = this.sortItems(filteredItems);

        // Update pagination
        this.totalItems = filteredItems.length;
        this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

        // Get current page items
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageItems = filteredItems.slice(startIndex, endIndex);

        this.displayItems(pageItems);
        this.updatePagination();
        this.updateItemsCount();
    }

    filterItems(items) {
        return items.filter(item => {
            // Search filter
            if (this.filters.search && !item.name.toLowerCase().includes(this.filters.search.toLowerCase()) &&
                !item.description.toLowerCase().includes(this.filters.search.toLowerCase())) {
                return false;
            }

            // Category filter
            if (this.filters.categories.length > 0 && !this.filters.categories.includes(item.category)) {
                return false;
            }

            // Game filter
            if (this.filters.games.length > 0 && !this.filters.games.includes(item.game)) {
                return false;
            }

            // Rarity filter
            if (this.filters.rarities.length > 0 && !this.filters.rarities.includes(item.rarity)) {
                return false;
            }

            // Price filter
            if (this.filters.minPrice && item.price < parseFloat(this.filters.minPrice)) {
                return false;
            }
            if (this.filters.maxPrice && item.price > parseFloat(this.filters.maxPrice)) {
                return false;
            }

            return true;
        });
    }

    sortItems(items) {
        return items.sort((a, b) => {
            switch (this.currentSort) {
                case 'price-low':
                    return a.price - b.price;
                case 'price-high':
                    return b.price - a.price;
                case 'popular':
                    return (b.is_hot ? 1 : 0) - (a.is_hot ? 1 : 0);
                case 'newest':
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });
    }

    displayItems(items) {
        const container = document.getElementById('itemsContainer');
        container.className = this.currentView === 'grid' ? 'items-grid' : 'items-list';
        container.innerHTML = '';

        if (items.length === 0) {
            this.showEmptyState();
            return;
        }

        items.forEach(item => {
            const itemElement = this.currentView === 'grid' ? this.createItemCard(item) : this.createItemList(item);
            container.appendChild(itemElement);
        });
    }

    createItemCard(item) {
        const card = document.createElement('div');
        card.className = 'item-card';
        card.onclick = () => this.showItemDetail(item);

        card.innerHTML = `
            <div class="item-image">
                ${item.is_hot ? '<div class="item-badge">熱門</div>' : ''}
                <i class="fas fa-${this.getItemIcon(item.category)}"></i>
            </div>
            <div class="item-content">
                <div class="item-header">
                    <h3 class="item-name">${item.name}</h3>
                    <p class="item-price">$${item.price}</p>
                </div>
                <div class="item-meta">
                    <span><i class="fas fa-tag"></i> ${item.category}</span>
                    <span><i class="fas fa-gamepad"></i> ${item.game}</span>
                </div>
                <div class="item-seller">
                    <i class="fas fa-user"></i> ${item.seller_username}
                </div>
                <div class="item-actions">
                    <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); market.addToCart(${item.id})">
                        <i class="fas fa-cart-plus"></i> 加入購物車
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); market.purchaseItem(${item.id})">
                        <i class="fas fa-bolt"></i> 立即購買
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); market.addToFavorites(${item.id})">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        `;

        return card;
    }

    createItemList(item) {
        const listItem = document.createElement('div');
        listItem.className = 'item-list';
        listItem.onclick = () => this.showItemDetail(item);

        listItem.innerHTML = `
            <div class="item-list-image">
                ${item.is_hot ? '<div class="item-badge">熱門</div>' : ''}
                <i class="fas fa-${this.getItemIcon(item.category)}"></i>
            </div>
            <div class="item-list-content">
                <div class="item-list-header">
                    <div>
                        <h3 class="item-name">${item.name}</h3>
                        <div class="item-meta">
                            <span><i class="fas fa-tag"></i> ${item.category}</span>
                            <span><i class="fas fa-gamepad"></i> ${item.game}</span>
                            <span><i class="fas fa-star"></i> ${item.rarity}</span>
                        </div>
                    </div>
                    <div class="item-price">$${item.price}</div>
                </div>
                <div class="item-seller">
                    <i class="fas fa-user"></i> ${item.seller_username}
                </div>
                <p class="item-description">${item.description.substring(0, 100)}...</p>
                <div class="item-actions">
                    <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); market.addToCart(${item.id})">
                        <i class="fas fa-cart-plus"></i> 加入購物車
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); market.purchaseItem(${item.id})">
                        <i class="fas fa-bolt"></i> 立即購買
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); market.addToFavorites(${item.id})">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        `;

        return listItem;
    }

    getItemIcon(category) {
        const icons = {
            '武器': 'sword',
            '防具': 'shield-alt',
            '消耗品': 'potion',
            '材料': 'gem',
            '皮膚': 'palette'
        };
        return icons[category] || 'box';
    }

    showItemDetail(item) {
        document.getElementById('modalTitle').textContent = item.name;
        
        // Store current item for carousel control
        window.currentItem = item;
        window.currentImageIndex = 0;
        
        // Setup carousel images (假設有多張圖片)
        const images = item.images || [item.image_url || null];
        this.setupCarousel(images, item);
        
        document.getElementById('modalPrice').textContent = `$${item.price}`;
        document.getElementById('modalCategory').textContent = item.category;
        document.getElementById('modalGame').textContent = item.game;
        document.getElementById('modalRarity').textContent = item.rarity || '普通';
        document.getElementById('modalSeller').textContent = item.seller_username;
        document.getElementById('modalDescription').textContent = item.description;
        
        // Store seller info for chat
        window.currentSeller = {
            id: item.seller_id,
            username: item.seller_username
        };

        document.getElementById('itemModal').classList.add('show');
    }
    
    setupCarousel(images, item) {
        const container = document.getElementById('carouselContainer');
        const indicators = document.getElementById('carouselIndicators');
        
        // Clear previous content
        container.innerHTML = '';
        indicators.innerHTML = '';
        
        // Create image slides
        images.forEach((img, index) => {
            const slideDiv = document.createElement('div');
            slideDiv.className = 'item-modal-image';
            if (img) {
                slideDiv.innerHTML = `<img src="${img}" alt="商品圖片 ${index + 1}" style="width: 100%; height: 100%; object-fit: contain;">`;
            } else {
                slideDiv.innerHTML = `<i class="fas fa-${this.getItemIcon(item.category)}" style="color: white;"></i>`;
            }
            container.appendChild(slideDiv);
            
            // Create indicator dots
            const dot = document.createElement('div');
            dot.className = `carousel-dot ${index === 0 ? 'active' : ''}`;
            dot.onclick = () => this.goToImage(index);
            indicators.appendChild(dot);
        });
        
        window.totalImages = images.length;
        this.updateCarouselPosition();
    }
    
    updateCarouselPosition() {
        const container = document.getElementById('carouselContainer');
        const offset = -(window.currentImageIndex * 100);
        container.style.transform = `translateX(${offset}%)`;
        
        // Update dots
        document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === window.currentImageIndex);
        });
    }

    closeItemModal() {
        document.getElementById('itemModal').classList.remove('show');
    }

    setView(view) {
        this.currentView = view;
        document.getElementById('gridView').classList.toggle('active', view === 'grid');
        document.getElementById('listView').classList.toggle('active', view === 'list');
        this.sortAndDisplayItems();
    }

    searchItems() {
        const searchTerm = document.getElementById('mainSearch').value.trim();
        this.filters.search = searchTerm;
        this.currentPage = 1;
        this.sortAndDisplayItems();
    }

    toggleFilter(type, value) {
        const checkbox = document.getElementById(`${type}-${value.replace(/\s+/g, '-')}`);
        if (!checkbox) return;

        let filterArray;
        switch (type) {
            case 'category':
                filterArray = this.filters.categories;
                break;
            case 'game':
                filterArray = this.filters.games;
                break;
            case 'rarity':
                filterArray = this.filters.rarities;
                break;
            default:
                return;
        }

        const index = filterArray.indexOf(value);
        if (index > -1) {
            filterArray.splice(index, 1);
            checkbox.classList.remove('checked');
        } else {
            filterArray.push(value);
            checkbox.classList.add('checked');
        }

        this.currentPage = 1;
        this.sortAndDisplayItems();
    }

    clearAllFilters() {
        this.filters = {
            search: '',
            categories: [],
            games: [],
            rarities: [],
            minPrice: '',
            maxPrice: ''
        };

        // Clear search inputs
        document.getElementById('mainSearch').value = '';
        document.getElementById('filterSearch').value = '';
        document.getElementById('minPrice').value = '';
        document.getElementById('maxPrice').value = '';

        // Clear all checkboxes
        document.querySelectorAll('.filter-checkbox').forEach(cb => {
            cb.classList.remove('checked');
        });

        this.currentPage = 1;
        this.sortAndDisplayItems();
    }

    updatePagination() {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        if (this.totalPages <= 1) return;

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.textContent = '上一頁';
        prevBtn.disabled = this.currentPage === 1;
        prevBtn.onclick = () => this.goToPage(this.currentPage - 1);
        pagination.appendChild(prevBtn);

        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `pagination-btn ${i === this.currentPage ? 'active' : ''}`;
            pageBtn.textContent = i.toString();
            pageBtn.onclick = () => this.goToPage(i);
            pagination.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.textContent = '下一頁';
        nextBtn.disabled = this.currentPage === this.totalPages;
        nextBtn.onclick = () => this.goToPage(this.currentPage + 1);
        pagination.appendChild(nextBtn);
    }

    goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.sortAndDisplayItems();
        }
    }

    updateItemsCount() {
        const countEl = document.getElementById('itemsCount');
        countEl.textContent = `顯示 ${this.totalItems} 個商品`;
    }

    showLoading() {
        const container = document.getElementById('itemsContainer');
        container.innerHTML = `
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>載入商品中...</p>
            </div>
        `;
    }

    showEmptyState() {
        const container = document.getElementById('itemsContainer');
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>沒有找到符合條件的商品</h3>
                <p>請嘗試調整篩選條件或搜尋關鍵字</p>
                <button class="btn btn-primary" onclick="market.clearAllFilters()">清除篩選</button>
            </div>
        `;
    }

    showError(message) {
        const container = document.getElementById('itemsContainer');
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>載入失敗</h3>
                <p>${message}</p>
                <button class="btn btn-primary" onclick="market.loadItems()">重試</button>
            </div>
        `;
    }

    // Cart-related methods
    addToCart(itemId) {
        const item = this.allItems.find(item => item.id === itemId);
        if (!item) {
            this.showNotification('商品不存在', 'error');
            return;
        }

        // Import cart functionality (we'll assume shoppingCart is available globally)
        if (typeof shoppingCart !== 'undefined' && shoppingCart.addItem) {
            const cartItem = {
                id: item.id,
                name: item.name,
                price: item.price,
                image: item.image_url || `https://via.placeholder.com/300x200/667eea/white?text=${encodeURIComponent(item.name)}`,
                game: item.game,
                category: item.category,
                quantity: 1
            };
            shoppingCart.addItem(cartItem);
        } else {
            // Fallback: store in localStorage directly
            this.addToCartFallback(item);
        }
    }

    addToCartFallback(item) {
        const cartItem = {
            id: item.id,
            name: item.name,
            price: item.price,
            image: item.image_url || `https://via.placeholder.com/300x200/667eea/white?text=${encodeURIComponent(item.name)}`,
            game: item.game,
            category: item.category,
            quantity: 1
        };

        let cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
        const existingItem = cart.find(cartItem => cartItem.id === item.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push(cartItem);
        }

        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        this.showNotification(`${item.name} 已加入購物車`);
        this.updateCartCount();
    }

    updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            let cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            cartCount.textContent = count > 99 ? '99+' : count;
            cartCount.style.display = count > 0 ? 'inline-flex' : 'none';
        }
    }

    purchaseItem(itemId) {
        const item = this.allItems.find(item => item.id === itemId);
        if (!item) {
            this.showNotification('商品不存在', 'error');
            return;
        }

        // Direct purchase - add to cart and redirect to checkout
        this.addToCart(itemId);

        // Redirect to cart after a short delay
        setTimeout(() => {
            window.location.href = 'cart.html';
        }, 1000);
    }

    addToFavorites(itemId) {
        // Placeholder for favorites functionality
        this.showNotification('收藏功能即將推出！', 'info');
    }

    showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Show animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Hide after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }


    openChat() {
        if (window.customerService) {
            window.customerService.openChat();
        }
    }
}

// Global functions for HTML onclick handlers
function openChat() {
    const market = window.market || new MarketPage();
    if (market.openChat) {
        market.openChat();
    }
}

// Global functions for HTML onclick handlers
function toggleFilter(type, value) {
    if (window.market) {
        window.market.toggleFilter(type, value);
    }
}

function clearAllFilters() {
    if (window.market) {
        window.market.clearAllFilters();
    }
}

function searchItems() {
    if (window.market) {
        window.market.searchItems();
    }
}

function sortItems() {
    if (window.market) {
        window.market.sortAndDisplayItems();
    }
}

function setView(view) {
    if (window.market) {
        window.market.setView(view);
    }
}

function showItemDetail(item) {
    if (window.market) {
        window.market.showItemDetail(item);
    }
}

function closeItemModal() {
    if (window.market) {
        window.market.closeItemModal();
    }
}

function filterItems() {
    if (window.market) {
        const filterSearch = document.getElementById('filterSearch').value.trim();
        window.market.filters.search = filterSearch;
        window.market.currentPage = 1;
        window.market.sortAndDisplayItems();
    }
}

// Initialize market page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.market = new MarketPage();
});