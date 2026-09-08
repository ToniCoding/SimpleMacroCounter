/**
 * @file payloadManager.js
 * @description Serves as a request generator and sender that creates, sends, and processes 
 * HTTP requests and responses from the client. This script allows the front-end to 
 * dynamically interact with the SMC API.
 */

export class PayloadManager {
    static #baseUrl = '/api/v1';

    static #availableMethods = ['get', 'post', 'put', 'delete', 'patch', 'options'];

    static #availableHeaders = {
        'json_default': {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        'json_content': {
            'Content-Type': 'application/json'
        },
        'json_accept': {
            'Accept': 'application/json'
        },
        'form_urlencoded': {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        'bearer_auth': (token) => ({
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        })
    };

    static #availableEndpoints = {
        'register_product': `${this.#baseUrl}/register-food`,
        'modify_macros': `${this.#baseUrl}/modify-macros`,
        'settings': `${this.#baseUrl}/settings`
    }

    static #availablePayloads = {
        'register_new_product': {
            product_name: null,
            product_brand: null,
            product_market: null,
            product_protein: null,
            product_carbs: null,
            product_fats: null,
            product_fiber: null
        },

        'register_new_intake': {
            product_id: null,
            product_consumed_grams: null
        },

        'modify_macros': {
            protein: null,
            carbs: null,
            fats: null,
            fiber: null,
            intent: null
        }
    }

    /**
     * Checks if the provided value is a valid non-null object and not an array.
     * 
     * @param {any} object - The value to evaluate.
     * @returns {boolean} True if the value is a plain object, false otherwise.
     */
    static isObject(object) {
        return (typeof object === 'object' && object != null && !Array.isArray(object));
    }

    /**
     * Verifies if a given string can be successfully parsed as a JSON structure.
     * 
     * @param {string} str - The string to test.
     * @returns {boolean} True if the string is valid JSON, false otherwise.
     */
    static isValidJson(str) {
        if (typeof str !== 'string') return false;

        try {
            JSON.parse(str);
            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Validates that incoming data matches an expected payload schema by checking 
     * type correctness and mandatory property presence.
     * 
     * @param {Object} incomingData - The raw payload object provided by the client.
     * @param {Object} selectedPayload - The reference template schema.
     * @returns {boolean} True if the payload passes validation.
     * @throws {Error} If the incoming data is not a valid object or schema is invalid.
     */
    static validateIncomingPayload(incomingData, selectedPayload) {
        if (!this.isObject(incomingData)) {
            throw Error(`[PayloadManager] The incoming data is not an object.`);
        }

        if (!selectedPayload) {
            throw new Error('[PayloadManager] Selected an invalid payload.');
        }

        const expectedObjectKeys = Object.keys(selectedPayload);
        const missingObjectKeys = expectedObjectKeys.filter(key => !(key in incomingData));

        if (missingObjectKeys.length > 0) {
            console.error(`[PayloadManager] The following keys are missing: ${missingObjectKeys.join(', ')}`);
            return false;
        }

        return true;
    }

    /**
     * Sanitizes and maps incoming data against a reference template, stripping 
     * out any unauthorized or unexpected properties.
     * 
     * @param {Object} payloadTemplate - The schema template defining allowed properties.
     * @param {Object} incomingData - The source data to filter.
     * @returns {Object} A clean object containing only authorized properties.
     */
    static sanitizePayload(payloadTemplate, incomingData) {
        return Object.keys(payloadTemplate).reduce((cleanPayload, key) => {
            if (Object.hasOwn(incomingData, key)) {
                cleanPayload[key] = incomingData[key];
            }
            return cleanPayload;
        }, {});
    }

    /**
     * Selects, validates, and sanitizes a predefined payload configuration based on 
     * the requested action type.
     * 
     * @param {string} payloadToForge - The identifier of the target payload template.
     * @param {Object} payloadInformation - The data inputs to populate and filter the payload.
     * @returns {Object} The finalized, clean payload object ready for transmission.
     * @throws {Error} If the target payload identifier does not exist.
     */
    static payloadForger(payloadToForge, payloadInformation) {
        let selectedPayload = null;

        switch (payloadToForge) {
            case 'register_product':
                selectedPayload = this.#availablePayloads.register_new_product;
                break;

            case 'register_new_intake':
                selectedPayload = this.#availablePayloads.register_new_intake;
                break;

            case 'modify_macros':
                selectedPayload = this.#availablePayloads.modify_macros;
                break;

            default:
                throw new Error(`[PayloadManager] The payload ${payloadToForge} doesn't exist.`);
        }

        this.validateIncomingPayload(payloadInformation, selectedPayload);
        return this.sanitizePayload(selectedPayload, payloadInformation);
    }

    /**
     * Assembles and validates a complete HTTP request configuration object containing 
     * the method, destination endpoint, headers, and payload body.
     * 
     * @param {string} method - The HTTP verb (e.g., 'get', 'post').
     * @param {string} endpoint - The key referencing the target URL endpoint.
     * @param {string} headers - The key referencing the header configuration.
     * @param {Object} body - The structured payload body.
     * @returns {Object} An object representing the fully validated request parameters.
     * @throws {Error} If any parameter (method, endpoint, headers, or body) is invalid.
     */
    static requestForger(method, endpoint, headers, body, token = null) {
        const selectedMethod = method.toUpperCase();
        const selectedEndpoint = this.#availableEndpoints[endpoint];
        let selectedHeaders = this.#availableHeaders[headers];

        if (typeof selectedHeaders === 'function') {
            if (!token) throw new Error(`[PayloadManager] The header ${headers} requires a token.`);
            selectedHeaders = selectedHeaders(token);
        }

        if (!this.#availableMethods.includes(selectedMethod.toLowerCase())) {
            throw new Error(`[PayloadManager] The method ${selectedMethod} doesn't exist.`);
        }
        if (!selectedEndpoint) {
            throw new Error(`[PayloadManager] The endpoint ${endpoint} doesn't exist.`);
        }
        if (!selectedHeaders) {
            throw new Error(`[PayloadManager] The header ${headers} doesn't exist.`);
        }
        if (!this.isObject(body)) {
            throw new Error('[PayloadManager] The payload body must be an object.');
        }

        return {
            method: selectedMethod,
            endpoint: selectedEndpoint,
            headers: selectedHeaders,
            body: body
        };
    }

    /**
     * Sends the fully validated HTTP request using the Fetch API with promise chaining,
     * ensuring comprehensive traceability and error handling based on response status.
     * 
     * @param {Object} request - The structured request configuration object.
     * @returns {Promise<any>} A promise resolving to the parsed response body.
     */
    static requestSender(request) {
        console.info(`[PayloadManager] Sending ${request.method} request to ${request.endpoint}.`);

        const fetchOptions = {
            method: request.method,
            headers: request.headers,
        };

        if (request.method !== 'GET' && request.method !== 'HEAD' && request.body) {
            fetchOptions.body = JSON.stringify(request.body);
        }

        return fetch(request.endpoint, fetchOptions)
            .then(response => {
                console.info('[PayloadManager] Sent.');
                console.log('[PayloadManager] Response received from the selected endpoint.');

                if (response.redirected || (response.status >= 300 && response.status < 400)) {
                    console.warn('[PayloadManager] The response contains a redirection.');
                }

                if (!response.ok) {
                    return response.text().then(errorBody => {
                        let errorMessage = `[PayloadManager] HTTP error status: ${response.status}`;
                        
                        if (errorBody) {
                            errorMessage += `\n${errorBody}`;
                        }

                        throw new Error(errorMessage);
                    });
                }

                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }

                return response.text();
            })
            .catch(error => {
                console.error(`[PayloadManager] Request failed:\n${error.message}`);
                
                throw error;
            });
    }
}
