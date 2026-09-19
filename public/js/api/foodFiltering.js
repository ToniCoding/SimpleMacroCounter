/**
 * @fileoverview High-performance asynchronous food catalog manager.
 * Integrates PayloadManager with Bearer authentication, request abortion for concurrency control,
 * DocumentFragment DOM batching for rendering large datasets, URL parameter synchronization, 
 * and debounced search mechanics.
 */

import { capitalize } from "../utils/utils.js";
import { PayloadManager } from "../utils/payloadManager.js";

class FoodCatalogManager {
    /**
     * Initializes state, caches DOM elements, and binds event listeners.
     */
    constructor() {
        this.foodCatalogEl = document.getElementById("foodCatalog");
        this.searchInput = document.getElementById("search-bar-food");
        this.desktopTbody = document.getElementById("foodFoundRegistries");
        this.mobileList = document.getElementById("foodMobileList");
        this.gramsInput = document.getElementById("grams");
        this.btnSubmitIntake = document.getElementById("intake-submit-desktop");

        this.foodCatalog = this.foodCatalogEl
            ? JSON.parse(this.foodCatalogEl.dataset.config || "[]")
            : [];

        this.selectedFood = null;
        this.selectedRow = null;
        this.activeGrams = 100;
        this.activeMobileRow = null;
        this.debounceTimer = null;
        this.abortController = null;

        this.init();
    }

    /**
     * Clears the current table row selection state.
     */
    clearSelection() {
        if (this.selectedRow) {
            this.selectedRow.classList.remove("selected-intake");
        }
        this.selectedRow = null;
        this.selectedFood = null;
    }

    /**
     * Sets the active selected food row, updates internal state, and refreshes the UI.
     * @param {HTMLElement} row - DOM row element.
     * @param {Array} food - Food data array.
     * @param {number} grams - Grams quantity.
     */
    setSelected(row, food, grams) {
        this.clearSelection();

        this.selectedRow = row;
        this.selectedFood = food;

        row.classList.add("selected-intake");
        this.activeGrams = grams;

        this.updateUI(food, grams);

        console.log(`[FoodCatalogManager] User selected food <name: ${food[0]}, market: ${food[1]}, brand: ${food[8]}, macros: P:${food[3]} C:${food[4]} F:${food[5]} Fi:${food[6]}, kcals: ${food[2]}, grams: ${grams}>.`);
    }

    /**
     * Calculates the scaling factor based on the given grams.
     * @param {number|string} grams - Grams quantity.
     * @returns {number} Scaling factor.
     */
    getFactor(grams) {
        const g = parseFloat(grams);
        return !g || g <= 0 ? 1 : g / 100;
    }

    /**
     * Scales nutritional values based on the gram quantity.
     * @param {Array} food - Food data array.
     * @param {number} grams - Grams quantity.
     * @returns {Object} Scaled macros and kcal.
     */
    calcScaled(food, grams) {
        const f = this.getFactor(grams);

        return {
            protein: (food[3] * f).toFixed(1),
            carbs: (food[4] * f).toFixed(1),
            fats: (food[5] * f).toFixed(1),
            fiber: (food[6] * f).toFixed(1),
            kcal: (food[2] * f).toFixed(1),
        };
    }

    /**
     * Updates the UI macro preview elements with scaled values.
     * @param {Array} food - Food data array.
     * @param {number} grams - Grams quantity.
     */
    updateUI(food, grams) {
        if (!food) return;

        const s = this.calcScaled(food, grams);

        document.getElementById("selected-food-name").textContent = food[0];
        document.getElementById("macro-1").textContent = `Protein: ${s.protein}`;
        document.getElementById("macro-2").textContent = `Carbs: ${s.carbs}`;
        document.getElementById("macro-3").textContent = `Fats: ${s.fats}`;
        document.getElementById("macro-4").textContent = `Fiber: ${s.fiber}`;
        document.getElementById("macro-5").textContent = `Calories: ${s.kcal}`;
    }

