<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 2/19/2021
 * Time: 6:27 PM
 */
require('../config.php');
include("./auth.php");
require_once("./template.php");

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Beállítások</title>
    <link rel="stylesheet" href="./css/leaflet.css"/>

    <?php admin_head(); ?>
    <script src="./js/leaflet.js"></script>
    <script src="./js/map.js"></script>
</head>
<body>
<?php
admin_header_menu();
require_once "process.php";

?>
<div style="padding: 1em;">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Bejelentkezési Beállítások</h5>

                </div>
               
                <div class="body settings" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;display: flex">

<form method="post" enctype="multipart/form-data" style="max-width: 500px;">
    <input type="hidden" name="main-settings" id="main-settings" value="1">
                    <?php


                    //$sql = "SELECT id,user,start_time,end_time,note,task FROM shift_list;";
                    $sql = "SELECT * FROM settings WHERE id < 3;";
                    if (isset($connection) && $connection) :
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_array($result)) {



                            ?>

                            <div style="display: flex;max-width:500px;flex-flow: wrap;" class="parent-element" data-id="<?php echo $row['id']?>">
                                <label for="id<?php echo $row['id']?>"><?php echo ucwords(join(" ",explode("_",$row['setting_name']))) ?></label>
                                       <input type="hidden" id="id<?php echo $row['id']?>" value="<?php echo $row['message']?>" name="id<?php echo $row['id']?>">
                                <div class="bubbles-<?php echo $row['id']?>" style="display: flex">
                                <?php $pieces = explode(";", $row['message']);

                                foreach ($pieces as $piece) {
                                    if( $row['setting_name'] === "ip_stack") {
                                        echo "<div class='setting-bubble' data-value='".$piece."'>".$piece."<span class='close-icon'></span></div>";
                                    } else if($row['setting_name'] === "geo_data"){
                                        $geo_parts = explode(",", $piece);
                                        if(count($geo_parts)>2){
                                            echo "<div class='setting-bubble' data-value='".$piece."'>Lat: ".$geo_parts[0]." Lon: ".$geo_parts[1]." Meter: ".$geo_parts[2]."<span class='close-icon'></span></div>";
                                        } else {
                                            echo "<div class='setting-bubble' data-value='".$piece."'>".$piece."<span class='close-icon'></span></div>";
                                        }

                                    }

                                }
                                ?> </div><?php

                                if( $row['setting_name'] === "ip_stack"):?>

                                    <div style="width:100%"><label
                                                for="id-<?php echo $row['id']?>">IP or Range: </label><input type="text" id="id-<?php echo $row['id']?>" value="" name="id-<?php echo $row['id']?>" style="width: 95%;margin-bottom: 5px;">
                                        <input type="button" id="add-<?php echo $row['id']?>" value="+" class="add-ip btn btn-outline-secondary" />
                                        <input type="button" value="Saját IP" class="my-ip btn btn-outline-secondary" />
                                        <input type="button" value="Saját IP Stack" class="my-ip-stack btn btn-outline-secondary" />
                            </div>
                                <?php
                                elseif($row['setting_name'] === "geo_data"):?>
                                    <div style="width:100%"><label
                                                for="id-<?php echo $row['id']?>-lat">Lat: </label><input type="text" id="id-<?php echo $row['id']?>-lat" value="" name="id-<?php echo $row['id']?>-lat">
                                    <label for="id-<?php echo $row['id']?>-lon">Lon: </label><input type="text" id="id-<?php echo $row['id']?>-lon" value="" name="id-<?php echo $row['id']?>-lon">
                                    <label for="id-<?php echo $row['id']?>-meter">Meter: </label><input type="text" id="id-<?php echo $row['id']?>-meter" value="100" name="id-<?php echo $row['id']?>-meter">
                                        <input type="button" id="add-<?php echo $row['id']?>" value="+" name="add<?php echo $row['id']?>" class="add-geo btn btn-outline-secondary">
                                        <input type="button" value="Saját GPS" class="my-gps btn btn-outline-secondary hidden" />

                                    </div>
                                <?php
                                endif;
                                ?>

                            </div>

                            <?php

                        }
                    endif;
                    ?>
                   
