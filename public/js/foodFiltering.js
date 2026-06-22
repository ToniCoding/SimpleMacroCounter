import { capitalize } from "./utils.js";
import { createIntakePayload } from "./payloadCreator.js";

const foodCatalogEl = document.getElementById("foodCatalog");
const searchInput = document.getElementById("search-bar-food");
const desktopTbody = document.getElementById("foodFoundRegistries");
const mobileList = document.getElementById("foodMobileList");
const gramsInput = document.getElementById("grams");
const btnSubmitIntake = document.getElementById("intake-submit-desktop");

const foodCatalog = foodCatalogEl
    ? JSON.parse(foodCatalogEl.dataset.config || "[]")
    : [];

let selectedFood = null;
let selectedRow = null;
let activeGrams = 100;

/* =========================
   MOBILE STATE (NEW)
========================= */
let activeMobileRow = null;

/* =========================
   SELECT CORE
========================= */
function clearSelection() {
    if (selectedRow) {
        selectedRow.classList.remove("selected-intake");
    }
    selectedRow = null;
    selectedFood = null;
}

function setSelected(row, food, grams) {
    clearSelection();

    selectedRow = row;
    selectedFood = food;

    row.classList.add("selected-intake");

    activeGrams = grams;

    updateUI(food, grams);

    console.log("[SELECT]", food[0], grams);
}

/* =========================
   CALC
========================= */
function getFactor(grams) {
    const g = parseFloat(grams);
    return !g || g <= 0 ? 1 : g / 100;
}

function calcScaled(food, grams) {
    const f = getFactor(grams);

    return {
        protein: (food[3] * f).toFixed(1),
        carbs: (food[4] * f).toFixed(1),
        fats: (food[5] * f).toFixed(1),
        fiber: (food[6] * f).toFixed(1),
        kcal: (food[2] * f).toFixed(1),
    };
}

/* =========================
   UI UPDATE DESKTOP PANEL
========================= */
function updateUI(food, grams) {
    if (!food) return;

    const s = calcScaled(food, grams);

    document.getElementById("selected-food-name").textContent = food[0];
    document.getElementById("macro-1").textContent = `Protein: ${s.protein}`;
    document.getElementById("macro-2").textContent = `Carbs: ${s.carbs}`;
    document.getElementById("macro-3").textContent = `Fats: ${s.fats}`;
    document.getElementById("macro-4").textContent = `Fiber: ${s.fiber}`;
    document.getElementById("macro-5").textContent = `Calories: ${s.kcal}`;
}

/* =========================
   DESKTOP
========================= */
function renderDesktop(data) {
    if (!desktopTbody) return;

    desktopTbody.innerHTML = "";

    data.forEach(food => {
        const tr = document.createElement("tr");
        tr.classList.add("food-search-row");

        tr.innerHTML = `
            <td>${food[0]}</td>
            <td>${capitalize(food[8])}</td>
            <td>${capitalize(food[1])}</td>
            
            <td>${food[2]}</td>
            <td>${food[3]}</td>
            <td>${food[4]}</td>
            <td>${food[5]}</td>
            <td>${food[6]}</td>
        `;

        tr.addEventListener("click", () => {
            const grams = gramsInput?.value === "" ? 100 : parseFloat(gramsInput?.value || 100);
            setSelected(tr, food, grams);
        });

        desktopTbody.appendChild(tr);
    });
}

/* =========================
   MOBILE CALC RENDER (NEW)
========================= */
function updateMobileMacros(row, food, grams) {
    const s = calcScaled(food, grams);

    row.querySelector(".macro-kcal").textContent = `Kcal: ${s.kcal}`;
    row.querySelector(".macro-protein").textContent = `P: ${s.protein}`;
    row.querySelector(".macro-carbs").textContent = `C: ${s.carbs}`;
    row.querySelector(".macro-fats").textContent = `F: ${s.fats}`;
    row.querySelector(".macro-fiber").textContent = `Fi: ${s.fiber}`;
}

/* =========================
   GLOBAL BUTTON
========================= */
btnSubmitIntake?.addEventListener("click", (e) => {
    e.preventDefault();

    if (!selectedFood) {
        console.warn("[INTAKE] no food selected");
        return;
    }

    createIntakePayload(selectedFood[7], gramsInput.value);
});

