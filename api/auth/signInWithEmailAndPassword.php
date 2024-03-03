<?php

include_once '../../utils/api.php';
include_once '../../utils/database.php';

class SignInWithEmailAndPassword {
    private $api;
    private $database;
    private $apiEndpoint;
    private $userID;

    /**
     * Initializes the API and Database objects, sets the API endpoint for user sign-in,
     * and executes the sign-in process.
     *
     * @throws Exception If there are errors during the initialization or execution process,
     *                   exceptions are thrown with relevant error messages.
     */
    public function __construct() {
        $this->api = new API();
        $this->database = new Database();
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword";
        $this->execute();
    }

    /**
     * Execute the user sign-up process.
     *
     * This method checks if the request method is POST, sanitizes the provided email,
     * and processes the sign-up request. It then returns the user data in the JSON format. 
     * If the request is invalid or the method is not POST, appropriate error responses
     * are returned using the returnError method.
     *
     * @throws Exception If there are errors during the sign-up process, exceptions are caught,
     *                   and relevant error responses are returned.
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
            if ($email && isset($_POST['password'])) {
                $data = array(
                    "clientType" => "CLIENT_TYPE_WEB",
                    "email" => $email,
                    "password" => $_POST['password'],
                    "returnSecureToken" => true
                );
            
                $data = $this->api->post($this->apiEndpoint, $data);
                $user = $this->accountLookup($data);
                $this->userID = $this->getUserID($user);
                $lastLogin = $this->getLastLogin();
                $this->updateStreak($lastLogin);
                $this->addAuthToken();
                $this->updateLastLogin();
                echo json_encode($data);
            } else {
                // Handle the case where the email is missing or fails sanitisation
                $this->api->returnError(400, "Invalid email provided.");
            }
        } else {
            // Handle non-POST requests with a 405 status code
            $this->api->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
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
     * Get the user ID from the Firebase user data.
     *
     * This method extracts the user ID from the Firebase user data array. It checks for the
     * presence of required keys and verifies if the email is verified. If successful, it returns
     * the user ID; otherwise, it returns an appropriate error response.
     *
     * @param array $user The Firebase user data array.
     *
     * @return string The user ID if successful.
     */
    private function getUserID($user) {
        if (!isset($user["users"]) || !isset($user["users"][0]) || !isset($user["users"][0]["emailVerified"])) {
            $this->api->returnError(500, "Account lookup failed: " . print_r($user));
            exit();
        }

        if (!$user["users"][0]["emailVerified"]) {
            $this->api->returnError(401, "EMAIL_NOT_VERIFIED");
        }
    
        return $user["users"][0]["localId"];
    }

    /**
     * Add an authentication token to the user by generating and inserting it into the 'tokens' table.
     *
     * This method checks if the user information is present and if the email is verified.
     * If verification fails, a 401 error response is triggered. If verification succeeds,
     * an authentication token is generated, and its expiration time is set (e.g., 24 hours from now).
     * The generated token is then inserted into the 'tokens' table associated with the user ID.
     * If the insertion is unsuccessful, appropriate error messages are displayed.
     *
     * @param array $user An associative array containing user information, typically from the account lookup.
     * @throws Exception If there are errors during the token generation, insertion, or if the user information is missing,
     *                   exceptions are caught, and relevant error responses are returned.
     */
    private function addAuthToken() {
        // Generate a token and set the expiration time (e.g., 24 hours from now)
        $token = $this->generateToken();
        $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Insert the token into the database
        $sql = "INSERT INTO tokens (token, expiration, user_id)
                VALUES (?, ?, ?)";

        $statement = $this->database->query($sql, "sss", $token, $expiration, $this->userID);
        $statement->close();
    }
    
