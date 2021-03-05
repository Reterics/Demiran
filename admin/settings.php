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

$globalSettings = array();

function echoSetting($name){
    global $globalSettings;
    if(isset($globalSettings[$name])){
        echo $globalSettings[$name];
    }
}
function getSetting($name){
    global $globalSettings;
    if(isset($globalSettings[$name])){
        return $globalSettings[$name];
    } else {
        return null;
    }
}
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
                    $sql = "SELECT * FROM settings;";
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
                                else:
                                    $globalSettings[$row['setting_name']] = $row['message'];
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


                    <label for="timezone">Időzóna</label><select name="timezone" id="timezone">
                        <option value="Europe/Budapest">"(GMT+01:00) Budapest, Belgrade, Bratislava, Ljubljana, Prague"</option>
                    </select>
                    <label for="start_work_auto">Munka automatikusan indul</label>
                    <input id="start_work_auto" name="start_work_auto" type="checkbox" <?php if(getSetting('start_work_auto') == 'true') {echo 'checked';}?>>

                    <label for="stop_work_auto">Munka automatikusan leáll</label>
                    <input id="stop_work_auto" name="stop_work_auto" type="checkbox" <?php if(getSetting('stop_work_auto') == 'true') {echo 'checked';}?>>


                    <script type="text/javascript">
                        const tzStrings = [
                            {"label":"(GMT-12:00) International Date Line West","value":"Etc/GMT+12"},
                            {"label":"(GMT-11:00) Midway Island, Samoa","value":"Pacific/Midway"},
                            {"label":"(GMT-10:00) Hawaii","value":"Pacific/Honolulu"},
                            {"label":"(GMT-09:00) Alaska","value":"US/Alaska"},
                            {"label":"(GMT-08:00) Pacific Time (US & Canada)","value":"America/Los_Angeles"},
                            {"label":"(GMT-08:00) Tijuana, Baja California","value":"America/Tijuana"},
                            {"label":"(GMT-07:00) Arizona","value":"US/Arizona"},
                            {"label":"(GMT-07:00) Chihuahua, La Paz, Mazatlan","value":"America/Chihuahua"},
                            {"label":"(GMT-07:00) Mountain Time (US & Canada)","value":"US/Mountain"},
                            {"label":"(GMT-06:00) Central America","value":"America/Managua"},
                            {"label":"(GMT-06:00) Central Time (US & Canada)","value":"US/Central"},
                            {"label":"(GMT-06:00) Guadalajara, Mexico City, Monterrey","value":"America/Mexico_City"},
                            {"label":"(GMT-06:00) Saskatchewan","value":"Canada/Saskatchewan"},
                            {"label":"(GMT-05:00) Bogota, Lima, Quito, Rio Branco","value":"America/Bogota"},
                            {"label":"(GMT-05:00) Eastern Time (US & Canada)","value":"US/Eastern"},
                            {"label":"(GMT-05:00) Indiana (East)","value":"US/East-Indiana"},
                            {"label":"(GMT-04:00) Atlantic Time (Canada)","value":"Canada/Atlantic"},
                            {"label":"(GMT-04:00) Caracas, La Paz","value":"America/Caracas"},
                            {"label":"(GMT-04:00) Manaus","value":"America/Manaus"},
                            {"label":"(GMT-04:00) Santiago","value":"America/Santiago"},
                            {"label":"(GMT-03:30) Newfoundland","value":"Canada/Newfoundland"},
                            {"label":"(GMT-03:00) Brasilia","value":"America/Sao_Paulo"},
                            {"label":"(GMT-03:00) Buenos Aires, Georgetown","value":"America/Argentina/Buenos_Aires"},
                            {"label":"(GMT-03:00) Greenland","value":"America/Godthab"},
                            {"label":"(GMT-03:00) Montevideo","value":"America/Montevideo"},
                            {"label":"(GMT-02:00) Mid-Atlantic","value":"America/Noronha"},
                            {"label":"(GMT-01:00) Cape Verde Is.","value":"Atlantic/Cape_Verde"},
                            {"label":"(GMT-01:00) Azores","value":"Atlantic/Azores"},
                            {"label":"(GMT+00:00) Casablanca, Monrovia, Reykjavik","value":"Africa/Casablanca"},
                            {"label":"(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London","value":"Etc/Greenwich"},
                            {"label":"(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna","value":"Europe/Amsterdam"},
                            {"label":"(GMT+01:00) Budapest, Belgrade, Bratislava, Ljubljana, Prague","value":"Europe/Budapest"},
                            {"label":"(GMT+01:00) Brussels, Copenhagen, Madrid, Paris","value":"Europe/Brussels"},
                            {"label":"(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb","value":"Europe/Sarajevo"},
                            {"label":"(GMT+01:00) West Central Africa","value":"Africa/Lagos"},
                            {"label":"(GMT+02:00) Amman","value":"Asia/Amman"},
                            {"label":"(GMT+02:00) Athens, Bucharest, Istanbul","value":"Europe/Athens"},
                            {"label":"(GMT+02:00) Beirut","value":"Asia/Beirut"},
                            {"label":"(GMT+02:00) Cairo","value":"Africa/Cairo"},
                            {"label":"(GMT+02:00) Harare, Pretoria","value":"Africa/Harare"},
                            {"label":"(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius","value":"Europe/Helsinki"},
                            {"label":"(GMT+02:00) Jerusalem","value":"Asia/Jerusalem"},
                            {"label":"(GMT+02:00) Minsk","value":"Europe/Minsk"},
                            {"label":"(GMT+02:00) Windhoek","value":"Africa/Windhoek"},
                            {"label":"(GMT+03:00) Kuwait, Riyadh, Baghdad","value":"Asia/Kuwait"},
                            {"label":"(GMT+03:00) Moscow, St. Petersburg, Volgograd","value":"Europe/Moscow"},
                            {"label":"(GMT+03:00) Nairobi","value":"Africa/Nairobi"},
                            {"label":"(GMT+03:00) Tbilisi","value":"Asia/Tbilisi"},
                            {"label":"(GMT+03:30) Tehran","value":"Asia/Tehran"},
                            {"label":"(GMT+04:00) Abu Dhabi, Muscat","value":"Asia/Muscat"},
                            {"label":"(GMT+04:00) Baku","value":"Asia/Baku"},
                            {"label":"(GMT+04:00) Yerevan","value":"Asia/Yerevan"},
                            {"label":"(GMT+04:30) Kabul","value":"Asia/Kabul"},
                            {"label":"(GMT+05:00) Yekaterinburg","value":"Asia/Yekaterinburg"},
                            {"label":"(GMT+05:00) Islamabad, Karachi, Tashkent","value":"Asia/Karachi"},
                            {"label":"(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi","value":"Asia/Calcutta"},
                            {"label":"(GMT+05:30) Sri Jayawardenapura","value":"Asia/Calcutta"},
                            {"label":"(GMT+05:45) Kathmandu","value":"Asia/Katmandu"},
                            {"label":"(GMT+06:00) Almaty, Novosibirsk","value":"Asia/Almaty"},
                            {"label":"(GMT+06:00) Astana, Dhaka","value":"Asia/Dhaka"},
                            {"label":"(GMT+06:30) Yangon (Rangoon)","value":"Asia/Rangoon"},
                            {"label":"(GMT+07:00) Bangkok, Hanoi, Jakarta","value":"Asia/Bangkok"},
                            {"label":"(GMT+07:00) Krasnoyarsk","value":"Asia/Krasnoyarsk"},
                            {"label":"(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi","value":"Asia/Hong_Kong"},
                            {"label":"(GMT+08:00) Kuala Lumpur, Singapore","value":"Asia/Kuala_Lumpur"},
                            {"label":"(GMT+08:00) Irkutsk, Ulaan Bataar","value":"Asia/Irkutsk"},
                            {"label":"(GMT+08:00) Perth","value":"Australia/Perth"},
                            {"label":"(GMT+08:00) Taipei","value":"Asia/Taipei"},
                            {"label":"(GMT+09:00) Osaka, Sapporo, Tokyo","value":"Asia/Tokyo"},
                            {"label":"(GMT+09:00) Seoul","value":"Asia/Seoul"},
                            {"label":"(GMT+09:00) Yakutsk","value":"Asia/Yakutsk"},
                            {"label":"(GMT+09:30) Adelaide","value":"Australia/Adelaide"},
                            {"label":"(GMT+09:30) Darwin","value":"Australia/Darwin"},
                            {"label":"(GMT+10:00) Brisbane","value":"Australia/Brisbane"},
                            {"label":"(GMT+10:00) Canberra, Melbourne, Sydney","value":"Australia/Canberra"},
                            {"label":"(GMT+10:00) Hobart","value":"Australia/Hobart"},
                            {"label":"(GMT+10:00) Guam, Port Moresby","value":"Pacific/Guam"},
                            {"label":"(GMT+10:00) Vladivostok","value":"Asia/Vladivostok"},
                            {"label":"(GMT+11:00) Magadan, Solomon Is., New Caledonia","value":"Asia/Magadan"},
                            {"label":"(GMT+12:00) Auckland, Wellington","value":"Pacific/Auckland"},
                            {"label":"(GMT+12:00) Fiji, Kamchatka, Marshall Is.","value":"Pacific/Fiji"},
                            {"label":"(GMT+13:00) Nuku'alofa","value":"Pacific/Tongatapu"}
                        ];

                        const select = document.querySelector("#timezone");
                        if(select) {
                            select.innerHTML = "";
                            const selected = "<?php echoSetting('timezone'); ?>";
                            let selectedIndex = 0;
                            tzStrings.forEach(function(object, index){
                                const option = document.createElement("option");
                                option.innerHTML = object.label;
                                option.setAttribute("value", object.value);
                                select.appendChild(option);
                                if (object.value === selected) {
                                    selectedIndex = index;
                                }
                            });
                            select.selectedIndex = selectedIndex;

                        }

                    </script>
                </div>
            </div>
        </div>

    </div>
<?php footer(); ?>

</body></html>