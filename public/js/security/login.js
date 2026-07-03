/**
 * This script is in charge of listening to the submit element on the login page to contact endpoint and get the JWT token.
 */

import { auth } from './auth.js';

document.getElementById('loginForm').addEventListener('submit', async(e) => {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        await auth.login(username, password);
        window.location.href = 'google.com';
    } catch (error) {
        console.error('Login attempt failed!');
    }
})
