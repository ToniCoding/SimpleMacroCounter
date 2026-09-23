import { PayloadManager } from '../utils/payloadManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.querySelector('form');

    if (!registerForm) return;

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const rawPayloadData = {
            username: document.getElementById('username').value.trim(),
            password: document.getElementById('password').value,
            email: document.getElementById('email').value.trim(),
            age: parseInt(document.getElementById('age').value, 10) || 0
        };

        try {
            const forgedPayload = PayloadManager.payloadForger('register_user', rawPayloadData);

            const requestConfig = PayloadManager.requestForger(
                'POST',
                'register_user',
                'json_default',
                forgedPayload
            );

            const response = await PayloadManager.requestSender(requestConfig, true);

            console.log('[Register] ¡Successfully registered new user.', response.data);
            
        } catch (error) {
            console.error('[Register] Error while registering the new user:', error.message);
        }
    });
});
