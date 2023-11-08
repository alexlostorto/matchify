<?php

// PREVENT DIRECT ACCESS
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    // The file is being accessed directly
    http_response_code(403);
    header("Location: /hacknotts/403/");
    exit;
}
// PREVENT DIRECT ACCESS

if (!isset($displayName) || is_null($displayName)) {
    $displayName = 'User';
} 

?>

<style>
    nav {
        top: 0px;
        height: 6rem;
        z-index: 1000;
        background-color: var(--primary);
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.2);
    }

    nav .link-buttons {
        padding-right: 1rem;
    }

    nav span,
    nav a {
        font-size: 1.1rem;
        font-family: "Fredoka", sans-serif !important;
    }

    nav .link-buttons > a,
    nav #user-settings-button > a {
        padding: 0.5rem 1rem;
        border-radius: 7px;
    }

    nav .link-buttons > a:hover {
        cursor: pointer;
        color: black;
        background-color: var(--light-primary);
    }

    nav .dropdown {
        bottom: 0;
        transform: translate(0, 100%);
        height: 0;
        right: 0;
        transition: height 0.3s ease;
        padding: 0;
        background-color: var(--primary);
        border: none;
    }

    nav .dropdown ul {
        display: none !important;
        padding: 0;
        margin: 0;
    }

    nav .dropdown ul li {
        width: 100%;
        border-radius: 7px;
        list-style: none;
        background-color: var(--primary);
        padding: 0.3rem 0.6rem;
    }

    nav .dropdown ul li:hover {
        background-color: var(--light-primary);
    }

    nav .dropdown ul li a,
    nav .dropdown ul li button {
        gap: 0.5rem
    }

    nav .dropdown ul li a:hover {
        color: black;
    }

    nav #user-settings-button:hover .dropdown {
        animation: expandDropdown 0.3s ease forwards;
        border-top: 2px solid var(--accent);
        padding: 0.6rem;
    }

    nav #user-settings-button:hover .dropdown ul {
        display: flex !important;
    }

    nav #user-settings-button:hover > a {
        background-color: var(--light-primary);
        color: black;
    }

    nav #user-settings-button > a > svg {
        transform: scaleY(1); /* Flip vertically */
        transition: transform 0.3s ease; /* Add a transition for a smooth change */
    }

    nav #user-settings-button:hover > a > svg {
        transform: scaleY(-1);
    }

    @keyframes expandDropdown {
        0% {
            height: 3rem;
        }

        100% {
            height: 6rem;
        }
    }
</style>

<nav class="position-relative w-100 d-flex flex-row align-items-center justify-content-between">
    
</nav>

<script>

function logout() {
    document.cookie = "flashi-idToken=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "flashi-refreshToken=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    window.location.href = "/flashi/login";
}

</script>