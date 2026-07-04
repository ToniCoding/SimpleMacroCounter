import { auth } from './security/auth.js';

const manualMacroUpdateForm = document.getElementById('manualMacrosForm');

async function updateMacroIntake() {
    const endpoint = '/api/modify-macros';

    const addRadio = document.getElementById('add');
    const selectedRadio = addRadio.checked ? 'add' : 'reduce';

    const body = {
        protein: (document.getElementById('protein').value) * 1,
        carbs: (document.getElementById('carbs').value) * 1,
        fats: (document.getElementById('fats').value) * 1,
        fiber: (document.getElementById('fiber').value) * 1,
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