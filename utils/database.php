<?php

class Database {
    private $server;
    private $username = "";
    private $password = "";
    private $database = "";
    private $conn;

    /**
     * Constructor for the Database class.
     *
     * If the current host is "localhost", the server is set to "localhost"; otherwise, it's set 
     * to "sdb-61.hosting.stackcp.net". After determining the server, the constructor establishes 
     * a connection to the database using the connect() method.
     */
    public function __construct() {
        if ($this->getHost() == "localhost") {
            $this->server = "localhost";
        } else {
            $this->server = "sdb-61.hosting.stackcp.net";
        }
        $this->connect();
    }

    public function getServer() {
        return $this->server;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getConn() {
        return $this->conn;
    }

    /**
     * Retrieves the host from the current server environment.
     *
     * Checks if the 'HTTP_HOST' server variable is set, which typically represents 
     * the host name from the current request. If set, the host name is returned.
     *
     * @return string|null Returns the host name if 'HTTP_HOST' is set. 
     *                     Otherwise, returns null.
     */
    public function getHost() {
        if (isset($_SERVER["HTTP_HOST"])) {
            return $_SERVER["HTTP_HOST"];
        }
    }

    /**
     * Establishes a connection to the database using the provided credentials.
     *
     * Attempts to connect to the database server using the stored server, username,
     * password, and database name. If the connection is successful, the connection 
     * object is stored in the class property `$this->conn` and returned.
     *
     * If the connection fails, it triggers an error with a 500 status code and 
     * the specific error message from the mysqli extension.
     *
     * @return mysqli|void Returns the MySQLi connection object on success.
     *                     If the connection fails, the method calls the `returnError()` 
     *                     method with a 500 status and the error message.
     */
    public function connect() {
        $this->conn = mysqli_connect($this->server, $this->username, $this->password, $this->database);
        if ($this->conn) {
            return $this->conn;
        } else {
            $this->returnError(500, mysqli_connect_error());
        }
    }

    /**
     * Executes a SQL query with optional parameterized values and returns the prepared statement.
     *
     * This method prepares and executes an SQL query with optional parameterized values.
     * It takes the SQL query string, parameter types, and a variable-length list of parameters as input.
     * The method dynamically binds the parameters to the prepared statement and executes the query.
     * If the query execution is successful, it returns the prepared statement.
     * If there are any errors during query preparation or execution, it triggers an appropriate error response.
     *
     * @param string $sql The SQL query string to be executed.
     * @param string $types (Optional) A string representing the types of parameters (e.g., 'ssi' for string, string, integer).
     * @param mixed $params (Optional) Variable-length list of parameters to be bound to the query.
     * @return mysqli_stmt|null Returns the prepared statement if the query execution is successful.
     *                          Returns null in case of errors, and triggers an error response.
     */
    public function query($sql, $types='', ...$params) {
        try {
            $statement = $this->getConn()->prepare($sql);

            if (!$statement) {
                $this->returnError(500, "Error preparing query: " . $sql . " " . $this->getConn()->error);
            }

            if (count($params) > 0) {
                $statement->bind_param($types, ...$params);   
            }

            if ($statement->execute()) {
                return $statement;
            } else {
                $statement->close();
                $this->returnError(500, "Error executing query: " . $sql . " " . $this->getConn()->error);
            }
        } catch (mysqli_sql_exception $e) {
            $this->returnError(500, "Caught exception: " . $e->getMessage());
        } catch (Exception $e) {
            $this->returnError(500, "Caught general exception: " . $e->getMessage());
        }
    }

    /**
     * Fetches the user ID associated with the authenticated token from cookies.
     *
     * This method checks for the presence of a specific cookie ('matchify-authToken').
     * If the token exists and is valid (not expired), it fetches the corresponding user ID
     * from the database using a prepared SQL statement.
     *
     * @return string Returns the user ID if the token is valid and associated with a user.
     *                Otherwise, it throws an error message indicating the reason for failure.
     */
    public function getUserID() {
        $token = $this->getToken();

        if ($token) {
            $sql = "SELECT user_id FROM tokens WHERE token = ? AND expiration >= NOW()";
            $statement = $this->query($sql, 's', $token);
            $result = $statement->get_result();
            $row_count = $result->num_rows;

            if ($row_count > 0) {
                return $result->fetch_assoc()['user_id'];
            } else {
                // User is not authenticated
                $this->returnError(401, "TOKEN_EXPIRED");
            }
        } else {
            // Token is not valid
            $this->returnError(401, "TOKEN_INVALID");
        }
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
                if (isset($_GET['authToken'])) {
                    return htmlspecialchars($_GET['authToken']);
                }
        
            case 'POST':
                if (isset($_POST['authToken'])) {
                    return htmlspecialchars($_POST['authToken']);
                }
        
            default:
                if (isset($_COOKIE['matchify-authToken'])) {
                    return htmlspecialchars($_COOKIE['matchify-authToken']);
                }
        }
        
        // Handle the case where no token is found
        $this->returnError(401, "NOT_AUTHENTICATED");
    }
}

?>