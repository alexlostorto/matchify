class API {
    constructor() {
        this.logger = new Logger();
    }

    /**
     * Asynchronously sends form data to the specified URL using a POST request.
     *
     * Constructs a FormData object from the provided data dictionary and sends it to the specified URL
     * using the fetch API. Handles the response and returns a tuple containing a boolean indicating
     * success or failure and the response data or error message.
     *
     * @param {string} url - The URL to which the form data will be sent.
     * @param {Object} [data={}] - An optional data dictionary containing key-value pairs to be sent as form data.
     * @return {Promise<[boolean, any]>} A promise resolving to a tuple containing a boolean indicating
     *                                   success or failure and the response data or error message.
     * @example
     * const [success, responseData] = await send('https://api.example.com/data', { key1: 'value1', key2: 'value2' });
     * if (success) {
     *   console.log('Data sent successfully:', responseData);
     * } else {
     *   console.error('Error sending data:', responseData);
     * }
     */
    async post(url, data={}) {
        const formData = new FormData();
        for (const [key, value] of Object.entries(data)) {
            formData.append(key, value);
        }

        let response;

        try {
            response = await fetch(url, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            return [false, error];
        }

        try {
            response = await response.text();
            return [true, JSON.parse(response)];
        } catch (error) {
            return [false, response || error];
        }
    }

    /**
     * Performs an asynchronous GET request to the specified URL with the given parameters.
     *
     * @param {string} url - The URL to which the GET request should be sent.
     * @param {Object} [params={}] - An optional object containing query parameters for the request.
     * @return {Promise<[boolean, any]>} A promise resolving to a tuple containing a boolean indicating
     *                                   success or failure and the response data or error message.
     * @throws {Error} - Throws an error if the fetch operation fails or if the response is not valid JSON.
     *
     * @example
     * const [success, dataOrError] = await get('https://api.example.com/data', { param1: 'value1', param2: 'value2' });
     * if (success) {
     *     console.log('Data:', dataOrError);
     * } else {
     *     console.error('Error:', dataOrError);
     * }
     */
    async get(url, params={}) {
        const queryString = new URLSearchParams(params).toString();

        let response;

        try {
            response = await fetch(`${url}?${queryString}`, {
                method: 'GET'
            });
        } catch (error) {
            return [false, error];
        }

        try {
            response = await response.text();
            return [true, JSON.parse(response)];
        } catch (error) {
            return [false, response || error];
        }
    }
}

class Logger {
    constructor() {
        this.url = '/api/utils/logging.php';
    }

    /**
     * Sends an error log with the specified error code and message to the server.
     *
     * Constructs a URLSearchParams object containing the error code and message, and sends it
     * to the server using a POST request. The method can be used to log errors to the server for
     * monitoring and analysis purposes.
     *
     * @param {string} code - The error code to be logged.
     * @param {string} [message=''] - The error message to be logged (optional, default is an empty string).
     * @return {void} This method does not return a value.
     * @example
     * log('1A1A', 'Network error.');
     */
    log(code, message='') {
        const formData = new URLSearchParams();
        formData.append('errorCode', code);
        formData.append('errorMessage', message);
        fetch(this.url, {
            method: 'POST',
            body: formData
        });
    }
}