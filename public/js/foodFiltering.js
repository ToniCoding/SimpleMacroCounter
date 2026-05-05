import { capitalize } from "./utils.js";

const LOG = "[foodFilter]";
const activeLogging = false;
const foodCatalogEl = document.getElementById("foodCatalog");
const searchInput = document.getElementById("search-bar-food");
const desktopTbody = document.getElementById("foodFoundRegistries");
const mobileList = document.getElementById("foodMobileList");
const gramsInput = document.getElementById("grams");

const foodCatalog = foodCatalogEl
    ? JSON.parse(foodCatalogEl.dataset.config || "[]")
    : [];

let selectedFood = null;
let selectedRow = null;
let activeGrams = 100;

function log(...args) {
    if (activeLogging) console.log(LOG, ...args);
}

function warn(...args) {
    if (activeLogging) console.warn(LOG, ...args);
}

function getFactor(grams) {
    const g = parseFloat(grams);
    return !g || g <= 0 ? 1 : g / 100;
}

function calcScaled(food, grams) {
    const factor = getFactor(grams);
    return {
        protein: (food[3] * factor).toFixed(1),
        carbs: (food[4] * factor).toFixed(1),
        fats: (food[5] * factor).toFixed(1),
        fiber: (food[6] * factor).toFixed(1),
        kcal: (food[2] * factor).toFixed(1),
    };
}

function updateSelectedPanel(food, grams) {
    if (!food) return;

    const scaled = calcScaled(food, grams);

    const title = document.getElementById("selected-food-name");
    const macro1 = document.getElementById("macro-1");
    const macro2 = document.getElementById("macro-2");
    const macro3 = document.getElementById("macro-3");
    const macro4 = document.getElementById("macro-4");
    const macro5 = document.getElementById("macro-5");

    if (title) title.textContent = food[0];
    if (macro1) macro1.textContent = `Protein: ${scaled.protein}`;
    if (macro2) macro2.textContent = `Carbs: ${scaled.carbs}`;
    if (macro3) macro3.textContent = `Fats: ${scaled.fats}`;
    if (macro4) macro4.textContent = `Fiber: ${scaled.fiber}`;
    if (macro5) macro5.textContent = `Calories: ${scaled.kcal}`;

    log("[PANEL] updated", food[0], "grams:", grams, scaled);
}

function updateRowValues(row, food, grams) {
    const scaled = calcScaled(food, grams);

    const protein = row.querySelector(".js-protein");
    const carbs = row.querySelector(".js-carbs");
    const fats = row.querySelector(".js-fats");
    const fiber = row.querySelector(".js-fiber");
    const kcal = row.querySelector(".js-kcal");

    if (protein) protein.textContent = scaled.protein;
    if (carbs) carbs.textContent = scaled.carbs;
    if (fats) fats.textContent = scaled.fats;
    if (fiber) fiber.textContent = scaled.fiber;
    if (kcal) kcal.textContent = scaled.kcal;

    log("[ROW] updated", food[0], "grams:", grams, scaled);
}

