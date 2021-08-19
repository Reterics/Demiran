<?php

require_once('env.php');

mysqli_report(MYSQLI_REPORT_STRICT);
$connection = false;
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if(mysqli_connect_errno()){
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

require_once "admin/lib/settings.php";
$globalSettings = new Settings();
$globalSettings->loadSettings();
/**
 * Ezzel a függvénnyel írok ki részleteket az oldalra fejlesztés során
 * @param string $title
 * @param string $content
 */
function info($title, $content) {
    print "<br><strong>".$title.":</strong>".$content;
}

function head(){
    ?>
    <link href="./admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="./admin/css/product.css" rel="stylesheet">
    <link href="./admin/css/jquery-ui.min.css" rel="stylesheet">
    <script src="./admin/js/jquery.min.js" rel="script" ></script>

    <script src="./admin/js/bootstrap.js" rel="script" ></script>

    <?php
}

function headerHTML(){
    ?>

<header class="main">
    <nav class="navbar navbar-expand-lg navbar-dark" ><a
            class="navbar-brand" href="#">
            <img class="d-inline-block align-top" height="30" src="./admin/img/logo.svg" style="height: 35px; width: 100%;" alt="demiran Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#headerMenu"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigatiopn"><span
                class="navbar-toggler-icon"></span></button>
        <div id="headerMenu" class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link index" href="index.php">Kezdőlap</a></li>
                <li class="nav-item"><a class="nav-link page" href="page.php">Oldalak</a></li>
                <li class="nav-item" style="display: none"><a class="nav-link registration" href="registration.php">Regisztráció</a></li>
                <li class="nav-item"><a class="nav-link login" href="login.php">Bejelentkezés</a></li>


            </ul>

        </div>

    </nav>
</header>
<script>
    const selected = location.pathname.split("/").pop().split(".")[0];

    const node = document.querySelector("#headerMenu li a."+selected);
    if(selected && node){
        document.querySelectorAll("#headerMenu li").forEach(function (node) {
            node.classList.remove("active");
        });
        node.parentElement.classList.add("active");
    }

</script>

<?php
}



function sqlGetFirst($sql){
    global $connection;
    if($connection){
        $query = mysqli_query($connection, $sql);
        if(!$query){
            return null;
        }
        $row = mysqli_fetch_array($query);
        return $row;
    } else {
        return null;
    }

}

function sqlGetAll($sql) {
    global $connection;
    $array = array();
    if ($connection) {
        $query = mysqli_query($connection, $sql);
        if (!$query){
            return $array;
        }
        //$array[] = mysqli_fetch_array($query, MYSQLI_ASSOC);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
        {
            array_push($array, $row);
        }
        return $array;
    } else {
        return $array;
    }
}


function footer(){
?>
<footer class="footer">
    <div class="container">
        <p style="    font-size: 12px;"> Copyright (c) 2021, Attila Reterics - Minden Jog Fenntartva</p>
    </div>
</footer>

<?php
}