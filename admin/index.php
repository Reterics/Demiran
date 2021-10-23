<?php
require('../config.php');
include("./auth.php");
require_once("./template.php");

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Kezdőlap - Demiran</title>
    <link rel="stylesheet" href="./css/leaflet.css"/>
    <?php
    admin_head("Kezdőlap - Demiran");
    load_scripts(
            array(
                "./js/leaflet.js",
                "./js/map.js",
                "./js/charts/d3v5.js",
                "./js/charts/calendar.js",
                "./js/charts/lineChart.js",
                "./js/charts/barChart.js",
                "./js/charts/pieChart.js"
            )
    );
    ?>
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
                <h5>Vezérlőpult</h5>
            </div>
            <div class="body">
                <p>IP címed: <?php echo $ip; ?>  </p>
                <p id = "status"></p>
                <a id = "map-link" target="_blank"></a>
                <div id = "map" style = "height: 250px"></div>
            </div>
        </div>


    </div>
    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>


    <div class="col-sm-6">
        <div class="lio-modal">
            <div class="header">
                <h5>Projekt Kategóriák</h5>
            </div>
            <div id="pieChartDiv" class="body">
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
                            userData[name].duration = Math.floor(userData[name].duration / 3600);
                            barChartData.push(userData[name]);
                        }
                    });
                    Demiran.addResize(function(){
                        drawBarChart({
                            selector: "#barChartDiv",
                            name: "username",
                            value: "duration",
                            color: "color",
                            data: barChartData
                        })
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
                    let lineChartData = [];
                    let pieChartData = [];
                    let currentPrice = 0;
                    //console.log(json);
                    json.push({
                        deadline: new Date().getTime(),
                        price: 0
                    });
                    json = json.sort(function(a,b){
                        return new Date(a.deadline) - new Date(b.deadline);
                    });
                    const categories = {};
                    json.forEach(function (d){
                        currentPrice += Number.parseInt(d.price);
                        lineChartData.push({
                            price: currentPrice,
                            deadline: new Date(d.deadline)
                        });
                        if(d.category){
                            if(categories[d.category]){
                                categories[d.category]++;
                            } else {
                                categories[d.category] = 1;
                            }

                        }
                    });
                    Object.keys(categories).forEach(function(category){
                        pieChartData.push({
                            price: categories[category],
                            name:category
                        })
                    });
                    if(lineChartData.length > 1){
                        Demiran.addResize(function(){
                            drawLineChart({
                                selector: "#lineChartDiv",
                                date: "deadline",
                                value: "price",
                                data: lineChartData
                            });

                            drawPieChart({
                                selector: "#pieChartDiv",
                                value:"price",
                                name: "name",
                                data: pieChartData
                            })
                        });
                        drawLineChart({
                            selector: "#lineChartDiv",
                            date: "deadline",
                            value: "price",
                            data: lineChartData
                        });

                        drawPieChart({
                            selector: "#pieChartDiv",
                            value:"price",
                            name: "name",
                            data: pieChartData
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