/**
 * @file settingsUpdater.js
 * @description Manages the macro-nutrient intake goals modification form. It collects user inputs 
 * for calories and macronutrients, sanitizes the payload using the PayloadManager, and securely 
 * submits the POST request to the API with JWT authentication.
 */

import { PayloadManager } from '../utils/payloadManager.js';

const macroGoalsForm = document.getElementById('macroGoalsForm');

/**
 * Collects input values from the goals form, converts them to proper numbers,
 * sanitizes the data structure, and sends the authenticated POST request to the server.
 * 
 * @async
 * @returns {Promise<Object|null>} The parsed server response if successful, or null if an error occurs.
 */
async function updateMacroGoals() {
    const calories = document.getElementById('calories').value * 1;
    const protein = document.getElementById('protein').value * 1;
    const carbs = document.getElementById('carbs').value * 1;
    const fats = document.getElementById('fats').value * 1;
    const fiber = document.getElementById('fiber').value * 1;

    const body = {
        calories,
        protein,
        carbs,
        fats,
        fiber
    };

    console.info("[SettingsUpdater] Sanitizing and forging payload:", body);

    try {
        const sanitizedBody = PayloadManager.payloadForger('modify_goal_settings', body);
        const token = localStorage.getItem('jwt_token') || null; 
        const forgedRequest = PayloadManager.requestForger('put', 'settings', 'bearer_auth', sanitizedBody, token);

        const data = await PayloadManager.requestSender(forgedRequest, true);

        return data;
    } catch (error) {
        console.error("[SettingsUpdater] Error during request:", error);
        return null;
    }
}

if (macroGoalsForm) {
    macroGoalsForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await updateMacroGoals();
    });
}
