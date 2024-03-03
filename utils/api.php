<?php

class API {
    private $apiKey = "";
    private $authDomain = "=";
    private $projectId = "=";
    private $storageBucket = "";
    private $messagingSenderId = "";
    private $appId = "";
    private $measurementId = "";

    /**
     * Perform a POST request to the specified API endpoint with the provided data and API key.
     *
     * This method constructs a complete URL by appending an API key to the given API endpoint.
     * It then executes a POST request using cURL, sends JSON-encoded data in the request body,
     * and returns the JSON-decoded response. If any errors occur during the request or response
     * processing, appropriate error messages are returned.
     *
     * @param array $data An associative array containing data to be sent in the POST request body.
     * @param string $apiEndpoint The base API endpoint URL to which the request will be made.
     *
     * @return array|null The JSON-decoded response data from the API if successful; otherwise, null.
     * @throws Exception If a cURL error occurs or JSON decoding fails, a 500 error is thrown using the returnError() method.
     */
    public function post($apiEndpoint, $data=array()) {
        // Initialize cURL session
        $ch = curl_init($apiEndpoint . '?key=' . $this->apiKey);

        // Set cURL options for the POST request
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // JSON-encode the data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set request headers (optional)
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', // Specify the content type
        ));

        // Execute the POST request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            // cURL error
            $error = curl_error($ch);
            $this->returnError(500, "cURL error: ".  print_r($error, true));
        } else {
            $responseData = json_decode($response, true); // Decode the JSON response

            if ($responseData === null) {
                // JSON decoding failed
                $error = json_last_error_msg();
                $this->returnError(500, "JSON error: $error");
            } else {
                // Update auth tokens in cookies
                $this->updateCookies($responseData);
                return $responseData;
            }
        }

        // Close cURL session
        curl_close($ch);
    }

    /**
     * Perform a GET request to the specified API endpoint with query parameters and API key.
     *
     * This method constructs a complete URL by appending query string parameters and an API key
     * to the given API endpoint. It then executes a GET request using cURL and returns the response.
     * If an error occurs during the cURL request, an error message is returned.
     *
     * @param array $params An associative array containing query parameters for the request.
     * @param string $apiEndpoint The base API endpoint URL to which the request will be made.
     *
     * @return string|null The response from the API if successful; otherwise, null.
     * @throws Exception If a cURL error occurs, a 500 error is thrown using the returnError() method.
     */
    public function get($apiEndpoint, $params=array()) {
        // Convert params to query string parameters
        $queryString = http_build_query($params);
    
        // Construct the complete URL with query string and API key
        $url = $apiEndpoint . '?' . $queryString . '&key=' . $this->apiKey;
    
        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Set request headers (optional)
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', // Specify the content type
        ));
    
        // Execute the GET request
        $response = curl_exec($ch);
    
        // Check for cURL errors
        if (curl_errno($ch)) {
            // cURL error
            $error = curl_error($ch);
            $this->returnError(500, "cURL error: ".  print_r($error, true));
        } else {
            // Update auth tokens in cookies
            $this->updateCookies($response);
            return $response;
        }
    
        // Close cURL session
        curl_close($ch);
    }    

    /**
     * Sends an error response with the specified status code and error message.
     *
     * This function sets the HTTP response status code, constructs a JSON-encoded
     * error response message, and outputs the response. It then terminates the script
     * execution using the exit() function to prevent further processing.
     *
     * @param int    $statusCode   The HTTP status code to be set in the response.
     * @param string $errorMessage The error message to be included in the response.
     *
     * @return void This function does not return a value.
     */
    public function returnError($statusCode, $errorMessage) {
        http_response_code($statusCode); // Set status code
        $response = array(
            'error' => array(
                'message' => $errorMessage
            )
        );
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit();
    }

    /**
     * Sends a success response with the specified message.
     *
     * This function sets the HTTP response status code, constructs a JSON-encoded
     * success response message, and outputs the response. It then terminates the script
     * execution using the exit() function to prevent further processing.
     *
     * @param string $message The success message to be included in the response.
     *
     * @return void This function does not return a value.
     */
    public function returnSuccess($message) {
        http_response_code(200); // Set status code
        $response = array(
            'success' => array(
                'message' => $message
            )
        );
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        exit();
    }

    /**
     * Updates the cookies with the ID token and refresh token from the response.
     *
     * This private method checks if the "idToken" and "refreshToken" keys are present
     * in the provided response array. If both keys are present, it sets cookies
     * for the "matchify-idToken" and "matchify-refreshToken" with the corresponding values
     * from the response. The cookies are set to expire in 1 day (24 hours).
     *
     * @param array $response The response array containing the tokens.
     *
     * @return void This function does not return a value.
     */
    private function updateCookies($response) {
        if (isset($response["idToken"]) && isset($response["refreshToken"])) {
            setcookie("matchify-idToken", $response["idToken"], time() + 3600 * 24, "/"); // Cookie will expire in 1 day
            setcookie("matchify-refreshToken", $response["refreshToken"], time() + 3600 * 24, "/"); // Cookie will expire in 1 day
        }
    }

    /**
     * Retrieve the authentication token based on the current request method.
     *
     * This method checks the current HTTP request method (GET, POST, or other) and retrieves
     * the authentication token from the corresponding superglobal array ($_GET, $_POST, or $_COOKIE).
     * If a token is found, it is sanitized using htmlspecialchars() before being returned.
     * If no token is found, a 401 error is thrown using the returnError() method.
     *
     * @return string|null The sanitized authentication token if found; otherwise, null.
     * @throws Exception If no authentication token is found, a 401 error is thrown using the returnError() method.
     */
    public function getToken() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (isset($_GET['idToken'])) {
                    return htmlspecialchars($_GET['idToken']);
                }
        
            case 'POST':
                if (isset($_POST['idToken'])) {
                    return htmlspecialchars($_POST['idToken']);
                }
        
            default:
                if (isset($_COOKIE['matchify-idToken'])) {
                    return htmlspecialchars($_COOKIE['matchify-idToken']);
                }
        }
        
        // Handle the case where no token is found
        $this->returnError(401, "NOT_AUTHENTICATED");
    }
}

?>