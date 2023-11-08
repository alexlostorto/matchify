<?php

// Specify the target URL you want to redirect to
$targetURL = './home/';

// Use the header function to send a "301 Moved Permanently" status code and set the "Location" header to the target URL
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $targetURL);

// Terminate the script to ensure the redirect is followed
exit();

?>
