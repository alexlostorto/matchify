<style>
    .summary {
        display: none;
        z-index: 100;
        background-color: var(--primary);
        margin-top: 6rem;
    }

    .summary h1 {
        font-size: 2.3rem;
        font-weight: 600;
    }

    .summary ul {
        margin-top: 2.5rem;
    }

    .summary ul li {
        margin-bottom: 1rem;
    }

    .summary ul li::marker {
        color: var(--tertiary);
    }

    .summary > section {
        width: clamp(50rem, 60%, 100rem);
    }

    #download-button {
        margin-top: 1rem;
        color: var(--primary);
        background-color: var(--tertiary);
        padding: 0.7rem 1.5rem;
        border-radius: 10px;
    }

    #download-button:hover {
        opacity: 0.6;
        cursor: pointer;
    }

    .download-button svg {
        margin-bottom: 0.2rem;
    }

    #download-button svg path {
        fill: var(--primary);
    }

    .summary #go-back-button {
        margin-left: 1rem;
        color: var(--grey);
        padding: 0.6rem 1.5rem;
        border: 2px solid var(--grey);
        border-radius: 10px;
    }

    .summary #go-back-button:hover {
        cursor: pointer;
        color: var(--primary);
        background-color: var(--grey);
    }

    .cv {
        background-image: url(../assets/images/resume.webp);
        background-size: cover;
        margin-top: 2rem;
        width: 100%;
        height: 70rem;
        border: 4px dashed var(--light-grey);
        margin-bottom: 5rem;
    }
</style>

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