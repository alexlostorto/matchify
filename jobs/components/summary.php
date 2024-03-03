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

<link href="styles/css/summary.css" rel="stylesheet">

<section class="summary position-absolute flex-column align-items-center w-100 h-100">    
    <section class="">
        <h1>Updated CV</h1>
        <ul>
            <li>Updated the profile to highlight your expertise in Commercial Property Law.</li>
            <li>Emphasized key skills relevant to the role, such as Commercial Property Law and Legal Writing.</li>
            <li>Reworded the experience section to focus on responsibilities related to Commercial Property transactions and legal documentation.</li>
            <li>These changes make your CV more suitable for the Commercial Property Lawyer position at Talbots Law.</li>
        </ul>
        <div class="buttons">
            <button id="download-button">
                <span>Download</span>
                <?php include("../assets/svg/down-arrow.svg"); ?>
            </button>
            <a id="go-back-button" href="../jobs/">Go back</a>
        </div>
        <div class="cv"></div>
    </section>
</section>