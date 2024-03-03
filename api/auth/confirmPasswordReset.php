<?php

include_once '../../utils/api.php';

class ConfirmPasswordReset extends API {
    private $apiEndpoint;

    public function __construct() {
        $this->apiEndpoint = "https://identitytoolkit.googleapis.com/v1/accounts:resetPassword";
        $this->execute();
    }

    /**
     * Executes the API request for POST method to reset user password using a verification code.
     * Validates the verification code and new password from POST data.
     * Sends the validated data to the API endpoint and outputs the JSON-encoded response.
     *
     * @return void
     */
    private function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the request method is POST
            $verificationCode = filter_input(INPUT_POST, 'oobCode', FILTER_SANITIZE_SPECIAL_CHARS);
        
            if (filter_var($verificationCode, FILTER_SANITIZE_SPECIAL_CHARS) && isset($_POST['newPassword'])) {
                $data = array(
                    "oobCode" => $verificationCode,
                    "newPassword" => $_POST['newPassword']
                );
                $data = $this->post($this->apiEndpoint, $data);
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

$confirmPasswordReset = new ConfirmPasswordReset();

?>