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
        this.baseURL = '';
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
            console.warn('Token expired. Renewing JWT.');
            return this.handleExpiredToken(endpoint, options);
        }

        return response;
    }

    async handleExpiredToken(endpoint, options) {
        try {
            const refreshResponse = await fetch('/generate-jwt', {
                method: 'POST',
                credentials: 'include'
            });

            if (!refreshResponse.ok) {
                throw new Error('Can not renew JWT.');
            }

            const data = await refreshResponse.json();
            const newToken = data.token;

            this.setToken(newToken);
            console.log('Successfully renewed JWT.');

            const headers = {
                'Content-Type': 'application/json',
                ...this.getAuthHeaders(),
                ...options.headers
            };

            const retryResponse = await fetch(`${this.baseURL}${endpoint}`, {
                ...options,
                headers
            });

            return retryResponse;

        } catch (error) {
            console.error('Error renewing JWT:', error);
            this.logout();
            throw new Error('Expired session, try again.');
        }
    }

    logout() {
        this.token = null;
        localStorage.removeItem('jwt_token');
        window.location.href = '/login';
    }
}

export const auth = new AuthService();
