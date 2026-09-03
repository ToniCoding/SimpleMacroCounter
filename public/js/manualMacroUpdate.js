import { auth } from './security/auth.js';

const manualMacroUpdateForm = document.getElementById('manualMacrosForm');

async function updateMacroIntake() {
    const endpoint = '/api/modify-macros';

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

    console.info("[ManualMacroUpdate] Sending:", body);

    try {
        const response = await auth.fetch(endpoint, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.debug("[ManualMacroUpdate] Success:", data);
        return data;
    } catch (error) {
        console.error("[ManualMacroUpdate] Error:", error);
        return null;
    }
}

manualMacroUpdateForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    await updateMacroIntake();
});