    /**
     * Registers a new product intake using PayloadManager.
     * @param {string|number} productId - Product identifier.
     * @param {number|string} grams - Consumed grams.
     */
    async submitIntake(productId, grams) {
        try {
            const payload = PayloadManager.payloadForger('register_intake', {
                id: productId,
                grams: parseFloat(grams)
            });

            const request = PayloadManager.requestForger(
                'POST',
                'register_intake',
                'bearer_auth',
                payload,
                localStorage.getItem('jwt_token')
            );

            await PayloadManager.requestSender(request, true);
        } catch (error) {
            console.error("[FoodCatalogManager] Failed to submit intake:", error);
        }
    }

    /**
     * Renders desktop table rows using a DocumentFragment to eliminate layout thrashing.
     * @param {Array<Array>} data - List of food records.
     */
    renderDesktop(data) {
        if (!this.desktopTbody) return;

        const fragment = document.createDocumentFragment();
        this.desktopTbody.innerHTML = "";

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
                const grams = this.gramsInput?.value === "" ? 100 : parseFloat(this.gramsInput?.value || 100);
                this.setSelected(tr, food, grams);
            });

            fragment.appendChild(tr);
        });

        this.desktopTbody.appendChild(fragment);
    }

    /**
     * Updates mobile specific row macro displays.
     * @param {HTMLElement} row - Mobile row container.
     * @param {Array} food - Food data array.
     * @param {number} grams - Grams quantity.
     */
    updateMobileMacros(row, food, grams) {
        const s = this.calcScaled(food, grams);

        row.querySelector(".macro-kcal").textContent = `Kcal: ${s.kcal}`;
        row.querySelector(".macro-protein").textContent = `P: ${s.protein}`;
        row.querySelector(".macro-carbs").textContent = `C: ${s.carbs}`;
        row.querySelector(".macro-fats").textContent = `F: ${s.fats}`;
        row.querySelector(".macro-fiber").textContent = `Fi: ${s.fiber}`;
    }

    /**
     * Creates a mobile row element with full interactive event bindings.
     * @param {Array} food - Food data array.
     * @returns {HTMLElement} Mobile row element.
     */
    createMobileRow(food) {
        const row = document.createElement("div");
        row.className = "food-row";

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
            if (this.activeMobileRow && this.activeMobileRow !== row) {
                this.activeMobileRow.classList.remove("active");
            }

            row.classList.toggle("active");
            this.activeMobileRow = row;
        };

        const applySelection = () => {
            const grams = parseFloat(input.value || 100);
            this.setSelected(row, food, grams);
            this.updateMobileMacros(row, food, grams);
        };

        header.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleOpen();
            applySelection();
        });

        button.addEventListener("click", (e) => {
            e.stopPropagation();
            const grams = parseFloat(input.value || 100);

            this.setSelected(row, food, grams);
            this.updateMobileMacros(row, food, grams);
            this.submitIntake(food[7], grams);
        });

        input.addEventListener("input", (e) => {
            e.stopPropagation();
            const grams = parseFloat(e.target.value || 100);
            this.activeGrams = grams;

            if (this.selectedFood && this.selectedRow === row) {
                this.updateMobileMacros(row, food, grams);
                this.updateUI(food, grams);
            }
        });

        return row;
    }

    /**
     * Renders mobile card views using a DocumentFragment for maximum performance.
     * @param {Array<Array>} data - List of food records.
     */
    renderMobile(data) {
        if (!this.mobileList) return;

        const fragment = document.createDocumentFragment();
        this.mobileList.innerHTML = "";

        data.forEach(food => {
            fragment.appendChild(this.createMobileRow(food));
        });

        this.mobileList.appendChild(fragment);
    }

    /**
     * Fetches products using PayloadManager and Bearer authentication with query parameters.
     * @param {string} query - Search string.
     * @param {number} page - Page number.
     */
    async searchProductsApi(query, page = 1) {
        if (query.length < 2) {
            this.renderDesktop(this.foodCatalog);
            this.renderMobile(this.foodCatalog);
            if (window.foodCatalogPagination) {
                this.updatePagination(window.foodCatalogPagination);
            }
            return;
        }

        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        const marketFilter = new URLSearchParams(window.location.search).get('market') || '<none>';
        console.log(`[FoodCatalogManager] Loading pagination ${page} with market ${marketFilter}.`);

        try {
            const request = PayloadManager.requestForger(
                'GET',
                'search_products',
                'bearer_auth',
                {},
                localStorage.getItem('bearer_token')
            );
            request.endpoint += `?q=${encodeURIComponent(query)}&page=${page}`;

            const result = await PayloadManager.requestSender(request, false, {
                signal: this.abortController.signal
            });

            if (!result || !result.data) return;

            const formattedResults = result.data.map(p => [
                p.name,
                p.brand,
                p.market,
                p.kcal,
                p.protein,
                p.carbs,
                p.fats,
                p.fiber,
                p.id,
                p.brand
            ]);

            this.renderDesktop(formattedResults);
            this.renderMobile(formattedResults);
            this.updatePagination(result.pagination);

            console.log("[FoodCatalogManager] Catalog loaded successfully.");
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error("[FoodCatalogManager] An error occurred while loading the catalog.", error);
            }
        }
    }

    /**
     * Renders pagination controls and binds click handlers.
     * @param {Object} pagination - Pagination metadata.
     */
    updatePagination(pagination) {
        const paginationContainer = document.querySelector('.pagination-controls');
        if (!paginationContainer || !pagination) return;

        const marketFilter = new URLSearchParams(window.location.search).get('market') || '';
        
        paginationContainer.innerHTML = `
            ${pagination.hasPrevious ? `<a href="#" class="btn-pagination" data-page="${pagination.currentPage - 1}" data-market="${marketFilter}">←</a>` : `<span class="btn-pagination disabled">←</span>`}
            <span class="page-info">Página ${pagination.currentPage} de ${pagination.totalPages} (${pagination.totalItems} productos)</span>
            ${pagination.hasNext ? `<a href="#" class="btn-pagination" data-page="${pagination.currentPage + 1}" data-market="${marketFilter}">→</a>` : `<span class="btn-pagination disabled">→</span>`}
        `;

        paginationContainer.querySelectorAll('.btn-pagination[data-page]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(btn.dataset.page, 10);
                const query = this.searchInput?.value.trim() || '';
                const market = btn.dataset.market || '<none>';

                if (query.length >= 2) {
                    this.searchProductsApi(query, page);
                } else {
                    console.log(`[FoodCatalogManager] Loading pagination ${page} with market ${market}.`);
                    window.location.href = `?pagination=${page}&market=${btn.data?.market || ''}`;
                }
            });
        });
    }

    /**
     * Binds general event listeners and initializes the component lifecycle.
     */
    init() {
        this.renderDesktop(this.foodCatalog);
        this.renderMobile(this.foodCatalog);

        if (window.foodCatalogPagination) {
            this.updatePagination(window.foodCatalogPagination);
        }

        this.searchInput?.addEventListener("input", (e) => {
            const query = e.target.value.trim();
            clearTimeout(this.debounceTimer);

            if (query.length < 2) {
                this.renderDesktop(this.foodCatalog);
                this.renderMobile(this.foodCatalog);
                if (window.foodCatalogPagination) {
                    this.updatePagination(window.foodCatalogPagination);
                }
                return;
            }

            this.debounceTimer = setTimeout(() => {
                this.searchProductsApi(query, 1);
            }, 300);
        });

        this.gramsInput?.addEventListener("input", () => {
            const grams = this.gramsInput.value === "" ? 100 : parseFloat(this.gramsInput.value);
            this.activeGrams = grams;

            if (this.selectedFood && this.selectedRow) {
                this.updateUI(this.selectedFood, grams);
            }
        });

        this.btnSubmitIntake?.addEventListener("click", (e) => {
            e.preventDefault();

            if (!this.selectedFood) {
                console.warn("[FoodCatalogManager] No food selected for intake submission.");
                return;
            }

            this.submitIntake(this.selectedFood[7], this.gramsInput.value);
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new FoodCatalogManager();
});
