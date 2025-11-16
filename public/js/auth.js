// Authentication Module
class AuthManager {
    constructor(app) {
        this.app = app;
        this.init();
    }

    init() {
        this.setupAuthForms();
    }

    setupAuthForms() {
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

    async handleLogin(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await this.app.apiRequest('/users/login', {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (response.success) {
                this.app.token = response.data.token;
                this.app.user = response.data.user;
                localStorage.setItem('token', this.app.token);

                this.app.updateUIForAuthenticatedUser();
                this.app.hideModal();
                this.app.showAlert('登入成功！', 'success');

                // Reload market data
                this.app.loadMarketItems();
            }
        } catch (error) {
            this.app.showAlert('登入失敗：' + error.message, 'error');
        }
    }

    async handleRegister(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Validate password confirmation
        if (data.password !== data.confirmPassword) {
            this.app.showAlert('密碼確認不相符', 'error');
            return;
        }

        // Remove confirmPassword from data
        delete data.confirmPassword;

        try {
            const response = await this.app.apiRequest('/users/register', {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (response.success) {
                this.app.token = response.data.token;
                this.app.user = response.data.user;
                localStorage.setItem('token', this.app.token);

                this.app.updateUIForAuthenticatedUser();
                this.app.hideModal();
                this.app.showAlert('註冊成功！', 'success');

                // Reload market data
                this.app.loadMarketItems();
            }
        } catch (error) {
            this.app.showAlert('註冊失敗：' + error.message, 'error');
        }
    }

    async updateProfile(profileData) {
        try {
            const response = await this.app.apiRequest('/users/profile', {
                method: 'PUT',
                body: JSON.stringify(profileData)
            });

            if (response.success) {
                this.app.user = response.data.user;
                this.app.showAlert('個人資料更新成功！', 'success');
            }
        } catch (error) {
            this.app.showAlert('更新失敗：' + error.message, 'error');
        }
    }

    async addGameAccount(gameData) {
        try {
            const response = await this.app.apiRequest('/users/game-accounts', {
                method: 'POST',
                body: JSON.stringify(gameData)
            });

            if (response.success) {
                this.app.showAlert('遊戲帳號添加成功！', 'success');
            }
        } catch (error) {
            this.app.showAlert('添加失敗：' + error.message, 'error');
        }
    }

    async getGameAccounts() {
        try {
            const response = await this.app.apiRequest('/users/game-accounts');

            if (response.success) {
                return response.data.gameAccounts;
            }
        } catch (error) {
            this.app.showAlert('獲取遊戲帳號失敗：' + error.message, 'error');
        }
        return [];
    }
}

// Initialize auth manager when app is ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.app) {
        window.authManager = new AuthManager(window.app);
    }
});
