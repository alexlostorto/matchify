<?php

include_once '../../utils/api.php';

class ConfirmEmailVerification extends API {
    private $apiEndpoint;

    public function __construct() {
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:update";
        $this->execute();
    }

    /**
     * Executes the API request for POST method to verify a user with a verification code.
     * Validates the verification code from POST data.
     * Sends the validated code to the API endpoint and outputs the JSON-encoded response.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
            $verificationCode = filter_input(INPUT_POST, 'oobCode', FILTER_SANITIZE_SPECIAL_CHARS);
        
            if (filter_var($verificationCode, FILTER_SANITIZE_SPECIAL_CHARS)) {
                $data = array(
                    "oobCode" => $verificationCode
                );
                $data = $this->post($this->apiEndpoint, $data);
                echo json_encode($data);
            } else {
                // Handle the case where the verification code is missing or fails sanitisation
                $this->returnError(401, "Invalid verification code provided.");
            }
        } else {
            // Handle non-POST requests with a 405 status code
            $this->returnError(405, "Method Not Allowed: This endpoint only accepts POST requests.");
        }
    }
}

$confirmEmailVerification = new ConfirmEmailVerification();

?>