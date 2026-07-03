/**
 * This script is in charge of parsing the user information and sending it directly to the API to get the user JWT.
 * Creates the payload and sends the request to the API.
 *      On success, sets the header the authentication header with the JWT int he response.
 *      On fail, returns the error to notify the user.
 *      On existing and non-expired JWT, returns the user to the home page.
 *      On existing but expired JWT, returns the warning message to notify the user.
 */

class AuthService {
    constructor() {
        this.token = null;
        this.refreshToken = null;
        this.baseURL = '/api';
    }

    async login(username, password) {
        try {
            const response = await fetch(`${this.baseURL}/login_check`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            if (!response.ok) {
                throw new Error('Invalid credentials.');
            }

            const data = await response.json();
            this.setToken(data.token);
            return data;
        } catch (error) {
            console.error('Login failed:', error);
            throw error;
        }
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('jwt_token', token);
    }

    getToken() {
        if (!this.token) {
            this.token = localStorage.getItem('jwt_token');
        }
        return this.token;
    }

    logout() {
        this.token = null;
        localStorage.removeItem('jwt_token');
        window.location.href = '/login';
    }

    isAuthenticated() {
        return !!this.getToken();
    }

    getAuthHeaders() {
        const token = this.getToken();
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    }

    async fetch(endpoint, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            ...this.getAuthHeaders(),
            ...options.headers
        };

        const response = await fetch(`${this.baseURL}${endpoint}`, {
            ...options,
            headers
        });

        if (response.status === 401) {
            this.logout();
            throw new Error('Your session has expired.');
        }

        return response;
    }
}

export const auth = new AuthService();
