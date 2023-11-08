<?php
// Specify the file path
$filePath = '../assets/docs/resume.docx'; // Replace with the path to your file

// Check if the file exists
if (file_exists($filePath)) {
    // Read the file content
    $fileContent = file_get_contents($filePath);

    // Encode the file content as Base64
    $base64File = base64_encode($fileContent);

    // Output the Base64-encoded file content
    echo $base64File;
} else {
    echo "File not found at $filePath";
}
?>
