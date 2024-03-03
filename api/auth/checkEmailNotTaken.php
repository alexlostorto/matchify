<?php

include_once '../../utils/database.php';

class CheckEmailNotTaken extends Database {
    private $email;

    public function __construct() {
        parent::__construct();
        $this->execute();
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
            $this->returnError(400, "INVALID_EMAIL");
        }
    }

    /**
     * Execute the process of checking if the provided email already exists.
     *
     * This method checks if the request method is POST. If it is, it retrieves the sanitized email
     * using the getEmail method and then performs a check to determine if the email already exists
     * in the system by calling the checkEmailExists method. If the request is not a POST request,
     * a 405 error response is triggered, indicating that this endpoint only accepts POST requests.
     *
     * @throws Exception If there are errors during the email existence check or if the method is not POST,
     *                   exceptions are caught, and relevant error responses are returned.
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
            
            $this->email = $this->getEmail();
            $this->checkEmailNotTaken();
        } else {
            // Handle non-POST requests with a 405 status code
            $this->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
    }

    /**
     * Check if the provided email already exists in the 'users' table.
     *
     * This method executes an SQL query to retrieve user information based on the provided email.
     * If the query is successful, it checks the number of rows in the result set to determine
     * whether the email already exists. The response is then formatted as a JSON object with a success message.
     * The 'EMAIL_EXISTS' message is sent if the email is found, and 'EMAIL_NOT_EXISTS' is sent otherwise.
     * If there are any errors during the process, appropriate error responses are returned.
     *
     * @throws Exception If there are errors during the database query or if an exception is caught,
     *                   relevant error responses are returned.
     */
    private function checkEmailNotTaken() {
        // SQL query to get user
        $sql = "SELECT *
                FROM users
                WHERE email = ?";

        $statement = $this->query($sql, "s", $this->email);
        $result = $statement->get_result();
        $rowCount = $result->num_rows;
        $statement->close();
        $this->getConn()->close();

        if ($rowCount > 0) {
            $this->returnSuccess("EMAIL_TAKEN");
        } else {
            $this->returnSuccess("EMAIL_NOT_TAKEN");
        }
    }
}

$checkEmailNotTaken = new CheckEmailNotTaken();

?>