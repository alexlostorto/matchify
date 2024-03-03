<?php

$seo_keywords = 'matchify, Matchify, employers, cv personaliser, cv personalizer, personaliser, resume, resume builder';
$seo_description = "Matchify - Matching employees with employers!";
$seo_author = 'Matchify - Matching employees with employers!';
$site_title = 'Matchify';

$title = 'Matchify';

include('../components/header.php');

?>

<link href="styles/css/styles.css" rel="stylesheet">

<section class="side-menu">
    <div class="logo">
        <?php include("../assets/svg/logo.svg"); ?>
        <span>Matchify</span>
    </div>
    <div class="links">
        <div>
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