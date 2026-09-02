/**
 * AuthService
 * 
 * Client-side authentication service responsible for:
 * - Requesting and storing JWT tokens in LocalStorage and memory.
 * - Injecting Bearer Authorization headers into outgoing HTTP requests.
 * - Intercepting 401 Unauthorized responses to attempt transparent token renewal and request retry.
 * - Managing user session cleanup and redirects on logout/expiration.
 */

class AuthService {
    constructor() {
        this.authEndpoint = 'api/generate-jwt';
        this.token = null;
        this.baseURL = '';
        this.initTokenFromSession();
    }

    initTokenFromSession() {
        const configEl = document.getElementById('app-config');
        
        if (configEl && configEl.dataset.jwtToken) {
            this.setToken(configEl.dataset.jwtToken);
        }
    }

    /**
     * Saves the JWT token in the clients storage.
     * @param {*} token JWT token.
     */
    setToken(token) {
        this.token = token;
        localStorage.setItem('jwt_token', token);
    }

    /**
     * Gets the token. If it is not present, tries to get it from the clients local storage.
     * @returns JWT token.
     */
    getToken() {
        if (!this.token) {
            this.token = localStorage.getItem('jwt_token');
        }
        return this.token;
    }

    /**
     * If present, returns the JWT token ready to use as a header.
     * @returns JWT token ready to use as a header.
     */
    getAuthHeaders() {
        const token = this.getToken();
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    }

    /**
     * --- DEPRECATED ---
     * Fetchs the JWT token for the user that is trying to log in and saves it in the client's local storage.
     * @param {string} username
     * @param {string} password
     */
    async getJwtToken(username, password) {
        const body = { username, password };

        try {
            const response = await fetch(this.authEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(body)
            });

            if (!response.ok) {
                throw new Error(`Authentication failed: ${response.status}`);
            }

            const data = await response.json();

            if (data.token) {
                this.setToken(data.token);
            } else {
                throw new Error('The response does not include a field named token.');
            }

        } catch (error) {
            localStorage.removeItem('jwt_token');
            console.error('There was an error trying to fetch the JWT token. Reason:', error);
            throw error;
        }
    }

    /**
     * Fetch information from the server using the JWT token sent as request header as authentication method.
     * @param {*} endpoint Endpoint to fetch data.
     * @param {*} options Options for the request.
     * @returns Server response to the request.
     */
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

    /**
     * Renews an expired token. If called from another function, it will re-send the previous request with the renewed token.
     * @param {*} endpoint Endpoint to fetch data.
     * @param {*} options Options for the request.
     * @returns Server response to the request.
     */
    async handleExpiredToken(endpoint, options) {
        try {
            const refreshResponse = await fetch(this.authEndpoint, {
                method: 'POST',
                credentials: 'include'
            });

            if (!refreshResponse.ok) {
                throw new Error('Cannot renew JWT.');
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

    /**
     * Removes the JWT token from the local storage and relocates the user to the login page.
     */
    logout() {
        this.token = null;
        localStorage.removeItem('jwt_token');
        window.location.href = '/login';
    }
}

export const auth = new AuthService();
