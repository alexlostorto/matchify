<?php

include_once '../../utils/api.php';
include_once '../../utils/database.php';

class ChangeUsername {
    private $api;
    private $database;
    private $userID;
    private $username;

    public function __construct() {
        $this->api = new API();
        $this->database = new Database();
        $this->userID = $this->database->getUserID();
        $this->username = $this->getUsername();
        $this->execute();
    }

    /**
     * Executes the API request for POST method to update user display name.
     * Validates token from cookies and display name from POST data.
     * Sends validated data to the API endpoint and outputs the JSON-encoded response.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
    
            if ($this->userID && $this->username) {
                $this->checkUsernameNotTaken();
                $this->changeUsername();
            } else {
                // Handle the case where inputs are invalid
                $this->api->returnError(400, "Invalid inputs provided.");
            }
        } else {
            // Handle non-POST requests with a 405 status code
            $this->api->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
    }

    /**
     * Retrieve and sanitize a username from the POST data.
     *
     * This method checks if the username consists only of alphanumeric characters,
     * underscores, and hyphens. If the username is valid, it is returned; otherwise, a 400 error is thrown
     * using the returnError method.
     * 
     * Note: This method showcases polymorphism by overriding the getUsername method from the inherited Database class,
     * providing a specialized implementation for username retrieval in this context.
     *
     * @return string The sanitized username if valid.
     * @throws Exception If the username is invalid, a 400 error is thrown with the message "INVALID_USERNAME".
     */
    public function getUsername() {
        if (!isset($_POST['username'])) {
            $this->api->returnError(400, "MISSING_USERNAME");
        }

        // Validate username
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $_POST['username'])) {
            $this->api->returnError(400, "INVALID_USERNAME");
        }

        return $_POST['username'];
    }

    /**
     * Check if the given username is available by making an API request to a separate endpoint.
     *
     * This method constructs the base URL using the current server environment and the script name.
     * It then makes a POST request to a separate endpoint (checkUsernameNotTaken.php) with the provided username.
     * If the username is taken, it returns a 400 error with the message "USERNAME_TAKEN."
     * If the username is not taken, it returns without any action.
     *
     * @throws Exception If there are errors during the API request or if an unexpected response is received,
     *                   relevant error responses are returned.
     */
    private function checkUsernameNotTaken() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseUrl = $protocol . '://' . $host . str_replace(basename($scriptName), '', $scriptName);
        $response = $this->api->post($baseUrl . "checkUsernameNotTaken.php", array("username" => $this->username));

        if (isset($response['success']) && isset($response['success']['message'])) {
            if ($response['success']['message'] === "USERNAME_TAKEN") {
                $this->api->returnError(400, "USERNAME_TAKEN");
            } elseif ($response['success']['message'] === "USERNAME_NOT_TAKEN") {
                return;
            }
        } else {
            $this->api->returnError(400, print_r($response, true));
        }
    }

    /**
     * Change the username for the current user.
     *
     * This method updates the 'username' field in the 'users' table for the user identified by the 'user_id'.
     * It executes an SQL query to perform the update and returns a JSON response indicating the success or failure
     * of the operation. If successful, a 200 Success status code is set, and a success message is sent in the response.
     * If there are any errors during the update process, appropriate error responses are returned.
     *
     * @throws Exception If there are errors during the database update or if an exception is caught,
     *                   relevant error responses are returned.
     */
    private function changeUsername() {
        // SQL query to update username field in users
        $sql = "UPDATE users
                SET username = ?
                WHERE user_id = ?";

        $statement = $this->database->query($sql, "ss", $this->username, $this->userID);
        $statement->close();
        $this->database->getConn()->close();
        $this->api->returnSuccess("USERNAME_CHANGED");
    }
}

$changeUsername = new ChangeUsername();

?>