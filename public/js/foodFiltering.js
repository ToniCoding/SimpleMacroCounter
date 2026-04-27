/**
 * This JS file is in charge of dynamically changing the product list based on the input on the "Search"
 * text box in AddFoodTemplate with event listeners.
 */
const currentScript = '[FoodFiltering.js]';
console.info(`${currentScript} Loading food filtering script.`);

function filterFood(searchTerm) {
    let foodCatalog = JSON.parse(document.getElementById('foodCatalog').dataset.config);
    console.info(`${currentScript} Catalog is: ${foodCatalog}`);

    if (!searchTerm || searchTerm.length < 2) {
        return foodCatalog;
    }
    
    const results = [];

    for (let i = 0; i < foodCatalog.length; i++) {
        if (foodCatalog[i][0].toLowerCase().includes(searchTerm.toLowerCase())) {
            results.push(foodCatalog[i]);
        }
    }
    
    console.info(`${currentScript} Found results for: ${results}`);
    return results;
}

const searchInput = document.getElementById('search-bar-food');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const resultados = filterFood(e.target.value);
        console.log('Resultados:', resultados);
    });
}
