<?php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Demiran CRM és Munkaerőnyilvántartó rendszer</title>
    <link rel="stylesheet" href="./style.css" />
    <?php head("Demiran CRM és Munkaerőnyilvántartó rendszer"); ?>
</head>
<body>
<?php headerHTML(); ?>
<style>
    body {
        -ms-flex-pack: center;
        -webkit-box-pack: center;
        justify-content: center;
        box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
        overflow: hidden;
        background-image: url(/admin/img/DSC01980.jpg);
    }
    .cover-container {
        max-width: 35em;
        margin-top: 10vh;
        text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .2);

        margin-bottom: calc(90vh - 61px - 338px);
        color: #3b3b3bdb;
    }

    /*
     * Header
     */
    .masthead {
        margin-bottom: 2rem;
    }

    .masthead-brand {
        margin-bottom: 0;
    }

    .nav-masthead .nav-link {
        padding: .25rem 0;
        font-weight: 700;
        color: rgba(255, 255, 255, .5);
        background-color: transparent;
        border-bottom: .25rem solid transparent;
    }

    .nav-masthead .nav-link:hover,
    .nav-masthead .nav-link:focus {
        border-bottom-color: rgba(255, 255, 255, .25);
    }

    .nav-masthead .nav-link + .nav-link {
        margin-left: 1rem;
    }

    .nav-masthead .active {
        color: #fff;
        border-bottom-color: #fff;
    }

    @media (min-width: 48em) {
        .masthead-brand {
            float: left;
        }
        .nav-masthead {
            float: right;
        }
    }


    /*
     * Cover
     */
    .cover {
        padding: 0 1rem;
    }
    .cover .btn-lg {
        padding: .5rem 1rem;
        font-weight: 700;
    }


    /*
     * Footer
     */
    .mastfoot {
        color: rgba(255, 255, 255, .5);
    }

    h1.cover-heading {
        font-size: 2rem;
    }

    .lead {
        font-size: 1rem;
    }

</style>
<div class="cover-container d-flex p-3 flex-column text-center" style="height: calc(100vh - 121px);margin-right: auto!important;">
    <main role="main" class="inner cover" style="    background-color: #ffffffd9;">
        <h1 class="cover-heading">Demiran Projektmenedzsment Szoftver</h1>
        <p class="lead">A rendszer fejlesztés alatt áll, a funkcionalitás és a Design az idő folyamán változhat. A Béta rendszer használatáért felelősséget nem vállalok.</p>
        <p class="lead">
            <a href="login.php" class="btn btn-lg btn-secondary" style="font-weight: 500;background-color: #3b3b3b;">Bejelentkezés</a>
        </p>
    </main>

</div>



<?php footer(); ?>
</body>
</html>