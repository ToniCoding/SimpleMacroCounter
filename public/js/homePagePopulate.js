/**
 * This is the HomePagePopulate script that gets the todays progress of the user by consuming the SMC API,
 * making the software more dynamic. This is part of the innovation of SMC to make easier future development.
 */

const caloricInformationMessage = "Today you consumed <b>{calories}</b> calories. You are <b>{remainingCalories} {over_under}</b> your goal of {calorieGoal} calories <i>({calorieProgress}%)</i>.";

/**
 * Consumes the API based on a given user ID.
 * @param {Number} userId - Used by the script main function that will be called with the user ID.
 * @returns - API response or null if error. 
 */
async function getTodayProgress(userId) {
    const endpoint = '/api/today-progress';

    console.info("[HomePagePopulate] Sending request to get progress parameters.");
    
    return fetch(endpoint, {
            method: "GET",
            credentials: "include"
    })
        .then(async (res) => {
            console.debug("[HomePagePopulate] Got response from endpoint.");

            const rawResponse = await res.text();

            try {
                console.log(JSON.parse(rawResponse));
                return JSON.parse(rawResponse);
            } catch (ex) {
                console.error("[HomePagePopulate] Error while parsing JSON response.");
                return null;
            }
    })
}

/**
 * Creates the HTML structure for all progress wrappers
 * @param {Object} todayUserMacroGramsConsumed - Object with consumed grams
 * @param {Object} dailyMacroGoals - Object with goal grams
 * @returns {string} HTML string of all wrappers
 */
function createProgressWrappers(todayUserMacroGramsConsumed, dailyMacroGoals) {
    const macroConfigs = [
        { name: "protein", svg: "strength", label: "Proteína" },
        { name: "carbs", svg: "carbs", label: "Carbohidratos" },
        { name: "fat", svg: "trans_fats", label: "Grasas" },
        { name: "fiber", svg: "wheat", label: "Fibra" }
    ];

    const consumed = {
        protein: todayUserMacroGramsConsumed?.proteinGrams || 0,
        carb: todayUserMacroGramsConsumed?.carbGrams || 0,
        fat: todayUserMacroGramsConsumed?.fatGrams || 0,
        fiber: todayUserMacroGramsConsumed?.fiberGrams || 0
    };

    const goals = {
        protein: dailyMacroGoals?.proteinGoal || 0,
        carb: dailyMacroGoals?.carbGoal || 0,
        fat: dailyMacroGoals?.fatGoal || 0,
        fiber: dailyMacroGoals?.fiberGoal || 0
    };

    return macroConfigs.map(({ name, svg }) => {
        const consumedGrams = consumed[name] || 0;
        const goalGrams = goals[name] || 0;

        return `
            <div class="progress-wrapper">
                <img src="/svg/${svg}.svg" alt="${name}" style="width:1.75em;height:1.75em;">
                <div class="progress-track">
                    <div class="progress-fill" id="${name}-track" data-progress="0"></div>
                </div>
                <span class="progress-text" id="${name}-info">${consumedGrams}g / ${goalGrams}g <sup><i>goal</i></sup></span>
            </div>
        `;
    }).join("");
}

/*
 * Function in charge of translating the JSON response into the actual DOM of the home page.
 * @param userId - The user ID to lookup.
 * @returns void - The function just populates the fields with API information.
 */
