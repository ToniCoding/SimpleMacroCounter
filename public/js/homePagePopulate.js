/**
 * This is the HomePagePopulate script that gets the todays progress of the user by consuming the SMC API,
 * making the software more dynamic. This is part of the innovation of SMC to make easier future development.
 */

import { auth } from './security/auth.js';

const caloricInformationMessage = 'Today you consumed <b>{calories}</b> calories. ' + 
                                  'You are <b>{remainingCalories} {over_under}</b> your goal of {calorieGoal} ' +
                                  'calories <i>({calorieProgress}%)</i>.';

/**
 * Consumes the API based on a given user ID.
 * @param {Number} userId - Used by the script main function that will be called with the user ID.
 * @returns - API response or null if error. 
 */
async function getTodayProgress() {
    const endpoint = '/api/today-progress';

    console.info("[HomePagePopulate] Sending request to get progress parameters.");

    try {
        const response = await auth.fetch(endpoint, {
            method: "GET"
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.debug("[HomePagePopulate] Data received:", data);

        return data;
    } catch (error) {
        console.error("[HomePagePopulate] Error fetching data:", error);
        return null;
    }
}

/**
 * Creates the HTML structure for all progress wrappers
 * @param {Object} todayUserMacroGramsConsumed - Object with consumed grams
 * @param {Object} dailyMacroGoals - Object with goal grams
 * @returns {string} HTML string of all wrappers
 */
function createProgressWrappers(todayUserMacroGramsConsumed, dailyMacroGoals) {
    const macroConfigs = [
        { name: "protein", svg: "strength", label: "Protein" },
        { name: "carbs", svg: "carbs", label: "Carbs" },
        { name: "fat", svg: "trans_fats", label: "Fats" },
        { name: "fiber", svg: "wheat", label: "Fiber" }
    ];

    const consumed = {
        protein: todayUserMacroGramsConsumed?.protein || 0,
        carb: todayUserMacroGramsConsumed?.carbs || 0,
        fat: todayUserMacroGramsConsumed?.fats || 0,
        fiber: todayUserMacroGramsConsumed?.fiber || 0
    };

    const goals = {
        protein: dailyMacroGoals?.protein || 0,
        carb: dailyMacroGoals?.carbs || 0,
        fat: dailyMacroGoals?.fats || 0,
        fiber: dailyMacroGoals?.fiber || 0
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
        console.debug("[HomePagePopulate] Successfully created progress wrappers.");
    } else {
        console.error("[HomePagePopulate] Progress wrapper container not found.");
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
        carbs: todayUserMacroProgress.carbsProgress || 0,
        fats: todayUserMacroProgress.fatsProgress || 0,
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
        protein: (todayUserMacroGrams.protein).toFixed(2) || 0,
        carbs: (todayUserMacroGrams.carbs).toFixed(2) || 0,
        fats: (todayUserMacroGrams.fats).toFixed(2) || 0,
        fiber: (todayUserMacroGrams.fiber).toFixed(2) || 0
    };

    const goals = {
        protein: dailyUserMacroGoal.protein || 0,
        carbs: dailyUserMacroGoal.carbs || 0,
        fats: dailyUserMacroGoal.fats || 0,
        fiber: dailyUserMacroGoal.fiber || 0
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
        const calorieDifference = Number(todayUserMacroGrams.calories) - Number(dailyUserMacroGoal.calories);
        const caloricMessage = caloricInformationMessage
            .replace(/{calories}/g, todayUserMacroGrams.calories || 0)
            .replace(/{remainingCalories}/g, Math.abs(calorieDifference))
            .replace(/{over_under}/g, calorieDifference < 0 ? "under" : "over")
            .replace(/{calorieGoal}/g, dailyUserMacroGoal.calories || 0)
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

    console.debug("[HomePagePopulate] UI rendering success.");
}

document.addEventListener('DOMContentLoaded', () => {
    populateTracks();
});
