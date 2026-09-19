import { PayloadManager } from '../utils/payloadManager.js';
import { sanitizeNumber, sanitizeString } from '../utils/utils.js';

async function registerNewProduct() {
    const productName = sanitizeString(document.getElementById('product_name').value);
    const productBrand = sanitizeString(document.getElementById('product_brand').value);
    const productMarket = sanitizeString(document.getElementById('product_market').value);

    const protein = sanitizeNumber(document.getElementById('product_protein').value, { allowNegative: false, min: 0, decimals: true });
    const carbs = sanitizeNumber(document.getElementById('product_carbs').value, { allowNegative: false, min: 0, decimals: true });
    const fats = sanitizeNumber(document.getElementById('product_fats').value, { allowNegative: false, min: 0, decimals: true });
    const fiber = sanitizeNumber(document.getElementById('product_fiber').value, { allowNegative: false, min: 0, decimals: true });

    const body = {
        productName: productName,
        brand: productBrand,
        market: productMarket,
        protein: protein * 1,
        carbs: carbs * 1,
        fats: fats * 1,
        fiber: fiber * 1,
    };

    console.info("[RegisterNewProduct] Sanitizing and forging:", body);

    try {
        const sanitizedBody = PayloadManager.payloadForger('register_product', body);
        const token = localStorage.getItem('jwt_token') || null; 
        const forgedRequest = PayloadManager.requestForger('post', 'register_product', 'bearer_auth', sanitizedBody, token);

        const data = await PayloadManager.requestSender(forgedRequest, true);

        console.debug("[RegisterNewProduct] Success:", data);

        return data;
    } catch (error) {
        console.error("[RegisterNewProduct] Error:", error);
        return null;
    }
}

const registerProductForm = document.getElementById('registerProductForm');

registerProductForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    registerNewProduct();
});
