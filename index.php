<?php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Demiran CRM és Munkaerőnyilvántartó rendszer</title>
    <link rel="stylesheet" href="style.css" />
    <?php head(); ?>
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
    }
    .cover-container {
        max-width: 42em;
        margin-top: 10vh;
        text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);

        margin-bottom: calc(90vh - 61px - 338px);
        color: #949494;

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
        padding: 0 1.5rem;
    }
    .cover .btn-lg {
        padding: .75rem 1.25rem;
        font-weight: 700;
    }


    /*
     * Footer
     */
    .mastfoot {
        color: rgba(255, 255, 255, .5);
    }

</style>
<div class="cover-container d-flex h-100 p-3 mx-auto flex-column text-center">
    <main role="main" class="inner cover">
        <h1 class="cover-heading">Demiran CRM és Munkaerőnyilvántartó rendszer</h1>
        <p class="lead">A rendszer fejlesztés alatt áll, a funkcionalitás és a Design az idő folyamán változhat. A Béta rendszer használatáért felelősséget nem vállalok.</p>
        <p class="lead">
            <a href="login.php" class="btn btn-lg btn-secondary">Bejelentkezés</a>
        </p>
    </main>
</div>
<footer class="mastfoot mt-auto">
    <div class="inner">
        <p>© 2021 - Reterics Attila</p>
    </div>
</footer>
</body>
</html>