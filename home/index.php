<?php

$seo_keywords = 'matchify, Matchify, employers, cv personaliser, cv personalizer, personaliser, resume, resume builder';
$seo_description = "Matchify - Matching employees with employers!";
$seo_author = 'Matchify - Matching employees with employers!';
$site_title = 'Matchify';

$title = 'Matchify';

include('../components/header.php');

?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter&family=Inder&family=Fredoka&display=swap');

    html,
    body {
        display: flex;
        align-items: center;
        flex-direction: row;
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

<style>
    .side-menu {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 2.5rem 0 2.5rem 4rem;
        width: 21rem;
        height: 100%;
        background-color: var(--tertiary);
    }

    .side-menu .logo {
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 6rem;
        font-size: 1.2rem;
    }

    .side-menu .links > div {
        padding: 0.8rem 0.5rem;
        border-radius: 10px;
    }

    .side-menu .links > div:hover {
        cursor: pointer;
        background-color: var(--accent);
    }

    .side-menu .links a {
        color: var(--primary);
    }

    .side-menu .links path {
        fill: var(--primary) !important;
    }

    .side-menu .links svg {
        width: 1rem;
        height: 1rem;
    }

    .main-view {
        display: flex;
        flex-direction: column;
        padding: 2.5rem 6rem;
    }

    .main-view .profile {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .main-view .profile:hover {
        cursor: pointer;
        opacity: 0.6;
    }

    .main-view .profile svg {
        transition: all 0.3s ease;
    }

    .main-view .profile:hover svg {
        transform: rotate(180deg);
    }

    .main-view .message {
        font-weight: 500;
        padding: 0.5rem 0.7rem;
        border-radius: 10px;
        border: 2px solid var(--light-grey);
        margin-top: 4rem;
    }

    .main-view .cv-section {
        margin-top: 1rem;
        border-radius: 10px;
        background-color: var(--lighter-grey);
        padding: 2rem;
    }

    .main-view .cv-section h1 {
        font-weight: 600;
    }

    #sort-by {
        margin-top: 1.5rem;
        padding: 0.5rem 1rem;
        border: 2px solid var(--grey);
        width: max-content;
        border-radius: 10px;
        font-weight: 500;
    }

    #sort-by svg {
        margin-bottom: 0.2rem;
    }

    #sort-by svg path {
        fill: var(--grey) !important;
    }

    #upload-button {
        top: 2rem;
        right: 2rem;
        color: var(--primary);
        background-color: var(--tertiary);
        padding: 0.7rem 1.5rem;
        border-radius: 10px;
    }

    #upload-button:hover {
        cursor: pointer;
        opacity: 0.6;
    }

    .cv-container {
        margin-top: 2rem;
    }

    .cv-container .cv {
        padding: 3rem;
        width: 20rem;
        height: 28rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 4px dashed var(--light-grey);
        background: var(--primary);
        color: var(--grey);
        opacity: 0.6;
        background-size: contain;
    }

    .cv-container .cv.unuploaded:hover {
        opacity: 0.6 !important;
        cursor: pointer;
    }

    .cv .overlay {
        background-color: rgba(0, 0, 0, 0.4);
        gap: 1rem;
    }

    .cv .overlay {
        display: flex;
    }

    .cv.unuploaded .overlay {
        display: none;
    }

    .cv .overlay button {
        width: 12rem;
        color: var(--primary);
        background-color: var(--tertiary);
        padding: 0.7rem 1.5rem;
        border-radius: 10px;
    }

    .cv .overlay button:hover {
        background-color: var(--accent);
        cursor: pointer;
    }

    .cv .overlay span {
        position: absolute;
        bottom: 0;
        padding: 0.5rem 1rem;
        width: 100%;
        background-color: var(--primary);
        font-weight: 600;
    }

    .cv-container .cv svg {
        width: 2rem;
        height: 2rem;
        margin-bottom: 0.5rem;
    }

    .cv-container .cv svg path {
        fill: var(--grey);
        opacity: 0.6;
    }
</style>

<section class="side-menu">
    <div class="logo">
        <?php include("../assets/svg/logo.svg"); ?>
        <span>Matchify</span>
    </div>
    <div class="links">
        <div style="background-color: var(--accent);">
            <?php include("../assets/svg/pencil.svg"); ?>
            <a href="#">CV Matching</a>
        </div>
        <div>
            <?php include("../assets/svg/settings.svg"); ?>
            <a href="#">Settings</a>
        </div>
        <div>
            <?php include("../assets/svg/question.svg"); ?>
            <a href="#">Help & Support</a>
        </div>
    </div>
