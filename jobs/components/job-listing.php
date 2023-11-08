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