</form>

                    <div style="width: 100%">
                        <div id="map" style="height: 100%"></div>

                    </div>
                </div>
                <div class="footer btn-group mr-2">
                    <input type="button" class="btn btn-outline-black" name="send" value="Mentés" id="save" />
                </div>
                    <script>

                        Demiran.applyDragNDrop(".drag-container", ".dragged");

                        const bubbles = document.querySelectorAll(".setting-bubble");
                        bubbles.forEach(function(node){
                            node.onclick = function(){
                                node.outerHTML = "";
                            }
                        });

                        const addIP = document.querySelector(".add-ip");
                        if(addIP){
                            console.log(addIP);
                            const id = addIP.id.replace("add-","");
                            addIP.onclick = function(){
                                const element = document.querySelector("#id-"+id);
                                const bubbles = document.querySelector(".bubbles-"+id);

                                const ipData = element ? element.value || element.getAttribute("value") : "";

                                if(ipData){
                                    if(bubbles){
                                        const bubble = document.createElement("div");
                                        bubble.classList.add("setting-bubble");
                                        bubble.setAttribute("data-value",ipData);
                                        bubble.innerHTML = ipData;
                                        bubble.onclick = function(){
                                            bubble.outerHTML = "";
                                        };
                                        bubbles.appendChild(bubble);
                                    }
                                    console.log(ipData);
                                } else {
                                    alert("Kérlek add meg egy értéket!");
                                }
                            };
                        }
                        const addMyIp = document.querySelector(".my-ip");
                        const addMyIpStack = document.querySelector(".my-ip-stack");
                        const inputIp = document.querySelector("input#id-1");

                        if(inputIp && addMyIp && addMyIpStack){
                            <?php
                            $ip = getIPAddress();
                            $string = 'Hello World Again';
                            $string = explode('.', $ip);
                            array_pop($string);
                            $string = implode('.', $string); ?>
                            const myIP = "<?php echo $ip; ?>";
                            const myIPStack = "<?php echo $string.".1-".$string.".255"; ?>";

                            addMyIp.onclick = function (){
                                inputIp.value = myIP;
                            };
                            addMyIpStack.onclick = function (){
                                inputIp.value = myIPStack;
                            };
                        }

                        const addGeo = document.querySelector(".add-geo");

                        if(addGeo){
                            const id = addGeo.id.replace("add-","");

                            addGeo.onclick = function(){
                                const latNode = document.querySelector("#id-"+id+"-lat");
                                const lonNode = document.querySelector("#id-"+id+"-lon");
                                const meterNode = document.querySelector("#id-"+id+"-meter");
                                const bubbles = document.querySelector(".bubbles-"+id);

                                const lat = latNode ? latNode.value || latNode.getAttribute("value") : "";
                                const lon = lonNode ? lonNode.value || lonNode.getAttribute("value") : "";
                                const meter = meterNode ? meterNode.value || meterNode.getAttribute("value") : "";

                                if(lat && lon && meter){
                                    if(bubbles){
                                        const bubble = document.createElement("div");
                                        bubble.classList.add("setting-bubble");
                                        bubble.setAttribute("data-value",lat+","+lon+","+meter);
                                        bubble.innerHTML = "Lat: "+lat+" Lon: "+lon+" Meter: "+meter;
                                        bubble.onclick = function(){
                                            bubble.outerHTML = "";
                                        };
                                        bubbles.appendChild(bubble);
                                    }
                                    console.log("Lat: "+lat+" Lon: "+lon+" Meter: "+meter);
                                } else {
                                    alert("Kérlek add meg egy értéket!");
                                }
                            };
                            const coords = {
                                latitude:46.738440399999995,
                                longitude:16.9152252
                            };
                            <?php
                            $parts = array();
                            if(isset($_SESSION["geo_string"]) && $_SESSION["geo_string"] != ""){
                                $parts = explode(",", $_SESSION["geo_string"]);
                            }
                            $size = count($parts);
                            if($size > 2):
                            ?>
                            coords.lat = Number.parseFloat("<?php echo $parts[0]; ?>");
                            coords.lon = Number.parseFloat("<?php echo $parts[1]; ?>");
                            const gps = document.querySelector(".my-gps");
                            if (gps) {
                                gps.classList.remove("hidden");
                                gps.onclick = function (){
                                    const lat = document.querySelector("#id-2-lat");
                                    const lon = document.querySelector("#id-2-lon");
                                    if(lat && lon){
                                        lat.value = coords.lat;
                                        lon.value = coords.lon;
                                    }
                                }
                            }

                            <?php
                            endif;
                            ?>
                            createMap("map", coords, {
                                onclickPopup:true,
                                centerIcon:true,
                                onclick:function (e){
                                    console.log(e);

                                    const lat = document.querySelector("#id-2-lat");
                                    const lon = document.querySelector("#id-2-lon");
                                    if(lat && lon){
                                        lat.value = e.latlng.lat;
                                        lon.value = e.latlng.lng;
                                    }
                                }
                            });

                        }


                        const saveButton = document.querySelector("#save");
                        if (saveButton) {
                            saveButton.onclick = function (){
                                const settings = document.querySelectorAll(".parent-element");
                                if (settings.length) {
                                    settings.forEach(function (setting){
                                        const id = setting.getAttribute("data-id");
                                        if (id) {
                                            const input = setting.querySelector("#id"+id);
                                            const bubbles = setting.querySelectorAll(".setting-bubble");

                                            if (input && bubbles.length) {
                                                let values = "";
                                                bubbles.forEach(function (bubble,index){
                                                    const value = bubble.getAttribute("data-value");
                                                    if (index) {
                                                        values+=";";
                                                    }

                                                    values+=value;
                                                });

                                                input.setAttribute("value", values);
                                                input.value = values;
                                            } else {
                                                console.warn("No Input or Bubbles");
                                                console.log(setting);
                                            }

                                        } else {
                                            console.warn("Invalid Element ID");
                                        }
                                    });
                                    const form = document.querySelector("form");
                                    if(form){
                                        Demiran.post("process.php",Demiran.convertToFormEncoded(form),function(error,data){
                                            if(!error){

                                            }
                                            console.log(data,error);
                                        });
                                    } else {
                                        console.error("Form is not found");
                                    }




                                } else {
                                    console.warn("No Settings Node");
                                }
                            };
                        } else {
                            console.warn("There is no setup button");
                        }
                    </script>





            </div>



        </div>


    </div>

    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Általános Beállítások</h5>
                </div>
                <div class="body">

                </div>
            </div>
        </div>

    </div>
<?php footer(); ?>

</body></html>