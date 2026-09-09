/**
 * @file manualMacroUpdate.js
 * @description Manages the manual macro adjustment form interface. It gathers user inputs 
 * for macronutrients and the preferred action (adding or reducing), sanitizes the payload 
 * using the PayloadManager, and securely submits the update request to the API.
 */

import { PayloadManager } from '../utils/payloadManager.js';

const manualMacroUpdateForm = document.getElementById('manualMacrosForm');

/**
 * Collects input values from the macro adjustment form, converts them to proper numbers,
 * sanitizes the data structure, and sends the authenticated request to the server.
 * 
 * @async
 * @returns {Promise<Object|null>} The parsed server response if successful, or null if an error occurs.
 */
async function newUpdateMacroIntake() {
    const addRadio = document.getElementById('add');
    const selectedRadio = addRadio.checked ? 'add' : 'reduce';

    const protein = (document.getElementById('protein').value).replace(',', '.');
    const carbs = (document.getElementById('carbs').value).replace(',', '.');
    const fats = (document.getElementById('fats').value).replace(',', '.');
    const fiber = (document.getElementById('fiber').value).replace(',', '.');

    const body = {
        protein: protein * 1,
        carbs: carbs * 1,
        fats: fats * 1,
        fiber: fiber * 1,
        intent: selectedRadio
    };

    console.info("[ManualMacroUpdate] Sanitizing and forging:", body);

    try {
        const sanitizedBody = PayloadManager.payloadForger('modify_macros', body);
        const token = localStorage.getItem('jwt_token') || null; 
        const forgedRequest = PayloadManager.requestForger('post', 'modify_macros', 'bearer_auth', sanitizedBody, token);

        const data = await PayloadManager.requestSender(forgedRequest, true);

        console.debug("[ManualMacroUpdate] Success:", data);

        return data;
    } catch (error) {
        console.error("[ManualMacroUpdate] Error:", error);
        return null;
    }
}

manualMacroUpdateForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    newUpdateMacroIntake();
});
