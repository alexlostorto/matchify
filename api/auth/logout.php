<?php

include_once '../../utils/database.php';

class Logout extends Database {
    private $userID;

    public function __construct() {
        parent::__construct();
        $this->execute();
    }

    /**
     * Executes the API request for GET method to retrieve user information.
     *
     * Validates the request method to ensure it's a GET request.
     * Retrieves the user ID using the `getUserID` method.
     * Removes user auth tokens based on the retrieved user ID using the `logout` method.
     * If the request method is not valid it returns a 405 status code error.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Check if the request method is GET
            
            $this->userID = $this->getUserID();    
            $this->logout();
        } else {
            // Handle non-GET requests with a 405 status code
            $this->returnError(405, "Method Not Allowed: This endpoint only accepts GET requests.");
        }
    }

    /**
     * Logout the user by deleting their auth tokens from the database.
     *
     * This method executes a SQL query to delete the user's auth tokens from the 'tokens' table
     * based on their user ID. If the deletion is successful, it returns a success response.
     * If there is an error during the process, it returns an appropriate error response.
     *
     * @return void This function does not return a value; it sends a response.
     */
    private function logout() {
        // SQL query to delete user's auth tokens
        $sql = "DELETE FROM tokens
                WHERE user_id = ?;";

        $statement = $this->query($sql, "s", $this->userID);
        $statement->close();
        $this->getConn()->close();
        $this->returnSuccess("LOGOUT_SUCCESS");
    }
}

$logout = new Logout();

?>