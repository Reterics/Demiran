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
    <script src="./js/charts/d3v5.js" ></script>

    <script src="./js/charts/calendar.js" ></script>
    <script src="./js/charts/lineChart.js" ></script>
    <script src="./js/charts/barChart.js" ></script>
</head>
<body>

<?php
require_once('./backend/main.php');
admin_header_menu();


$ip = getIPAddress();

?>

<div class="row top_outer_div">
    <div class="col-sm-6">
        <div class="lio-modal">
            <div class="header">
                <h5><?php
                    if($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin'){
                        echo "Költségvetés";
                    } else{
                        echo "Roadmap";
                    }
                    ?></h5>
            </div>
            <div id="lineChartDiv" class="body" style="height: 190px">


            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="lio-modal">
            <div class="header">
                <h5>Munkavégzés (óra)</h5>
            </div>
            <div id="barChartDiv" class="body">


            </div>
        </div>
    </div>
</div>


<div class="form row top_outer_div">
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


        Demiran.call("get_project_hours", "", function (e,r){
            if(!e && r) {
                let json = null;
                try{
                    json = JSON.parse(r);
                }catch (e){
                    console.warn(e);
                }
                if(json && Array.isArray(json)){
                    const barChartData = [];
                    let colorIndex = 0;
                    const colors = ["#DDA0DD", "steelblue", "#8A2BE2", "#F08080", "#7FFF00"];

                    const userData = {};
                    json.forEach(function (d){
                        const start_time = new Date(d.start_time);
                        const end_time = new Date(d.end_time);

                        //seconds
                        const duration = Math.floor((end_time.getTime() - start_time.getTime()) /1000);

                        if(!userData[d.username]){
                            colorIndex++;
                            if(!colors[colorIndex]){
                                colorIndex = 0;
                            }
                            userData[d.username] = {
                                username: d.username,
                                duration: duration,
                                color: colors[colorIndex]
                            }
                        } else {
                            userData[d.username].duration += duration
                        }

                    });
                    Object.keys(userData).forEach(function (name){
                        if(userData[name]){
                            // Convert to hours
                            userData[name].duration = Math.floor(userData[name].duration / 3600)
                            barChartData.push(userData[name]);
                        }
                    });
                    drawBarChart({
                        selector: "#barChartDiv",
                        name: "username",
                        value: "duration",
                        color: "color",
                        data: barChartData
                    })
                }

            }
        });


        Demiran.call("get_project_prices","", function(e,r){
            if(!e && r) {
                let json = null;
                try{
                    json = JSON.parse(r);
                }catch (e){
                    console.warn(e);
                }
                if(json && Array.isArray(json)){
                    const lineChartData = [];
                    let currentPrice = 0;
                    json.forEach(function (d){
                        currentPrice += Number.parseInt(d.price);
                        lineChartData.push({
                            price: currentPrice,
                            deadline: new Date(d.deadline)
                        });
                    });

                    if(lineChartData.length > 1){
                        drawLineChart({
                            selector: "#lineChartDiv",
                            date: "deadline",
                            value: "price",
                            data: lineChartData
                        })
                    }
                }
                //console.log(JSON.parse(r));
            }

        });

    </script>


</div>
<?php footer(); ?>
</body>
</html>