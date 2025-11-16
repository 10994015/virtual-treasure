// UI Module - Handles UI interactions and effects
class UIManager {
    constructor(app) {
        this.app = app;
        this.init();
    }

    init() {
        this.setupScrollEffects();
        this.setupLoadingStates();
        this.setupResponsiveMenu();
    }

    setupScrollEffects() {
        // Navbar background on scroll
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Animate elements on scroll
        this.setupScrollAnimations();
    }

    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe elements that should animate in
        document.querySelectorAll('.feature-card, .stat, .about-text, .contact-form').forEach(el => {
            observer.observe(el);
        });
    }

    setupLoadingStates() {
        // Add loading class to buttons during async operations
        this.setupButtonLoadingStates();
    }

    setupButtonLoadingStates() {
        // This will be handled by individual button clicks in other modules
    }

    setupResponsiveMenu() {
        // Mobile menu toggle (if needed in the future)
        const navMenu = document.getElementById('navMenu');
        if (navMenu) {
            // Add mobile menu functionality if needed
        }
    }

    showLoading(element) {
        if (element) {
            element.classList.add('loading');
            element.disabled = true;
            element.innerHTML = '<span class="loading"></span> 處理中...';
        }
    }

    hideLoading(element, originalText = '') {
        if (element) {
            element.classList.remove('loading');
            element.disabled = false;
            if (originalText) {
                element.innerHTML = originalText;
            }
        }
    }

    createToast(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${this.getToastIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close">&times;</button>
        `;

        // Add to toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toast);

        // Setup close button
        toast.querySelector('.toast-close').onclick = () => {
            this.removeToast(toast);
        };

        // Auto remove
        setTimeout(() => {
            this.removeToast(toast);
        }, duration);

        return toast;
    }

    getToastIcon(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            case 'info': return 'fa-info-circle';
            default: return 'fa-info-circle';
        }
    }

    removeToast(toast) {
        toast.classList.add('fade-out');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    showConfirmDialog(message, onConfirm, onCancel) {
        const dialog = document.createElement('div');
        dialog.className = 'modal show confirm-dialog';
        dialog.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>確認操作</h3>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline" id="cancelBtn">取消</button>
                    <button class="btn btn-primary" id="confirmBtn">確認</button>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);

        const confirmBtn = dialog.querySelector('#confirmBtn');
        const cancelBtn = dialog.querySelector('#cancelBtn');

        confirmBtn.onclick = () => {
            if (onConfirm) onConfirm();
            document.body.removeChild(dialog);
        };

        cancelBtn.onclick = () => {
            if (onCancel) onCancel();
            document.body.removeChild(dialog);
        };

        // Click outside to cancel
        dialog.onclick = (e) => {
            if (e.target === dialog) {
                if (onCancel) onCancel();
                document.body.removeChild(dialog);
            }
        };
    }

    createSkeletonLoader(container, count = 1) {
        for (let i = 0; i < count; i++) {
            const skeleton = document.createElement('div');
            skeleton.className = 'skeleton';
            skeleton.innerHTML = `
                <div class="skeleton-image"></div>
                <div class="skeleton-content">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line short"></div>
                    <div class="skeleton-line"></div>
                </div>
            `;
            container.appendChild(skeleton);
        }
    }

    removeSkeletonLoaders(container) {
        const skeletons = container.querySelectorAll('.skeleton');
        skeletons.forEach(skeleton => skeleton.remove());
    }

    animateNumber(element, target, duration = 1000) {
        const start = parseInt(element.textContent) || 0;
        const increment = (target - start) / (duration / 16);
        let current = start;

        const animate = () => {
            current += increment;
            if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
                element.textContent = target.toString();
            } else {
                element.textContent = Math.floor(current).toString();
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    setupImageLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    createProgressBar(container, percentage) {
        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';
        progressBar.innerHTML = `
            <div class="progress-fill" style="width: ${percentage}%"></div>
        `;

        if (container) {
            container.appendChild(progressBar);
        }

        return progressBar;
    }

    updateProgressBar(progressBar, percentage) {
        const fill = progressBar.querySelector('.progress-fill');
        if (fill) {
            fill.style.width = `${percentage}%`;
        }
    }

    setupDarkMode() {
        const darkModeToggle = document.createElement('button');
        darkModeToggle.className = 'dark-mode-toggle';
        darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        darkModeToggle.onclick = () => this.toggleDarkMode();

        document.querySelector('.navbar').appendChild(darkModeToggle);
    }

    toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        const toggle = document.querySelector('.dark-mode-toggle i');

        if (document.body.classList.contains('dark-mode')) {
            toggle.className = 'fas fa-sun';
            localStorage.setItem('darkMode', 'true');
        } else {
            toggle.className = 'fas fa-moon';
            localStorage.setItem('darkMode', 'false');
        }
    }

    loadDarkModePreference() {
        const darkMode = localStorage.getItem('darkMode') === 'true';
        if (darkMode) {
            document.body.classList.add('dark-mode');
        }
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchInput').focus();
            }

            // Escape: Close modals
            if (e.key === 'Escape') {
                this.app.hideModal();
            }
        });
    }

    createNotificationBadge(element, count) {
        const existingBadge = element.querySelector('.notification-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        if (count > 0) {
            const badge = document.createElement('span');
            badge.className = 'notification-badge';
            badge.textContent = count > 99 ? '99+' : count.toString();
            element.appendChild(badge);
        }
    }

    setupInfiniteScroll(callback) {
        let loading = false;

        window.addEventListener('scroll', () => {
            if (loading) return;

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;

            if (scrollTop + windowHeight >= documentHeight - 100) {
                loading = true;
                callback().finally(() => {
                    loading = false;
                });
            }
        });
    }
}

// Add CSS for additional UI elements
const additionalStyles = `
/* Toast Notifications */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
}

.toast {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    min-width: 300px;
    max-width: 500px;
    opacity: 0;
    transform: translateX(100%);
    animation: slideIn 0.3s ease forwards;
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
}

.toast-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    opacity: 0.7;
    padding: 0 15px;
}

