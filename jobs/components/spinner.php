<?php

// PREVENT DIRECT ACCESS
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    // The file is being accessed directly
    http_response_code(403);
    header("Location: /403/");
    exit;
}
// PREVENT DIRECT ACCESS

?>

<link href="styles/css/spinner.css" rel="stylesheet">

<div class="lds-ring"><div></div><div></div><div></div><div></div></div>