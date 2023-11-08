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
        flex-direction: column;
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
    #go-back-button {
        top: 6.5rem;
        left: 5rem;
        font-weight: 500;
        color: var(--tertiary);
    }

    #go-back-button:hover {
        cursor: pointer;
        opacity: 0.6;
    }

    #go-back-button svg {
        margin-bottom: 0.1rem;
        margin-right: 0.5rem;
    }

    .main-view {
        margin-top: 6rem;
        margin-left: 1rem;
        width: clamp(50rem, 60%, 100rem);
    }

    .main-view h1 {
        font-size: 2.3rem;
        font-weight: 600;
    }

    .search {
        margin-top: 3rem;
        margin-right: 1.5rem;
    }

    #search-form {
        display: grid;
        grid-template-columns: 3fr 3fr 1fr;
        gap: 1rem;
    }

    #search-form label {
        margin-bottom: 0.3rem;
    }

    #search-form input {
        padding: 0.6rem 1rem;
        border: 2px solid var(--grey);
        border-radius: 10px;
    }

    #search-form button,
    .job button {
        margin-top: 1.7rem;
        color: var(--primary);
        background-color: var(--tertiary);
        padding: 0.7rem 1.5rem;
        border-radius: 10px;
    }

    .job button {
        height: max-content;
    }

    #search-form button:hover,
    .job button:hover {
        cursor: pointer;
        opacity: 0.6;
    }

    .filters {
        gap: 1rem;
    }

    .filters .dropdown {
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        border: 2px solid var(--grey);
        width: max-content;
        border-radius: 10px;
        font-weight: 500;
    }

    .filters .dropdown svg {
        margin-bottom: 0.2rem;
    }

    .filters .dropdown svg path {
        fill: var(--grey) !important;
    }

    .job-listings {
        margin-top: 2rem;
        gap: 1rem;
        overflow-y: scroll;
        height: 35%;
        padding-right: 0.3rem;
    }

    .job-listings .job {
        padding: 1rem;
        border: 2px solid var(--grey);
        border-radius: 10px;
    }

    .job-listings .job button {
        margin: 0;
    }

    .job-listings .job h3 {
        margin-bottom: 0.2rem;
        font-weight: 600;
        color: var(--tertiary);
    }

    .job-listings .job h4 {
        font-size: 1rem;
        margin-bottom: 0.8rem;
    }

    .job .stats {
        gap: 1.5rem;
    }

    .job .stats div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.2rem;
    }

    .job-description {
        display: none;
    }

    .job-description h3 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    #loading-screen {
        z-index: 999;
        background-color: var(--primary);
        gap: 1rem;
        top: 0;
        transition: all 1s ease;
    }

    .show-description {
        width: 100%;
        margin-top: 0.6rem;
    }

    .show-description svg {
        transform: rotate(0);
        transition: all 0.2s ease;
    }

    .show-description:hover {
        cursor: pointer;
        opacity: 0.6;
    }
</style>

<section id="loading-screen" class="position-absolute d-flex flex-column align-items-center justify-content-center w-100 h-100">
    <h4>Did you know we're not actually a dating site?</h4>
    <?php include('./components/spinner.php'); ?>
</section>
<a id="go-back-button" class="position-absolute d-flex align-items-center justify-content-center" href="../home/">
    <?php include('../assets/svg/left-arrow.svg'); ?>
    <span>Go back</span>
</a>
<section class="main-view">
    <h1>Search for a job you want to apply to</h1>
    <section class="search">
        <form id="search-form">
            <div class="d-flex flex-column">
                <label for="job-title">Job title</label>
                <input id="job-title" placeholder="Junior UX Designer" type="text">
            </div>
            <div class="d-flex flex-column">
                <label for="location">Location</label>
                <input id="location" placeholder="Birmingham" type="text">
            </div>
            <button>Search</button>
        </form>
    </section>
    <section class="filters d-flex flex-row align-items-center">
        <div id="date-dropdown" class="dropdown">
            <span>Date</span>
            <?php include("../assets/svg/down-arrow.svg"); ?>
        </div>
        <div id="type-dropdown" class="dropdown">
            <span>Type</span>
            <?php include("../assets/svg/down-arrow.svg"); ?>
        </div>
    </section>
    <section class="job-listings d-flex flex-column">
        <?php
        
        // Read the JSON data from the file
        $jsonData = file_get_contents('../assets/json/software-dev.json'); // Replace 'data.json' with the actual path to your JSON file

        // Parse the JSON data
        $data = json_decode($jsonData);

        // Check if the JSON data was successfully parsed
        if ($data === null) {
            die('Failed to parse JSON data');
        }

        // Iterate over each object in the JSON data
        foreach ($data as $item) {
            $title = $item->title;
            $jobURL = $item->jobUrl;
            $description = $item->description;
            $contract = $item->contractType;
            $salary = 'Negotiable';
            $location = 'London';
            $companyName = $item->companyName;

            if ($item->salary != '') {
                $salary = $item->salary;
            }
            
            include('./components/job-listing.php');
        }
        
        ?>
    </section>
</section>

<?php include('./components/summary.php'); ?>

<script>

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function load() {
    await sleep(2000);
    document.getElementById("loading-screen").style.top = "-100%";
}

