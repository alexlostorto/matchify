<?php

// Include API key
$path = $_SERVER['DOCUMENT_ROOT'];
$path .= "/secrets.php";
include_once($path); 

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the URL of the ConvertAPI endpoint
$url = 'https://v2.convertapi.com/convert/doc/to/txt?Secret=' . $CONVERT_API_KEY;

// Check if the file exists
function getFileContents($filePath) {
    if (file_exists($filePath)) {
        // Read the file content
        $fileContent = file_get_contents($filePath);
    
        // Encode the file content as Base64
        $base64File = base64_encode($fileContent);
    
        // Output the Base64-encoded file content
        return $base64File;
    } else {
        echo "File not found at $filePath";
        exit();
    }
}

function returnTextContent($response) {
    $json = json_decode($response, true);
    echo base64_decode($json['Files'][0]['FileData']);
}

function makeAPICall($fileContents, $url) {
    // Define the JSON request body
    $requestBody = json_encode([
        "Parameters" => [
            [
                "Name" => "File",
                "FileValue" => [
                    "Name" => "resume.doc",
                    "Data" => $fileContents
                ]
            ],
            [
                "Name" => "StoreFile",
                "Value" => false
            ]
        ]
    ]);
    
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($requestBody)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Execute the cURL request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        returnTextContent($response);
    }
    
    // Close the cURL session
    curl_close($ch);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw JSON data from the request body
    $json_data = file_get_contents('php://input');

    // Check if JSON data was successfully retrieved
    if ($json_data === false) {
        // Handle error
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Failed to read JSON data from request']);
        exit;
    }

    // Parse the JSON data into a PHP array
    $request_data = json_decode($json_data, true);

    // Check if JSON data was successfully parsed
    if ($request_data === null) {
        // Handle error (invalid JSON data)
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }
    
    if (isset($request_data['fileData'])) {
        makeAPICall($request_data['fileData'], $url);
    } else {
        // Handle missing 'increment' key
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing "fileData" key']);
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle non-GET/POST/OPTIONS requests
    http_response_code(204);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

?>