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

let activeMobileRow = null;

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

function updateMobileMacros(row, food, grams) {
    const s = calcScaled(food, grams);

    row.querySelector(".macro-kcal").textContent = `Kcal: ${s.kcal}`;
    row.querySelector(".macro-protein").textContent = `P: ${s.protein}`;
    row.querySelector(".macro-carbs").textContent = `C: ${s.carbs}`;
    row.querySelector(".macro-fats").textContent = `F: ${s.fats}`;
    row.querySelector(".macro-fiber").textContent = `Fi: ${s.fiber}`;
}

btnSubmitIntake?.addEventListener("click", (e) => {
    e.preventDefault();

    if (!selectedFood) {
        console.warn("[INTAKE] no food selected");
        return;
    }

    createIntakePayload(selectedFood[7], gramsInput.value);
});

function createMobileRow(food) {
    const row = document.createElement("div");
    row.className = "food-row";

    // Mobile: market + brand con clases
    const marketBrand = food[8] && food[8] !== 'unknown_brand' 
        ? `<span class="market-name">${capitalize(food[1])}</span><span class="separator"> - </span><span class="brand-name">${capitalize(food[8])}</span>` 
        : `<span class="market-name">${capitalize(food[1])}</span>`;

    row.innerHTML = `
        <div class="food-row-main">
            <div>
                <div class="food-name">${food[0]}</div>
                <div class="food-market">${marketBrand}</div>
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

async function searchProductsApi(query, page = 1) {
    if (query.length < 2) {
        renderDesktop(foodCatalog);
        renderMobile(foodCatalog);
        if (window.foodCatalogPagination) {
            updatePagination(window.foodCatalogPagination);
        }
        return;
    }

    try {
        const response = await fetch(`/api/search-products?q=${encodeURIComponent(query)}&page=${page}`);
        const result = await response.json();

        const formattedResults = result.data.map(p => [
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
        updatePagination(result.pagination);
    } catch (error) {
        console.error('Error en búsqueda API:', error);
    }
}

function updatePagination(pagination) {
    const paginationContainer = document.querySelector('.pagination-controls');
    if (paginationContainer) {
        const marketFilter = new URLSearchParams(window.location.search).get('market') || '';
        
        paginationContainer.innerHTML = `
            ${pagination.hasPrevious ? `<a href="#" class="btn-pagination" data-page="${pagination.currentPage - 1}" data-market="${marketFilter}">←</a>` : `<span class="btn-pagination disabled">←</span>`}
            <span class="page-info">Página ${pagination.currentPage} de ${pagination.totalPages} (${pagination.totalItems} productos)</span>
            ${pagination.hasNext ? `<a href="#" class="btn-pagination" data-page="${pagination.currentPage + 1}" data-market="${marketFilter}">→</a>` : `<span class="btn-pagination disabled">→</span>`}
        `;

        document.querySelectorAll('.pagination-controls .btn-pagination[data-page]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(btn.dataset.page);
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    searchProductsApi(query, page);
                } else {
                    window.location.href = `?pagination=${page}&market=${btn.dataset.market || ''}`;
                }
            });
        });
    }
}

function updatePaginationFromData(data) {
    if (window.foodCatalogPagination) {
        updatePagination(window.foodCatalogPagination);
    }
}

const foodCatalogPagination = window.foodCatalogPagination || null;

searchInput?.addEventListener("input", (e) => {
    const query = e.target.value.trim();

    clearTimeout(debounceTimer);

    if (query.length < 2) {
        renderDesktop(foodCatalog);
        renderMobile(foodCatalog);
        if (window.foodCatalogPagination) {
            updatePagination(window.foodCatalogPagination);
        }
        return;
    }

    debounceTimer = setTimeout(() => {
        searchProductsApi(query, 1);
    }, 300);
});

function init() {
    renderDesktop(foodCatalog);
    renderMobile(foodCatalog);

    if (window.foodCatalogPagination) {
        updatePagination(window.foodCatalogPagination);
    }

    gramsInput?.addEventListener("input", () => {
        const grams = gramsInput.value === "" ? 100 : parseFloat(gramsInput.value);
        activeGrams = grams;

        if (selectedFood && selectedRow) {
            updateUI(selectedFood, grams);
        }
    });
}

document.addEventListener("DOMContentLoaded", init);
