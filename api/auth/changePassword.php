<?php

include_once '../../utils/api.php';

class ChangePassword extends API {
    private $apiEndpoint;

    public function __construct() {
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:update";
        $this->execute();
    }

    /**
     * Executes the API request for POST method to update user password.
     * Validates token from cookies and new password from POST data.
     * Sends validated data to the API endpoint and outputs the JSON-encoded response.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
        
            $token = $this->getToken();
    
            if ($token && isset($_POST['newPassword'])) {
                $data = array(
                    "idToken" => $token,
                    "password" => $_POST['newPassword'],
                    "returnSecureToken" => true
                );
                $data = $this->post($this->apiEndpoint,$data);
                echo json_encode($data);
            } else {
                // Inputs are not valid
                $this->returnError(400, "Invalid inputs provided.");
            }
        } else {
            // Handle non-POST requests with a 405 status code
            $this->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
    }
}

$changePassword = new ChangePassword();

?>