<?php

// PREVENT DIRECT ACCESS
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    // The file is being accessed directly
    http_response_code(403);
    header("Location: /hacknotts/403/");
    exit;
}
// PREVENT DIRECT ACCESS

$seo_keywords = 'matchify, Matchify, employers, cv personaliser, cv personalizer, personaliser, resume, resume builder';
$seo_description = "Matchify - Matching employees with employers!";
$seo_author = 'Matchify';
$site_title = 'Matchify - Matching employees with employers!';

include('../components/header.php');

?>

<style>
    html,
    body {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    input,
    button,
    textarea,
    select {
        margin: 0;
        padding: 0;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: inherit;
        color: inherit;
        background: none;
        appearance: none;
    }

    /* Change selection color */
    ::selection {
        background-color: var(--secondary);
        color: black;
    }

    /* Fallback for older browsers */
    ::-moz-selection {
        background-color: var(--secondary);
        color: black;
    }
</style>

<section class="w-100 h-100">
    <h1>Transform Your CV to</h1>
    <h1>Perfectly Match with</h1>
    <h1 style="">Your Dream Job</h1>
</section>

<?php include('../components/footer.php'); ?>