.toast-success { border-left: 4px solid #28a745; }
.toast-error { border-left: 4px solid #dc3545; }
.toast-warning { border-left: 4px solid #ffc107; }
.toast-info { border-left: 4px solid #17a2b8; }

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.fade-out {
    opacity: 0 !important;
    transform: translateX(100%) !important;
}

/* Confirm Dialog */
.confirm-dialog .modal-content {
    max-width: 400px;
}

.confirm-dialog .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Skeleton Loading */
.skeleton {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.skeleton-image {
    height: 200px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.skeleton-content {
    padding: 20px;
}

.skeleton-line {
    height: 16px;
    background: #f0f0f0;
    margin-bottom: 10px;
    border-radius: 4px;
    animation: loading 1.5s infinite;
}

.skeleton-line.short {
    width: 60%;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Progress Bar */
.progress-bar {
    width: 100%;
    height: 8px;
    background: #e1e5e9;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

/* Notification Badge */
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* Dark Mode */
.dark-mode {
    background: #1a202c;
    color: #e2e8f0;
}

.dark-mode .navbar,
.dark-mode .feature-card,
.dark-mode .contact-form {
    background: #2d3748;
    color: #e2e8f0;
}

.dark-mode .btn-outline {
    color: #e2e8f0;
    border-color: #e2e8f0;
}

.dark-mode .btn-outline:hover {
    background: #e2e8f0;
    color: #2d3748;
}

/* Scrolled Navbar */
.navbar.scrolled {
    background: rgba(102, 126, 234, 0.95);
    backdrop-filter: blur(10px);
}

/* Animate In */
.animate-in {
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
`;

// Inject additional styles
document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');
    style.textContent = additionalStyles;
    document.head.appendChild(style);
});

// Initialize UI manager when app is ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.app) {
        window.uiManager = new UIManager(window.app);
    }
});
