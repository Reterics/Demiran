<?php
require('../config.php');
include("./auth.php");
require_once("./template.php");

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Dashboard - Secured Page</title>
    <link rel="stylesheet" href="./css/leaflet.css"/>
    <?php admin_head(); ?>
    <script src="./js/leaflet.js"></script>
    <script src="./js/map.js"></script>
</head>
<body>

<?php
admin_header_menu();


$ip = getIPAddress();

?>


<div class="form row" style="padding: 1em;">
    <div class="col-sm-<?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')) {
        echo "6";
    } else {echo "12";}?>">
        <div class="lio-modal">
            <div class="header">
                <h3>Vezérlőpult</h3>
            </div>
            <div class="body">
                <p>IP címed: <?php echo $ip; ?>  </p>
                <p><a href="index.php">Index</a></p>
                <p><a href="users.php">Felhasználók</a></p>
                <a href="logout.php">Logout</a>
                <p id = "status"></p>
                <a id = "map-link" target="_blank"></a>
                <div id = "map" style = "height: 580px"></div>
            </div>
        </div>


    </div>
    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>


    <div class="col-sm-6">
        <div class="lio-modal">
            <div class="header">
                <h3>Konzol</h3>
            </div>
            <div class="body">
                <div class="console">

                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>


    <script>

        const status = document.querySelector('#status');
        const mapLink = document.querySelector('#map-link');

        mapLink.href = '';
        mapLink.textContent = '';

        function success(position) {
            const latitude  = position.coords.latitude;
            const longitude = position.coords.longitude;

            status.textContent = '';
            mapLink.href = `https://www.openstreetmap.org/#map=18/${latitude}/${longitude}`;
            mapLink.textContent = `Latitude: ${latitude} °, Longitude: ${longitude} °`;

            createMap("map", position.coords, {
                onclickPopup:true,
                centerIcon:true
            });

        }

        function error() {
            status.textContent = 'A böngésző nem tudta bemérni a helyzetedet';
        }

        if(!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported by your browser';
        } else {
            status.textContent = 'Locating…';
            navigator.geolocation.getCurrentPosition(success, error);
        }
    </script>


</div>
<?php footer(); ?>
</body>
</html>