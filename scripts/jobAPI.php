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
    
    if (isset($request_data['title'])) {
        makeAPICall($request_data['title']);
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

function makeAPICall($title) {
    global $JOB_API_KEY;

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://linkedin-jobs-scraper-api.p.rapidapi.com/jobs",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'title' => $title,
            'location' => 'England',
            'rows' => 10
        ]),
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: linkedin-jobs-scraper-api.p.rapidapi.com",
            "X-RapidAPI-Key: " . $JOB_API_KEY,
            "content-type: application/json"
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo json_encode($response);
    }
}

?>