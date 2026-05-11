import { capitalize } from "./utils.js";
import { createIntakePayload } from "./payloadCreator.js";

const LOG = "[foodFilter]";
const activeLogging = false;

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
   UI UPDATE
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
   GLOBAL BUTTON
========================= */
btnSubmitIntake?.addEventListener("click", (e) => {
    e.preventDefault();

    if (!selectedFood) {
        console.warn("[INTAKE] no food selected");
        return;
    }

    console.log("[INTAKE] sending request...");
    console.log("[INTAKE] food:", selectedFood[0]);
    console.log("[INTAKE] grams:", activeGrams);

    createIntakePayload(selectedFood[7], gramsInput.value);
});

/* =========================
   MOBILE
========================= */
function createMobileRow(food) {
    const row = document.createElement("div");
    row.className = "food-row";

    row.innerHTML = `
        <div>${food[0]}</div>
        <div>${capitalize(food[1])}</div>
    `;

    row.addEventListener("click", () => {
        const grams = gramsInput?.value === "" ? 100 : parseFloat(gramsInput?.value || 100);
        setSelected(row, food, grams);
    });

    return row;
}

function renderMobile(data) {
    if (!mobileList) return;

    mobileList.innerHTML = "";
    data.forEach(food => mobileList.appendChild(createMobileRow(food)));
}

/* =========================
   INIT
========================= */
function init() {
    renderDesktop(foodCatalog);
    renderMobile(foodCatalog);

    searchInput?.addEventListener("input", (e) => {
        const clean = e.target.value.toLowerCase();

        if (clean.length < 2) {
            renderDesktop(foodCatalog);
            return;
        }

        const filtered = foodCatalog.filter(f =>
            f[0].toLowerCase().includes(clean) ||
            f[1].toLowerCase().includes(clean)
        );

        renderDesktop(filtered);
    });

    gramsInput?.addEventListener("input", () => {
        const grams = gramsInput.value === "" ? 100 : parseFloat(gramsInput.value);
        activeGrams = grams;

        if (selectedFood && selectedRow) {
            updateUI(selectedFood, grams);
        }
    });
}

document.addEventListener("DOMContentLoaded", init);
