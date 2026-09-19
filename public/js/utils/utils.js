export function capitalize(word) {
    return String(word).charAt(0).toUpperCase() + String(word).slice(1);
}

/**
 * Sanitizes and normalizes numerical inputs.
 * 
 * @param {number|string} value - The raw input value.
 * @param {Object} options - Configuration rules.
 * @param {boolean} [options.allowNegative=false] - Whether negative values are allowed.
 * @param {number|null} [options.min=null] - Minimum allowed boundary.
 * @param {number|null} [options.max=null] - Maximum allowed boundary.
 * @param {boolean} [options.decimals=true] - Allow decimals or force integers.
 * @param {number} [options.fallback=0] - Default value if parsing fails.
 * @returns {number} The sanitized number.
 */
export function sanitizeNumber(value, options = {}) {
    const {
        allowNegative = false,
        min = null,
        max = null,
        decimals = true,
        fallback = 0
    } = options;

    if (value === null || value === undefined || value === '') {
        return fallback;
    }

    let stringVal = String(value).replace(',', '.');

    if (!allowNegative) {
        stringVal = stringVal.replace(/-/g, '');
    }

    let num = parseFloat(stringVal);
    if (isNaN(num)) return fallback;

    if (!decimals) {
        num = Math.trunc(num);
    }

    if (min !== null && num < min) num = min;
    if (max !== null && num > max) num = max;

    return num;
}

/**
 * Heavy-duty string sanitizer for removing accents, special characters, and punctuation.
 * 
 * @param {string} value - The raw string input.
 * @param {Object} options - Configuration rules.
 * @param {boolean} [options.removeAccents=true] - Strip accents (á, é -> a, e).
 * @param {boolean} [options.removePunctuation=true] - Strip punctuation symbols.
 * @param {boolean} [options.removeHyphens=true] - Strip dashes and hyphens.
 * @param {boolean} [options.allowSpaces=true] - Allow standard whitespace.
 * @param {boolean} [options.uppercase=false] - Force uppercase output.
 * @param {boolean} [options.lowercase=false] - Force lowercase output.
 * @returns {string} The sanitized string.
 */
export function sanitizeString(value, options = {}) {
    const {
        removeAccents = true,
        removePunctuation = true,
        removeHyphens = true,
        allowSpaces = true,
        uppercase = false,
        lowercase = false
    } = options;

    if (value === null || value === undefined) return '';

    let str = String(value);

    if (removeAccents) {
        // NFD decomposes characters into base letter + accent mark, then we strip the marks
        str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    if (removePunctuation) {
        let pattern = '[^a-zA-Z0-9';
        if (allowSpaces) pattern += '\\s';
        if (!removeHyphens) pattern += '\\-';
        pattern += ']';
        
        str = str.replace(new RegExp(pattern, 'g'), '');
    } else if (removeHyphens) {
        str = str.replace(/-/g, '');
    }

    str = str.replace(/\s+/g, ' ').trim();

    if (uppercase) str = str.toUpperCase();
    if (lowercase) str = str.toLowerCase();

    return str;
}
