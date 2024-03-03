<?php

include_once '../../utils/api.php';
include_once '../../utils/database.php';

class ChangeEmail {
    private $api;
    private $database;
    private $apiEndpoint;
    private $userID;
    private $email;

    public function __construct() {
        $this->api = new API();
        $this->database = new Database();
        $this->userID = $this->database->getUserID();
        $this->email = $this->getEmail();
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:update";
        $this->execute();
    }

    /**
     * Executes the API request for POST method.
     * Validates token from cookies and email from POST data.
     * Sends validated data to the API endpoint and outputs the JSON-encoded response.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
            
            $this->checkEmailNotTaken();
            $token = $this->api->getToken();
    
            if ($token) {
                $data = array(
                    "idToken" => $token,
                    "email" => $this->email,
                    "returnSecureToken" => true
                );
                $data = $this->api->post($this->apiEndpoint, $data);
                if (isset($data["email"])) {
                    $this->changeEmail();
                }
                echo json_encode($data);
            } else {
                // Inputs are not valid
                $this->api->returnError(400, "Invalid inputs provided.");
            }
        } else {
            // Handle non-POST requests with a 405 status code
            $this->api->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
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
     * Check if the given email is available by making an API request to a separate endpoint.
     *
     * This method constructs the base URL using the current server environment and the script name.
     * It then makes a POST request to a separate endpoint (checkEmailNotTaken.php) with the provided email.
     * If the email is taken, it returns a 400 error with the message "EMAIL_TAKEN."
     * If the email is not taken, it returns without any action.
     *
     * @throws Exception If there are errors during the API request or if an unexpected response is received,
     *                   relevant error responses are returned.
     */
    private function checkEmailNotTaken() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseUrl = $protocol . '://' . $host . str_replace(basename($scriptName), '', $scriptName);
        $response = $this->api->post($baseUrl . "checkEmailNotTaken.php", array("email" => $this->email));

        if (isset($response['success']) && isset($response['success']['message'])) {
            if ($response['success']['message'] === "EMAIL_TAKEN") {
                $this->api->returnError(400, "EMAIL_TAKEN");
            } elseif ($response['success']['message'] === "EMAIL_NOT_TAKEN") {
                return;
            }
        } else {
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Change the email for the current user.
     *
     * This method updates the 'email' field in the 'users' table for the user identified by the 'user_id'.
     * It executes an SQL query to perform the update and returns a JSON response indicating the success or failure
     * of the operation. If successful, a 200 Success status code is set, and a success message is sent in the response.
     * If there are any errors during the update process, appropriate error responses are returned.
     *
     * @throws Exception If there are errors during the database update or if an exception is caught,
     *                   relevant error responses are returned.
     */
    private function changeEmail() {
            // SQL query to update email field in users
            $sql = "UPDATE users
                    SET email = ?
                    WHERE user_id = ?";

            $statement = $this->database->query($sql, "ss", $this->email, $this->userID);
            $statement->close();
            $this->database->getConn()->close();
            $this->api->returnSuccess("EMAIL_CHANGED");
    }
}

$changeEmail = new ChangeEmail();

?>