const jobs = document.querySelectorAll(".job button");
jobs.forEach(function (job) {
    job.addEventListener("click", async function () {
        this.textContent = 'Matching...';
        const summary = await createCV();
        console.log(summary);
        console.log(summary['choices'][0]['text']);
        document.querySelector(".summary").style.display = "flex";
        document.querySelector('html').style.overflow = "auto";
    });
});

const seeMores = document.querySelectorAll('.show-description');
seeMores.forEach(function (button) {
    button.addEventListener("click", function() {
        if (this.nextElementSibling.style.display == 'block') {
            this.nextElementSibling.style.display = 'none';
            this.querySelector('span').textContent = 'See more';
        } else {
            this.nextElementSibling.style.display = 'block';
            this.querySelector('span').textContent = 'See less';
        }
    });
});

const description = "About Sparta Global\n\nWe employ people from all backgrounds and give them careers within technology, working with enthusiastic individuals to give them the skills for success within the public and private sectors. We design careers, coach future leaders, and promote a more diverse and equal landscape with our work garnering over 10 awards across L&D and ED&I. We are a Top 20 Employer for Social Mobility and proud to say BCorp certified.\n\nAbout This Role\n\nWorking as a Software Developer you will work closely with other developers, product managers, designers, business analysts and testers to constantly create, maintain and modify systems to meet the demands of the business and their stakeholders.\n\nYou will not only design and write well-formed, readable code but you'll be well-versed in standard practices such as Test-Driven Development and Behaviour-Driven Development and understand how you can work efficiently within agile continuous integration and continuous development (CI/CD) pipelines for your software delivery process.\n\nWe're not expecting you to have the proficiencies right away - that's where our award-winning Academy comes in. We are the experts in building skills and confidence in a fun and supportive environment that will not only challenge you but also develop your specialist capabilities ready to work on our clients' projects.\n\nWhat we're looking for\n\nTo be successful for this role you will demonstrate a level of ability in a choice of Java or C#. You will be passionate about technology and eager to learn programme development to an advanced level.\n\nWe also look for the following traits that align with companies' values:\n\nEmpathy and Diversity - You operate with integrity, respect everyone and set a good example for the team.\n\nDrive - You challenge yourself to exceed targets to take pride in your work.\n\nCollaboration - You work in synergy with others, supportive, approachable, build healthy relationships.\n\nInnovation - Your inquisitiveness knows no bounds and you love to learn. You embrace creativity and enjoy working with different people and their viewpoints.\n\nFlexibility - Adaptable to change, you are calm and compassionate when responding to the unexpected.\n\nWhy should you apply?\n\nWe see ourselves as a people-powered business that likes to recognise and reward the hard work of our employees. We promote continuous learning and development with increasing earning potential for everyone who joins us. By the end of your first year, based on our performance metrics, you can expect to earn an average of £29,000.\n\nWe Also Provide\n\n\n * 20 days annual leave + bank holidays.\n * An extra day off for your birthday.\n * Pension.\n * Discounted gym membership.\n * Eye care.\n * Death in service cover.\n * Cycle to work scheme.\n * Season ticket loan.\n * Bonuses and structured pay rises.\n * Employee assistance program.\n * Yearly budget for personal development.\n * Access to alumni and community networks.\n * Opportunities to be brand ambassadors.\n   \n   \n\nBeing employed by Sparta Global is an investment in your future that pays dividends along the way. We give you breadth of experience and skills, along with increasing opportunities to develop further and earn more. No two career paths look the same at Sparta.\n\nMinimum Requirements\n\nWe are a national organisation serving clients across the country. After completing remote training, you may be deployed to various client sites throughout the UK. Flexibility and willingness to relocate are essential as specific locations cannot be guaranteed. We welcome applicants from diverse backgrounds and experience levels, but the successful candidate must, by the start of employment, have permission to work in the UK.\n\nOur Recruitment Process\n\nOnline Application: Interested candidates can apply online through our application portal. Our talent team will review applications and contact qualified candidates within 48 hours for further recruitment steps.\n\nTelephone Interview: Candidates who pass the initial screening will be invited to a telephone interview. We will assess communication skills, motivation, professionalism, and delve deeper into goals, interests, and background.\n\nOnline Assessments: Successful candidates from the telephone interview will complete online assessments evaluating technical competence in programming and cognitive abilities.\n\nCompetency Interview: Candidates excelling in the assessments will be invited to a competency interview. This interview provides an opportunity to showcase clear communication of technical concepts and behavioural competencies with relevant examples. We value candidates who demonstrate personality, collaborative skills, and a growth mindset.\n\nHow to Best Prepare for the Interview\n\nResearch the STAR Method (Situation, Task, Action, Result) and our six behavioural competencies. This knowledge will enable you to answer competency-based questions effectively. You can find a handy guide on answering competency questions on our website.\n\nVisit our YouTube Channel: Gain valuable insights and expert advice on virtual interviews,, strategies to manage nerves, and tips on nonverbal communication.\n\nWe look forward to receiving your application - good luck!";

function getCVData() {
    const url = new URL(window.location.href); // Replace with your URL
    const params = new URLSearchParams(url.search);

    // Get individual parameters
    return params.get("fileContents");
}

async function createCV() {
    let url = '../scripts/openAI.php';
    
    const data = {
        "cvData": getCVData(),
        "jobDescription": description
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
    text = text.replace('API Response: ', '');
    return JSON.parse(text);
}

load();

</script>

<?php include('../components/footer.php'); ?>