</section>
<section class="main-view w-100 h-100">
    <div class="profile w-100">
        <img src="../assets/images/profile.png" alt="">
        <?php include("../assets/svg/down-arrow.svg"); ?>
    </div>
    <div class="message">
        Welcome to Matchify! Upload a CV to match it to your dream job!
    </div>
    <section class="cv-section position-relative">
        <h1>Your CVs</h1>
        <div id="sort-by">
            <span>Sort by</span>
            <?php include("../assets/svg/down-arrow.svg"); ?>
        </div>
        <div class="cv-container">
            <div class="cv position-relative unuploaded">
                <input type="file" class="fileInput" style="display: none;">
                <?php include("../assets/svg/plus.svg"); ?>
                <p class="text-center">Drag and drop or browse your files.</p>
                <section class="overlay flex-column align-items-center justify-content-center position-absolute w-100 h-100">
                    <button id="match-button">Match to a job</button>
                    <button>Edit</button>
                    <button>Download</button>
                    <span>Google UX Designer</span>
                </section>
            </div>
        </div>
        <button id="upload-button" class="position-absolute">
            <?php include("../assets/svg/plus.svg"); ?>
            <span>Upload CV</span>
        </button>
        <input id="fileInput" type="file" style="display: none;">
    </section>
</section>

<script>

let fileContents = "";
let fileName = "";

function triggerFileUpload() {
    document.getElementById("fileInput").click();
}

document.querySelector("#upload-button").addEventListener("click", triggerFileUpload);
document.querySelector(".cv").addEventListener("click", triggerFileUpload);

// Listen for changes in the file input (optional)
document.getElementById("fileInput").addEventListener("change", function() {
    processFile();
});

function processFile() {
    const fileInput = document.getElementById("fileInput");
    const selectedFile = fileInput.files[0];

    if (selectedFile) {
        const reader = new FileReader();

        reader.onload = function(event) {
        // The result property contains the base64-encoded content of the file
        fileContents = event.target.result;
        fileName = selectedFile.name;
        const cv = document.querySelector(".cv");
        cv.style.backgroundImage = `url(../assets/images/resume.webp)`;
        cv.style.opacity = 1;
        cv.querySelector('svg').style.display = 'none';
        cv.querySelector('p').style.display = 'none';
        cv.classList.add('uploaded');
        document.querySelector(".cv").removeEventListener("click", triggerFileUpload);

        // You can use 'base64Content' as needed, for example, sending it to a server or displaying it.
        };

        // Read the file as a data URL, which will give you the base64-encoded content
        reader.readAsDataURL(selectedFile);
    } else {
        console.log("No file selected.");
    }
}

document.querySelector('.cv').addEventListener('click', function() {
    console.log(document.querySelector('.cv').classList.contains('uploaded'));
    if (document.querySelector('.cv').classList.contains('uploaded')) {
        document.querySelector('.cv').classList.remove('unuploaded');
    }
})

document.querySelector('#match-button').addEventListener("click", function() {
    document.querySelector('#match-button').textContent = 'Matching...';
    redirect();
})

function getFileExtension(fileName) {
    return fileName.slice(((fileName.lastIndexOf(".") - 1) >>> 0) + 2);
}

async function convertToText() {
    let url = '';
    const fileExtension = getFileExtension(fileName);

    if (fileExtension == 'doc') {
        url = '../api/convert/docToText.php';
    } else if (fileExtension == 'docx') {
        url = '../api/convert/docxToText.php';
    } else if (fileExtension == 'pdf') {
        url = '../api/convert/pdfToText.php';
    } else {
        console.log("File extension not supported");
        return;
    }
    
    const parts = fileContents.split('base64,');
    base64Data = parts[1];
    
    const data = {
        "fileData": base64Data
    }; // Replace with your request data

    let response = await fetch(url, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
        "Content-Type": "application/json"
        // You may need to set additional headers as required by your API
    }
    })
    let text = await response.text();
    return text;
}

async function redirect() {
    if (fileContents != '') {
        textContent = await convertToText();
        console.log(textContent);
        console.log('../jobs/' + '?fileContents=' + textContent);
        window.location.href = '../jobs/' + '?fileContents=' + textContent;
    }
}

</script>

<?php include('../components/footer.php'); ?>