/* =========================
   MOBILE
========================= */
function createMobileRow(food) {
    const row = document.createElement("div");
    row.className = "food-row";

    row.innerHTML = `
        <div class="food-row-main">
            <div>
                <div class="food-name">${food[0]}</div>
                <div class="food-market">${capitalize(food[1])}</div>
            </div>
        </div>

        <div class="food-row-details">
            <div class="macros-grid">
                <div class="macro-box macro-kcal">Kcal: ${food[2]}</div>
                <div class="macro-box macro-protein">P: ${food[3]}</div>
                <div class="macro-box macro-carbs">C: ${food[4]}</div>
                <div class="macro-box macro-fats">F: ${food[5]}</div>
                <div class="macro-box macro-fiber">Fi: ${food[6]}</div>
            </div>

            <input class="grams-input" type="number" placeholder="grams" />

            <button class="select-btn">Select</button>
        </div>
    `;

    const header = row.querySelector(".food-row-main");
    const input = row.querySelector(".grams-input");
    const button = row.querySelector(".select-btn");

    const toggleOpen = () => {
        if (activeMobileRow && activeMobileRow !== row) {
            activeMobileRow.classList.remove("active");
        }

        row.classList.toggle("active");
        activeMobileRow = row;
    };

    const applySelection = () => {
        const grams = parseFloat(input.value || 100);
        setSelected(row, food, grams);
        updateMobileMacros(row, food, grams);
    };

    header.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleOpen();
        applySelection();
    });

    button.addEventListener("click", (e) => {
        e.stopPropagation();

        const grams = parseFloat(input.value || 100);

        setSelected(row, food, grams);
        updateMobileMacros(row, food, grams);

        console.log("[MOBILE INTAKE]", {
            foodId: food[7],
            gramsConsumed: grams
        });

        createIntakePayload(food[7], grams);
    });

    input.addEventListener("input", (e) => {
        e.stopPropagation();

        const grams = parseFloat(e.target.value || 100);
        activeGrams = grams;

        if (selectedFood && selectedRow === row) {
            updateMobileMacros(row, food, grams);
            updateUI(food, grams);
        }
    });

    return row;
}

function renderMobile(data) {
    if (!mobileList) return;

    mobileList.innerHTML = "";
    data.forEach(food => mobileList.appendChild(createMobileRow(food)));
}

let debounceTimer;

async function searchProductsApi(query) {
    if (query.length < 2) {
        renderDesktop(foodCatalog);
        renderMobile(foodCatalog);
        return;
    }

    try {
        const response = await fetch(`/api/search-products?q=${encodeURIComponent(query)}`);
        const results = await response.json();

        const formattedResults = results.map(p => [
            p.name,
            p.market,
            p.kcal,
            p.protein,
            p.carbs,
            p.fats,
            p.fiber,
            p.id,
            p.brand
        ]);

        renderDesktop(formattedResults);
        renderMobile(formattedResults);
    } catch (error) {
        console.error('Error en búsqueda API:', error);
    }
}

searchInput?.addEventListener("input", (e) => {
    const query = e.target.value.trim();
    
    clearTimeout(debounceTimer);
    
    if (query.length < 2) {
        renderDesktop(foodCatalog);
        renderMobile(foodCatalog);
        return;
    }
    
    debounceTimer = setTimeout(() => {
        searchProductsApi(query);
    }, 300);
});

/* =========================
   INIT
========================= */
function init() {
    renderDesktop(foodCatalog);
    renderMobile(foodCatalog);

    // searchInput?.addEventListener("input", (e) => {
    //     const clean = e.target.value.toLowerCase().trim();

    //     if (clean.length < 2) {
    //         renderDesktop(foodCatalog);
    //         renderMobile(foodCatalog);
    //         return;
    //     }

    //     const filtered = foodCatalog.filter(f =>
    //         f[0].toLowerCase().includes(clean) ||
    //         f[1].toLowerCase().includes(clean)
    //     );

    //     renderDesktop(filtered);
    //     renderMobile(filtered);
    // });

    gramsInput?.addEventListener("input", () => {
        const grams = gramsInput.value === "" ? 100 : parseFloat(gramsInput.value);
        activeGrams = grams;

        if (selectedFood && selectedRow) {
            updateUI(selectedFood, grams);
        }
    });
}

document.addEventListener("DOMContentLoaded", init);
