<?php

include_once '../../utils/database.php';

class CheckUsernameNotTaken {
    private $database;
    private $username;

    public function __construct() {
        $this->database = new Database();
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
        // Get the raw JSON input
        $jsonInput = file_get_contents('php://input');
        $jsonData = json_decode($jsonInput, true);

        if (!isset($jsonData['username'])) {
            $this->database->returnError(400, "MISSING_USERNAME");
        }

        // Validate username
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $jsonData['username'])) {
            $this->database->returnError(400, "INVALID_USERNAME");
        }

        return $jsonData['username'];
    }

    /**
     * Execute the process for changing the username.
     *
     * If the incoming request method is POST, retrieve the new username and check its availability.
     * Proceed to change the username if available. If the request method is not POST, return a 405
     * Method Not Allowed status code, indicating that the endpoint only accepts POST requests.
     *
     * @throws Exception If errors occur during the username retrieval or availability check.
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST

            $this->username = $this->getUsername();
            $this->checkUsernameNotTaken();
        } else {
            // Handle non-POST requests with a 405 status code
            $this->database->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
    }

    /**
     * Check if the given username is available.
     *
     * This method executes an SQL query to check if a user with the provided username already exists in the 'users' table.
     * If the username is taken, it sends a JSON response with a 'USERNAME_TAKEN' message.
     * If the username is not taken, it sends a JSON response with a 'USERNAME_NOT_TAKEN' message.
     * The method sets a 200 Success status code in both cases.
     *
     * @throws Exception If there are errors during the database query or if an exception is caught,
     *                   relevant error responses are returned.
     */
    private function checkUsernameNotTaken() {
        // SQL query to get user
        $sql = "SELECT *
                FROM users
                WHERE username = ?";

        $statement = $this->database->query($sql, "s", $this->username);
        $result = $statement->get_result();
        $rowCount = $result->num_rows;
        $statement->close();
        $this->database->getConn()->close();

        if ($rowCount > 0) {
            $this->database->returnSuccess("USERNAME_TAKEN");
        } else {
            $this->database->returnSuccess("USERNAME_NOT_TAKEN");
        }
    }
}

$checkUsernameNotTaken = new CheckUsernameNotTaken();

?>