    /**
     * Retrieve the last login timestamp for the user from the database.
     *
     * This method executes a SQL query to select the 'last_login' field from the 'users' table
     * for the user identified by the stored user ID. If the user is found, the method returns
     * the timestamp of the last login. If the user is not found, it returns an appropriate
     * error response. If there is an SQL query failure, it returns a 500 Internal Server Error.
     *
     * @return int|void The timestamp of the last login if successful; otherwise, it handles errors by sending an API response.
     */
    private function getLastLogin() {
        // Select last login time in database
        $sql = "SELECT last_login
                FROM users
                WHERE user_id = ?";

        $statement = $this->database->query($sql, "s", $this->userID);
        $result = $statement->get_result();
        $row_count = $result->num_rows;
        $statement->close();

        if ($row_count > 0) {
            return strtotime($result->fetch_assoc()['last_login']);
        } else {
            // User not found
            $this->database->returnError(500, "USER_NOT_FOUND");
        }
    }

    /**
     * Checks if the current date is the day after the given date.
     *
     * This method determines whether the provided date falls one day before the current date.
     * It calculates the difference in days between the provided date and today's date by extracting
     * the year-month-day components from both dates and then subtracting them then converting 
     * the difference to days.
     *
     * @param string $date The date to compare, in any format recognized by strtotime().
     * @return bool True if the given date is the day before the current date, false otherwise.
     */
    private function isDayAfter($date) {
        return ((strtotime("today") - strtotime(date('Y-m-d', $date))) /  (60 * 60 * 24)) == 1;
    }

    /**
     * Update the user's daily streak in the database based on their last login time.
     *
     * This method calculates whether the current login is on the next day after the previous login
     * or on the same day. If it's the same day, the daily streak remains unchanged. If it's the next day,
     * the daily streak is incremented by 1, else it is set to 1. The method then executes a SQL 
     * query to update the 'daily_streak' field in the 'users' table accordingly. If there is an
     * SQL query failure, it returns a 500 Internal Server Error.
     *
     * @param int $lastLogin The timestamp of the user's last login.
     *
     * @return void This function does not return a value; it handles errors by sending an API response.
     */
    private function updateStreak($lastLogin) {
        $isDayAfter = $this->isDayAfter($lastLogin);
        $isSameDay = strtotime("today") === strtotime(date('Y-m-d', $lastLogin));

        if ($isSameDay) {
            return;
        }

        // Update daily streak in database
        if ($isDayAfter) {
            $sql = "UPDATE users
                    SET daily_streak = daily_streak + 1
                    WHERE user_id = ?";
        } else {
            $sql = "UPDATE users
                    SET daily_streak = 1
                    WHERE user_id = ?";
        }

        $statement = $this->database->query($sql, "s", $this->userID);
        $statement->close();
    }

    /**
     * Update the last login timestamp for a user in the database.
     *
     * This method updates the 'last_login' field in the 'users' table for a specific user
     * identified by the provided user ID. The update sets the last login time to the current timestamp.
     * If the update is successful, it completes silently. If there is an error during the process,
     * it returns an appropriate error response.
     *
     * @param string $userID The user ID for whom to update the last login timestamp.
     *
     * @return void This function does not return a value; it handles errors using the 'returnError' method.
     */
    private function updateLastLogin() {
        // Update last login time in database
        $sql = "UPDATE users
                SET last_login = CURRENT_TIMESTAMP
                WHERE user_id = ?";

        $statement = $this->database->query($sql, "s", $this->userID);            
        $statement->close();
        $this->database->getConn()->close();
    }
    
    /**
    * Generates a random token and sets it as a cookie for authentication purposes.
    *
    * @param int $length The length of the token to be generated (default is 32).
    * @return string Returns the generated token.
    *
    * This function generates a random token of specified length using random bytes.
    * It then sets this token as a cookie named "flashi-authToken" with an expiration time of 1 day.
    */
    private function generateToken($length = 32) {
        $token = bin2hex(random_bytes($length));
        setcookie("flashi-authToken", $token, time() + 3600 * 24, "/"); // Cookie will expire in 1 day
        return $token;
    }
}

$signInWithEmailAndPassword = new SignInWithEmailAndPassword();

?>