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

<div class="job d-flex flex-column align-items-center justify-content-between">
    <section class="main-job-data d-flex flex-row align-items-center justify-content-between w-100">
        <section class="job-data">
            <h3><?= $title ?></h3>
            <h4><?= $companyName ?></h4>
            <section class="stats d-flex flex-row">
                <div>
                    <?php include("../assets/svg/currency.svg"); ?>
                    <span><?= $salary ?></span>
                </div>
                <div>
                    <?php include("../assets/svg/work.svg"); ?>
                    <span><?= $contract ?></span>
                </div>
                <div>
                    <?php include("../assets/svg/location.svg"); ?>
                    <span><?= $location ?></span>
                </div>
            </section>
        </section>
        <button>Match</button>
    </section>
    <div class="show-description">
        <?php include('../assets/svg/down-arrow.svg'); ?>
        <span>See more</span>
    </div>
    <section class="job-description">
        <h3>Description</h3>
        <p><?= $description ?></p>
    </section>
</div>