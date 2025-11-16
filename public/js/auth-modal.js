// Authentication Modal Manager
class AuthModalManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupModalButtons();
        this.setupFormValidation();
        this.setupPasswordToggles();
        this.setupPasswordStrength();
    }

    // Setup modal open/close buttons
    setupModalButtons() {
        // Login buttons
        const loginBtns = document.querySelectorAll('[onclick="openLoginModal()"]');
        loginBtns.forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                this.openLoginModal();
            };
        });

        // Register buttons
        const registerBtns = document.querySelectorAll('[onclick="openRegisterModal()"]');
        registerBtns.forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                this.openRegisterModal();
            };
        });
    }

    // Open login modal
    openLoginModal() {
        document.getElementById('loginModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('loginEmail').focus();
    }

    // Close login modal
    closeLoginModal() {
        document.getElementById('loginModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        this.clearLoginForm();
    }

    // Open register modal
    openRegisterModal() {
        document.getElementById('registerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('registerUsername').focus();
    }

    // Close register modal
    closeRegisterModal() {
        document.getElementById('registerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        this.clearRegisterForm();
    }

    // Switch to register modal
    switchToRegister() {
        this.closeLoginModal();
        setTimeout(() => {
            this.openRegisterModal();
        }, 300);
    }

    // Switch to login modal
    switchToLogin() {
        this.closeRegisterModal();
        setTimeout(() => {
            this.openLoginModal();
        }, 300);
    }

    // Setup form validation
    setupFormValidation() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }
    }

    // Handle login
    async handleLogin(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('loginSubmitBtn');
        const btnText = document.getElementById('loginBtnText');
        const originalText = btnText.textContent;

        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = '登入中...';

        try {
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Basic validation
            if (!data.email || !data.password) {
                throw new Error('請填寫所有必填欄位');
            }

            // Simulate API call (replace with actual API)
            await this.delay(1500);

            // Mock successful login
            this.showLoginSuccess('登入成功！');
            this.closeLoginModal();

            // Here you would normally store the token and redirect
            console.log('Login successful:', data.email);

        } catch (error) {
            this.showLoginError(error.message || '登入失敗，請檢查您的憑證');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = originalText;
        }
    }

    // Handle register
    async handleRegister(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('registerSubmitBtn');
        const btnText = document.getElementById('registerBtnText');
        const originalText = btnText.textContent;

        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = '創建中...';

        try {
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Validation
            if (!data.username || !data.email || !data.password || !data.confirmPassword) {
                throw new Error('請填寫所有必填欄位');
            }

            if (data.password !== data.confirmPassword) {
                throw new Error('密碼確認不相符');
            }

            if (data.password.length < 6) {
                throw new Error('密碼至少需要6個字符');
            }

            if (!document.getElementById('termsAgree').checked) {
                throw new Error('請同意服務條款和隱私政策');
            }

            // Simulate API call (replace with actual API)
            await this.delay(2000);

            // Mock successful registration
            this.showRegisterSuccess('帳號創建成功！請檢查您的電子郵件以驗證帳號。');
            this.closeRegisterModal();

            // Here you would normally store the token and redirect
            console.log('Registration successful:', data.email);

        } catch (error) {
            this.showRegisterError(error.message || '註冊失敗，請稍後再試');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = originalText;
        }
    }

    // Setup password visibility toggles
    setupPasswordToggles() {
        // Login password toggle
        const loginToggle = document.querySelector('[onclick="togglePassword(\'loginPassword\')"]');
        if (loginToggle) {
            loginToggle.onclick = () => this.togglePasswordVisibility('loginPassword');
        }

        // Register password toggle
        const registerToggle = document.querySelector('[onclick="togglePassword(\'registerPassword\')"]');
        if (registerToggle) {
            registerToggle.onclick = () => this.togglePasswordVisibility('registerPassword');
        }
    }

    // Toggle password visibility
    togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + 'Icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    // Setup password strength indicator
    setupPasswordStrength() {
        const passwordInput = document.getElementById('registerPassword');
        if (passwordInput) {
            passwordInput.addEventListener('input', (e) => {
                this.updatePasswordStrength(e.target.value);
            });
        }
    }

    // Update password strength indicator
    updatePasswordStrength(password) {
        const strengthBars = ['strength1', 'strength2', 'strength3', 'strength4'];
        const strengthText = document.getElementById('passwordStrength');

        let strength = 0;

        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        // Update bars
        strengthBars.forEach((barId, index) => {
            const bar = document.getElementById(barId);
            if (index < strength) {
                bar.className = `w-1/4 h-1 rounded-full ${
                    strength === 1 ? 'bg-red-400' :
                    strength === 2 ? 'bg-yellow-400' :
                    strength === 3 ? 'bg-blue-400' :
                    'bg-green-400'
                }`;
            } else {
                bar.className = 'w-1/4 h-1 bg-gray-200 rounded-full';
            }
        });

        // Update text
        const strengthTexts = ['很弱', '弱', '中等', '強', '很強'];
        strengthText.textContent = `密碼強度：${strengthTexts[strength] || '很弱'}`;
        strengthText.className = `text-xs mt-1 ${
            strength === 1 ? 'text-red-500' :
            strength === 2 ? 'text-yellow-500' :
            strength === 3 ? 'text-blue-500' :
            strength === 4 ? 'text-green-500' :
            'text-gray-500'
        }`;
    }

    // Show login error
    showLoginError(message) {
        const errorDiv = document.getElementById('loginError');
        const errorText = document.getElementById('loginErrorText');
        errorText.textContent = message;
        errorDiv.classList.remove('hidden');

        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }

    // Show login success
    showLoginSuccess(message) {
        this.showNotification(message, 'success');
    }

    // Show register error
    showRegisterError(message) {
        const errorDiv = document.getElementById('registerError');
        const errorText = document.getElementById('registerErrorText');
        errorText.textContent = message;
        errorDiv.classList.remove('hidden');

        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }

    // Show register success
    showRegisterSuccess(message) {
        const successDiv = document.getElementById('registerSuccess');
        const successText = document.getElementById('registerSuccessText');
        successText.textContent = message;
        successDiv.classList.remove('hidden');

        setTimeout(() => {
            successDiv.classList.add('hidden');
        }, 5000);
    }

    // Show notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-system-lg shadow-system-lg ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            'bg-blue-500'
        } text-white max-w-sm`;

        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-triangle' :
                    'fa-info-circle'
                } mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Clear login form
    clearLoginForm() {
        const form = document.getElementById('loginForm');
        if (form) {
            form.reset();
            document.getElementById('loginError').classList.add('hidden');
        }
    }

    // Clear register form
    clearRegisterForm() {
        const form = document.getElementById('registerForm');
        if (form) {
            form.reset();
            document.getElementById('registerError').classList.add('hidden');
            document.getElementById('registerSuccess').classList.add('hidden');

            // Reset password strength
            ['strength1', 'strength2', 'strength3', 'strength4'].forEach(id => {
                document.getElementById(id).className = 'w-1/4 h-1 bg-gray-200 rounded-full';
            });
            document.getElementById('passwordStrength').textContent = '密碼強度';
            document.getElementById('passwordStrength').className = 'text-xs text-gray-500 mt-1';
        }
    }

    // Utility delay function
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Global functions for HTML onclick attributes
function openLoginModal() {
    if (window.authModalManager) {
        window.authModalManager.openLoginModal();
    }
}

function closeLoginModal() {
    if (window.authModalManager) {
        window.authModalManager.closeLoginModal();
    }
}

function openRegisterModal() {
    if (window.authModalManager) {
        window.authModalManager.openRegisterModal();
    }
}

function closeRegisterModal() {
    if (window.authModalManager) {
        window.authModalManager.closeRegisterModal();
    }
}

function switchToRegister() {
    if (window.authModalManager) {
        window.authModalManager.switchToRegister();
    }
}

function switchToLogin() {
    if (window.authModalManager) {
        window.authModalManager.switchToLogin();
    }
}

function togglePassword(inputId) {
    if (window.authModalManager) {
        window.authModalManager.togglePasswordVisibility(inputId);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.authModalManager = new AuthModalManager();
});