async function populateTracks(userId = 1) {
    const todayProgressInfo = await getTodayProgress(userId);

    if (!todayProgressInfo) {
        console.error("[HomePagePopulate] No data received");
        return;
    }

    const todayUserMacroProgress = todayProgressInfo.todayMacrosProgress;
    const todayUserMacroGrams = todayProgressInfo.todayUserMacroGrams;
    const dailyUserMacroGoal = todayProgressInfo.dailyMacroGramsGoal;
    const weeklyCalorieRiskInfo = todayProgressInfo.weeklyCalorieGoalRiskInfo;
    const weeklyCalorieConsumption = todayProgressInfo.weeklyCalorieConsumption;
    const weeklyCalorieGoal = todayProgressInfo.weeklyCalorieGoal;

    // ============================================
    // Update dynamic wrappers
    // ============================================
    const progressWrappersContainer = document.getElementById("progress-wrappers");
    
    if (progressWrappersContainer) {
        const wrappersHTML = createProgressWrappers(todayUserMacroGrams, dailyUserMacroGoal);
        progressWrappersContainer.innerHTML = wrappersHTML;
        console.debug("[HomePagePopulate] Progress wrappers creados dinámicamente");
    } else {
        console.error("[HomePagePopulate] Contenedor #progress-wrappers no encontrado");
        return;
    }

    // ============================================
    // Update progress trackers
    // ============================================
    const bars = {
        protein: document.getElementById("protein-track"),
        carbs: document.getElementById("carbs-track"),
        fats: document.getElementById("fat-track"),
        fiber: document.getElementById("fiber-track")
    };

    const progress = {
        protein: todayUserMacroProgress.proteinProgress || 0,
        carbs: todayUserMacroProgress.carbProgress || 0,
        fats: todayUserMacroProgress.fatProgress || 0,
        fiber: todayUserMacroProgress.fiberProgress || 0
    };

    Object.keys(bars).forEach(key => {
        if (bars[key]) {
            bars[key].dataset.progress = progress[key];
            bars[key].style.width = Math.min(progress[key], 100) + '%';
        }
    });

    // ============================================
    // Update wrappers texts
    // ============================================
    const infoElements = {
        protein: document.getElementById("protein-info"),
        carbs: document.getElementById("carbs-info"),
        fats: document.getElementById("fats-info"),
        fiber: document.getElementById("fiber-info")
    };

    const grams = {
        protein: todayUserMacroGrams.proteinGrams || 0,
        carbs: todayUserMacroGrams.carbGrams || 0,
        fats: todayUserMacroGrams.fatGrams || 0,
        fiber: todayUserMacroGrams.fiberGrams || 0
    };

    const goals = {
        protein: dailyUserMacroGoal.proteinGoal || 0,
        carbs: dailyUserMacroGoal.carbGoal || 0,
        fats: dailyUserMacroGoal.fatGoal || 0,
        fiber: dailyUserMacroGoal.fiberGoal || 0
    };

    Object.keys(infoElements).forEach(key => {
        if (infoElements[key]) {
            infoElements[key].innerHTML = `${grams[key]}g / ${goals[key]}g <sup><i>goal</i></sup>`;
        }
    });

    // ============================================
    // Update caloric message
    // ============================================
    const caloricProgress = document.getElementById("caloricProgress");
    if (caloricProgress) {
        const calorieDifference = Number(todayUserMacroGrams.calories) - Number(dailyUserMacroGoal.caloriesGoal);
        const caloricMessage = caloricInformationMessage
            .replace(/{calories}/g, todayUserMacroGrams.calories || 0)
            .replace(/{remainingCalories}/g, Math.abs(calorieDifference))
            .replace(/{over_under}/g, calorieDifference < 0 ? "under" : "over")
            .replace(/{calorieGoal}/g, dailyUserMacroGoal.caloriesGoal || 0)
            .replace(/{calorieProgress}/g, todayUserMacroProgress.calorieProgress || 0);
        caloricProgress.innerHTML = caloricMessage;
    }

    // ============================================
    // Update weekly balance and risks
    // ============================================
    const weeklyConsumption = document.getElementById("weekly-consumption");
    const weeklyGoal = document.getElementById("weekly-goal");
    
    if (weeklyConsumption) {
        weeklyConsumption.textContent = weeklyCalorieConsumption || 0;
    }
    if (weeklyGoal) {
        weeklyGoal.textContent = weeklyCalorieGoal || 0;
    }

    // ============================================
    // Update risk color
    // ============================================
    if (weeklyGoal && weeklyCalorieRiskInfo) {
        weeklyGoal.style.color = weeklyCalorieRiskInfo.risk_color || 'inherit';
    }

    console.debug("[HomePagePopulate] UI actualizada correctamente");
}

document.addEventListener('DOMContentLoaded', () => {
    populateTracks(1);
});
