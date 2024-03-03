<?php

include_once '../../utils/api.php';

class AccountLookup extends API {
    private $apiEndpoint;

    public function __construct() {
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:lookup";
        $this->execute();
    }

    /**
     * Executes the API request for GET method.
     * Verifies token from cookies and sends to the API endpoint.
     * Outputs the JSON-encoded API response.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Check if the request method is GET
        
            $token = $this->getToken();
    
            if ($token) {
                $data = array(
                    "idToken" => $token
                );
                $data = $this->post($this->apiEndpoint, $data);
                echo json_encode($data);
            } else {
                // Token is not valid
                $this->returnError(401, "Invalid token provided.");
            }
        } else {
            // Handle non-GET requests with a 405 status code
            $this->returnError(405, "Method Not Allowed: This endpoint only accepts GET requests.");
        }
    }
}

$accountLookup = new AccountLookup();

?>
