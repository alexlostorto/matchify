<?php

// Include API key
include('../secrets.php');

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
    
    if (isset($request_data['cvData']) && isset($request_data['jobDescription'])) {
        makeAPICall($request_data['cvData'], $request_data['jobDescription']);
    } else {
        // Handle missing 'increment' key
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing "title" key']);
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle non-GET/POST/OPTIONS requests
    http_response_code(204);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function makeAPICall($cvData, $listingData) {
    global $OPENAI_KEY;

    $api_url = 'https://api.openai.com/v1/engines/davinci/completions';
    
    $headers = [
        'Authorization: Bearer ' . $OPENAI_KEY,
        'Content-Type: application/json',
    ];
    
    $data = [
        'prompt' => 'Pay attention to making the current skills relevant to the job role. Using the following CV: '. $cvData . 'Tailor it to the following Job Listing:' . $listingData . 'Generate ONLY THE CV IN TXT.',
        'max_tokens' => 50,
    ];
    
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        echo 'Error: ' . curl_error($ch);
    } else {
        echo 'API Response: ' . $response;
    }
    
    curl_close($ch);
}

?>