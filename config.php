<?php
if(!file_exists(dirname(__FILE__).'/env.php')) {
    header("Location: /install.php");
    exit;
}
require_once('env.php');

mysqli_report(MYSQLI_REPORT_STRICT);
$connection = false;
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if(mysqli_connect_errno()){
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

require_once "admin/backend/settings.php";
$globalSettings = new Settings();
$globalSettings->loadSettings();
/**
 * Ezzel a függvénnyel írok ki részleteket az oldalra fejlesztés során
 * @param {string} $title
 * @param {string} $content
 */
function info($title, $content) {
    print "<br><strong>".$title.":</strong>".$content;
}

function head($title){
    favicon_meta();
    meta_tags($title);
?>
    <link href="./admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="./admin/css/product.css" rel="stylesheet">
    <link href="./admin/css/jquery-ui.min.css" rel="stylesheet">
    <script src="./admin/js/jquery.min.js" rel="script"></script>

    <script src="./admin/js/bootstrap.js" rel="script"></script>

<?php
}

function headerHTML(){
?>
<header class="main">
    <nav class="navbar navbar-expand-lg navbar-dark" ><a
            class="navbar-brand" href="/">
            <img class="d-inline-block align-top" height="30" src="./admin/img/logo.svg" alt="demiran Logo">
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
        <p> Copyright (c) 2021, Attila Reterics - Minden Jog Fenntartva</p>
    </div>
    <div class="footer-theme-parent" style="position:fixed; bottom:0; right:0">
        <label style="font-weight: normal">Téma:
            <select id="theme_selector">
                <option value="default">Sötét</option>
                <option value="blue">Kék</option>
                <option value="gray">Szürke</option>
                <option value="light">Világos</option>
            </select>
        </label>
    </div>
    <script type="application/javascript">
        const theme_selector = document.getElementById('theme_selector');
        if(theme_selector){
            window.selectedTheme = window.localStorage.getItem("theme");
            if(window.selectedTheme) {
                theme_selector.querySelectorAll("option").forEach(function(option){
                    if(option.value === window.selectedTheme) {
                        option.selected = true;
                    }
                });
            } else {
                window.localStorage.setItem("theme", "default");
            }

            theme_selector.onchange = function(){
                console.log('Change to ' + theme_selector.value);
                window.localStorage.setItem("theme", theme_selector.value);
                applyTheme(theme_selector.value);
            }
        }

        if(!window.applyTheme) {
            window.applyTheme = function(themeName){
                let uri = "./admin/css/";
                if(location.href.includes("/admin")) {
                    uri = uri.replace("/admin", "");
                }
                const availableStyle = document.getElementById("loadedTheme");
                if(!availableStyle && themeName !== "themeName"){
                    const style = document.createElement("link");
                    style.setAttribute("rel", "stylesheet");
                    style.setAttribute("id", "loadedTheme");
                    style.setAttribute("href", uri+"/theme-"+themeName+".css");

                    document.head.appendChild(style);
                } else if(themeName !== "default"){
                    availableStyle.setAttribute("href", uri+"./theme-"+themeName+".css");
                } else {
                    availableStyle.setAttribute("href", "#");
                }
            };
        }

        window.selectedTheme = window.localStorage.getItem("theme");
        if(window.selectedTheme && window.selectedTheme !== "default"){
            applyTheme(window.selectedTheme);
        }
    </script>
</footer>
<?php
}

function favicon_meta(){
    ?>
    <link rel="apple-touch-icon" sizes="57x57" href="/admin/img/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/admin/img/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/admin/img/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/admin/img/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/admin/img/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/admin/img/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/admin/img/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/admin/img/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/admin/img/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/admin/img/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/admin/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/admin/img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/admin/img/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
<?php
}

function meta_tags($titleOutside){
    $title = "Demiran Projektmenedzsment Szoftver";
    if(isset($titleOutside) && $titleOutside != "") {
        $title = $titleOutside;
    }
    $description = "Demiran segít kezelni a Projekteket, a Csapatot, a Megrendelőket, és óraszámokat egy helyen.";
    //$keywords = "";
?><meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="author" content="Attila Reterics" />
    <meta name="description" content="<?php echo $description; ?>">

    <meta property="og:description" content="<?php echo $description; ?>" />
    <meta property="og:title" content="<?php echo $title; ?>" />
    <meta property="og:locale" content="hu_HU" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Demiran" />

    <?php
}