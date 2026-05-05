import { capitalize } from "./utils.js";

const foodCatalog = JSON.parse(
    document.getElementById('foodCatalog').dataset.config
);

const searchInput = document.getElementById('search-bar-food');

let currentFood = null;

/* =========================
   FILTER
========================= */

function filterFood(searchTerm) {
    if (!searchTerm || searchTerm.length < 2) return foodCatalog;

    return foodCatalog.filter(f =>
        f[0].toLowerCase().includes(searchTerm.toLowerCase())
    );
}

/* =========================
   MACRO CALC (LOCAL PER ROW)
========================= */

function calculate(food, grams) {
    const factor = (!grams || grams <= 0) ? 1 : grams / 100;

    return {
        protein: (food[3] * factor).toFixed(1),
        carbs: (food[4] * factor).toFixed(1),
        fats: (food[5] * factor).toFixed(1),
        fiber: (food[6] * factor).toFixed(1),
        calories: (food[2] * factor).toFixed(1),
    };
}

/* =========================
   MOBILE TABLE (NEW UX)
========================= */

function renderMobileTable(data) {
    const container = document.getElementById('foodMobileList');
    if (!container) return;

    container.innerHTML = '';

    data.forEach(food => {
        const row = document.createElement('div');
        row.classList.add('food-row');

        const name = food[0];
        const market = capitalize(food[1]);

        row.innerHTML = `
            <div class="food-row-main">
                <div>
                    <div class="food-name">${name}</div>
                    <div class="food-market">${market}</div>
                </div>
                <div></div>
                <div class="food-kcal">${food[2]} kcal</div>
            </div>

            <div class="food-row-details">

                <input class="row-grams-input" type="number" placeholder="Grams (100 default)" />

                <div class="macros-grid">
                    <div class="macro-box protein">Protein: ${food[3]}</div>
                    <div class="macro-box carbs">Carbs: ${food[4]}</div>
                    <div class="macro-box fats">Fats: ${food[5]}</div>
                    <div class="macro-box fiber">Fiber: ${food[6]}</div>
                    <div class="macro-box kcal">Calories: ${food[2]}</div>
                </div>

                <button class="select-btn">Register intake</button>
            </div>
        `;

        const gramsInput = row.querySelector('.row-grams-input');
        const btn = row.querySelector('.select-btn');

        /* ===== EXPAND ===== */
        row.querySelector(".food-row-main").addEventListener("click", () => {
            row.classList.toggle("active");
        });

        /* ===== LIVE CALC PER ROW ===== */
        gramsInput.addEventListener('input', () => {
            const grams = parseFloat(gramsInput.value);
            const calc = calculate(food, grams);

            row.querySelector('.protein').textContent = `Protein: ${calc.protein}`;
            row.querySelector('.carbs').textContent = `Carbs: ${calc.carbs}`;
            row.querySelector('.fats').textContent = `Fats: ${calc.fats}`;
            row.querySelector('.fiber').textContent = `Fiber: ${calc.fiber}`;
            row.querySelector('.kcal').textContent = `Calories: ${calc.calories}`;
        });

        /* ===== REGISTER INTAKE (NEW PRIMARY ACTION) ===== */
        btn.addEventListener('click', (e) => {
            e.stopPropagation();

            const grams = parseFloat(gramsInput.value) || 100;
            const calc = calculate(food, grams);

            // 👉 AQUÍ conectas backend luego
            console.log("REGISTER INTAKE:", {
                food,
                grams,
                macros: calc
            });

            row.classList.add('selected');
        });

        container.appendChild(row);
    });
}

/* =========================
   INIT
========================= */

function renderAll(data) {
    const desktop = document.getElementById('foodFoundRegistries');
    if (desktop) {
        desktop.innerHTML = '';
        data.forEach(f => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${f[0]}</td>
                <td>${capitalize(f[1])}</td>
                <td>${f[2]}</td>
                <td>${f[3]}</td>
                <td>${f[4]}</td>
                <td>${f[5]}</td>
                <td>${f[6]}</td>
            `;
            desktop.appendChild(tr);
        });
    }

    renderMobileTable(data);
}

if (searchInput) {
    renderAll(foodCatalog);

    searchInput.addEventListener('input', (e) => {
        renderAll(filterFood(e.target.value));
    });
}