function renderDesktop(data) {
    if (!desktopTbody) {
        warn("[DESKTOP] tbody not found");
        return;
    }

    log("[DESKTOP] rendering", data.length, "rows");
    desktopTbody.innerHTML = "";

    data.forEach(food => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${food[0]}</td>
            <td>${capitalize(food[1])}</td>
            <td>${food[2]}</td>
            <td>${food[3]}</td>
            <td>${food[4]}</td>
            <td>${food[5]}</td>
            <td>${food[6]}</td>
        `;
        desktopTbody.appendChild(tr);
    });
}

function createMobileRow(food) {
    const row = document.createElement("div");
    row.className = "food-row";
    row.dataset.search = `${food[0]} ${food[1]}`.toLowerCase();

    row.innerHTML = `
        <div class="food-row-main">
            <div>
                <div class="food-name">${food[0]}</div>
                <div class="food-market">${capitalize(food[1])}</div>
            </div>
            <div class="food-kcal">
                ${food[2]} kcal
            </div>
        </div>

        <div class="food-row-details">
            <div class="macros-grid">
                <div>Protein: <span class="js-protein">${food[3]}</span></div>
                <div>Carbs: <span class="js-carbs">${food[4]}</span></div>
                <div>Fats: <span class="js-fats">${food[5]}</span></div>
                <div>Fiber: <span class="js-fiber">${food[6]}</span></div>
            </div>

            <div class="macro-kcal">
                Calories: <span class="js-kcal">${food[2]}</span>
            </div>

            <div class="grams-wrap">
                <input class="grams-input" type="number" min="0" placeholder="Gramos" />
            </div>

            <button class="select-btn" type="button">Register intake</button>
        </div>
    `;

    const main = row.querySelector(".food-row-main");
    const gramsField = row.querySelector(".grams-input");
    const selectBtn = row.querySelector(".select-btn");

    main.addEventListener("click", () => {
        row.classList.toggle("active");
        log("[ROW] toggle expand", food[0], "active:", row.classList.contains("active"));
    });

    gramsField.addEventListener("input", (e) => {
        const grams = e.target.value === "" ? 100 : parseFloat(e.target.value);

        log("[GRAMS] input", food[0], "value:", grams);

        updateRowValues(row, food, grams);

        if (selectedRow === row) {
            activeGrams = grams;
            updateSelectedPanel(food, grams);
        }
    });

    selectBtn.addEventListener("click", (e) => {
        e.stopPropagation();

        if (selectedRow && selectedRow !== row) {
            selectedRow.classList.remove("selected");
        }

        selectedRow = row;
        selectedFood = food;

        row.classList.add("selected");

        const grams = gramsField.value === "" ? 100 : parseFloat(gramsField.value);
        activeGrams = grams;

        log("[SELECT] selected", food[0], "grams:", grams);

        updateRowValues(row, food, grams);
        updateSelectedPanel(food, grams);

        console.log("[REGISTER INTAKE]", {
            food: food[0],
            market: food[1],
            grams,
            scaled: calcScaled(food, grams),
        });
    });

    return row;
}

function renderMobile(data) {
    if (!mobileList) {
        warn("[MOBILE] container not found");
        return;
    }

    log("[MOBILE] rendering", data.length, "cards");
    mobileList.innerHTML = "";

    data.forEach(food => {
        const row = createMobileRow(food);
        mobileList.appendChild(row);
    });
}

function filterFood(term) {
    const clean = (term || "").trim().toLowerCase();
    log("[FILTER] term:", clean);

    if (clean.length < 2) {
        return foodCatalog;
    }

    return foodCatalog.filter(food =>
        food[0].toLowerCase().includes(clean) ||
        food[1].toLowerCase().includes(clean)
    );
}

function filterMobileVisibility(term) {
    if (!mobileList) return;

    const clean = (term || "").trim().toLowerCase();
    const rows = mobileList.querySelectorAll(".food-row");

    log("[MOBILE FILTER] term:", clean, "rows:", rows.length);

    rows.forEach(row => {
        const match =
            clean.length < 2 ||
            row.dataset.search.includes(clean);

        row.style.display = match ? "" : "none";
    });
}

function init() {
    log("[BOOT] init start");
    log("[BOOT] catalog items:", foodCatalog.length);

    renderDesktop(foodCatalog);
    renderMobile(foodCatalog);

    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const filtered = filterFood(e.target.value);

            renderDesktop(filtered);
            filterMobileVisibility(e.target.value);

            log("[SEARCH] rendered desktop:", filtered.length);
        });
    } else {
        warn("[BOOT] search input not found");
    }

    if (gramsInput) {
        gramsInput.addEventListener("input", () => {
            const grams = gramsInput.value === "" ? 100 : parseFloat(gramsInput.value);

            log("[GLOBAL GRAMS] input:", grams);

            activeGrams = grams;

            if (selectedFood) {
                updateSelectedPanel(selectedFood, grams);
                if (selectedRow) {
                    updateRowValues(selectedRow, selectedFood, grams);
                }
            }
        });
    } else {
        warn("[BOOT] global grams input not found");
    }

    log("[BOOT] init done");
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
