<?php

include_once '../../utils/api.php';
include_once '../../utils/database.php';

class CreateUserWithEmailAndPassword {
    private $api;
    private $database;
    private $apiEndpoint;
    private $username;
    private $email;
    private $password;

    /**
     * Initializes the API and Database objects, sets the API endpoint for user sign-up,
     * retrieves and sets the email and password from the POST data, and executes the sign-up process.
     *
     * @throws Exception If there are errors during the initialization or execution process,
     *                   exceptions are thrown with relevant error messages.
     */
    public function __construct() {
        $this->api = new API();
        $this->database = new Database();
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:signUp";
        $this->username = $this->getUsername();
        $this->email = $this->getEmail();
        $this->password = $this->getPassword();
        $this->execute();
    }

    /**
     * Retrieve and sanitize a username from the POST data.
     *
     * This method uses filter_input to obtain the username input from the POST array.
     * It then applies additional validation to check if the username consists only of alphanumeric characters,
     * underscore, and hyphen. If the username is valid, it is returned; otherwise, a 400 error is thrown
     * using the returnError method.
     *
     * @return string The sanitized username if valid.
     * @throws Exception If the username is invalid, a 400 error is thrown with the message "INVALID_USERNAME".
     */
    private function getUsername() {
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
     * Retrieve and sanitize an email address from the POST data.
     *
     * This method uses filter_input to obtain the email input from the POST array.
     * It then applies additional validation using filter_var with the FILTER_VALIDATE_EMAIL filter.
     * If the email is valid, it is returned; otherwise, a 400 error is thrown using the returnError method.
     *
     * @return string The sanitized email address if valid.
     * @throws Exception If the email is invalid, a 400 error is thrown with the message "INVALID_EMAIL".
     */
    private function getEmail() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        } else {
            $this->api->returnError(400, "INVALID_EMAIL");
        }
    }

    /**
     * Retrieve the password from the POST data.
     *
     * This method checks if the 'password' key is set in the POST array.
     * If the password is set, it is returned; otherwise, a 400 error is thrown
     * using the returnError method with the message "MISSING_PASSWORD".
     *
     * @return string The password from the POST data.
     * @throws Exception If the 'password' key is not set in the POST data, a 400 error is thrown with the message "MISSING_PASSWORD".
     */
    private function getPassword() {
        if (isset($_POST['password'])) {
            return $_POST['password'];
        } else {
            $this->api->returnError(400, "MISSING_PASSWORD");
        }
    }

    /**
     * Executes the API request for POST method to create a user with an email and password.
     *
     * Filters and sanitizes the email input from POST data.
     * Validates the presence of both email and password in POST data.
     * Constructs the authentication data array and sends it to the API endpoint.
     * Outputs the API response if inputs are valid; otherwise, returns an error message with the appropriate HTTP status code.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
            $data = array(
                "clientType" => "CLIENT_TYPE_WEB",
                "email" => $this->email,
                "password" => $this->password,
                "returnSecureToken" => true
            );
            $this->checkUsernameNotTaken();
            $response = $this->api->post($this->apiEndpoint, $data);
            $user = $this->accountLookup($response);
            $this->createUser($user);
            echo json_encode($user);
        } else {
            // Handle non-POST requests with a 405 status code
            $this->api->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
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
     * Look up user account information using the provided ID token.
     *
     * This method checks if the ID token is present in the provided data. If present,
     * it performs an HTTP GET request to the account lookup API endpoint with the ID token.
     * The response is then decoded from JSON, and the user account information is returned as an associative array.
     * If the ID token is missing, a 401 error response is triggered using the returnError method.
     *
     * @param array $data An associative array containing the response data, typically from user sign-up.
     * @return array An associative array containing user account information.
     * @throws Exception If there are errors during the account lookup process or if the ID token is missing,
     *                   exceptions are caught, and relevant error responses are returned.
     */
    private function accountLookup($data) {
        if (!isset($data["idToken"])) {
            echo json_encode($data);
            exit;
        }
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseUrl = $protocol . '://' . $host . str_replace(basename($scriptName), '', $scriptName);
        $response = $this->api->get($baseUrl . "../auth/accountLookup.php", array("idToken"=>$data["idToken"]));
        return json_decode($response, true);
    }

    /**
     * Create a new user by inserting their information into the 'users' table.
     *
     * This method prepares and executes an SQL query to insert a new user into the 'users' table,
     * using the user data provided. The $userID comes from the response of user creation
     * in Firebase's API. If the insertion is unsuccessful, appropriate error messages are displayed.
     *
     * @param array $user The user data received from the Identity Toolkit API response.
     * @throws Exception If there are errors during the database operation, exceptions are caught
     *                   and a 500 error is returned with relevant error messages.
     */
    private function createUser($user) {
        // SQL query to insert a new user
        $sql = "INSERT INTO users (user_id, username, email)
                VALUES (?, ?, ?)";

        $userID = $user["users"][0]["localId"];

        $statement = $this->database->query($sql, "sss", $userID, $this->username, $this->email);
        $statement->close();
        $this->database->getConn()->close();
    }
}

$createUserWithEmailAndPassword = new CreateUserWithEmailAndPassword();

?>