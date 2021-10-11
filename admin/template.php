<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2020. 10. 13.
 * Time: 13:49
 */
global $globalSettings;
$timezone = $globalSettings->getSettingByName("timezone");
if($timezone){
    date_default_timezone_set($timezone||'Europe/Budapest');
} else {
    date_default_timezone_set('Europe/Budapest');
}

function getIPAddress() {
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getUserIDs($array) {
    $userIDs = array();
    foreach($array as $value) {
        $pieces = explode(",", $value['users']);
        foreach ($pieces as $userID) {
            if (is_numeric($userID) && !in_array($userID, $userIDs)) {
                array_push($userIDs, $userID);
            }
        }
        if(isset($value['client']) && $value['client'] != "" && !in_array($value['client'], $userIDs)) {
            array_push($userIDs, $value['client']);
        }
    }
    return $userIDs;
}

function getUsersByIDs($array){
    $sql = "SELECT * FROM users WHERE ID IN (";
    $i = 0;
    foreach($array as $userID) {
        if($i != 0){
            $sql.= ", ";
        }
        $sql.= $userID;
        $i = $i + 1;
    }
    $sql.= ");";
    //echo "<br>".$sql."<br>";
    return sqlGetAll($sql);
}

function getClientsAsOptions(){
    global $connection;
    $sql = "SELECT id,username,full_name FROM users WHERE role = 'client'";
    $result = mysqli_query($connection, $sql);
    $html = "";
    while ($row = mysqli_fetch_array($result)) {
        if(isset($row['full_name']) && $row['full_name'] != ""){
            $html .= "<option value='".$row['id']."'>".$row['full_name']."</option>";
        } else {
            $html .= "<option value='".$row['id']."'>".$row['username']."</option>";
        }
    }
    return $html;
}
function geUsersAsOptions(){
    global $connection;
    $sql = "SELECT id,username,full_name FROM users WHERE role != 'client'";
    $result = mysqli_query($connection, $sql);
    $html = "";
    while ($row = mysqli_fetch_array($result)) {
        if(isset($row['full_name']) && $row['full_name'] != ""){
            $html .= "<option value='".$row['id']."'>".$row['full_name']."</option>";
        } else {
            $html .= "<option value='".$row['id']."'>".$row['username']."</option>";
        }

    }
    return $html;
}
function geProjectsAsOptions($selected = null){
    global $connection;
    $sql = "SELECT id, title FROM project";
    $result = mysqli_query($connection, $sql);
    $html = "";
    if ($selected == null){
        $html .= "<option value=''>Válassz</option>";
    }
    while ($row = mysqli_fetch_array($result)) {
        if ($selected != null && $selected == $row['id']) {
            $html .= "<option value='".$row['id']."' selected>".$row['title']."</option>";
        } else {
            $html .= "<option value='".$row['id']."'>".$row['title']."</option>";
        }

    }
    return $html;
}

/**
 * @param $id
 * @param $array
 * @return array
 */
function selectUserFromArray($id, $array) {
    $selectedUser = array();

    $has_comma = strpos($id, ",");
    if($has_comma !== false) {
        $id = explode(",", $id);
    }

    foreach($array as $user) {
        if(is_array($id)) {
            foreach($id as $i) {
                if (isset($user["id"]) && $user["id"] == $i ){
                    if(!in_array($user, $selectedUser)) {
                        array_push($selectedUser, $user);
                    }
                }
            }
        } else {
            if (isset($user["id"]) && $user["id"] == $id ){
                array_push($selectedUser, $user);
            }
        }
    }
    return $selectedUser;
}

function stringToColorCode($str) {
    $code = dechex(crc32($str));
    $code = substr($code, 0, 6);
    return $code;
}


function setUserIconSpan($userData) {
    if(!isset($userData) || $userData == null || $userData == "") {
        return;
    }

    if(is_array($userData)) :
        foreach($userData as $user):
            ?>
            <span class="userSpan" title="<?php echo $user["username"]; ?>" style="background-color:<?php echo "#D1".stringToColorCode($user["username"]); ?>">
                <?php echo strtoupper(substr($user["username"], 0,1)) . substr($user["username"], 1,1); ?>
            </span>
            <?php
        endforeach;
    else:
        ?>
        <span class="userSpan" title="<?php echo $userData["username"]; ?>" style="background-color:<?php echo "#D1".stringToColorCode($userData["username"]); ?>">
            <?php echo strtoupper(substr($userData["username"], 0,1)) . substr($userData["username"], 1,1); ?>
        </span>
        <?php
    endif;
    return;
}

function load_tiny_mce(){
    ?>

    <!-- <link href="./css/jquery-ui.min.css" rel="stylesheet">-->
    <script src="./js/jquery.min.js" rel="script" ></script>
    <script src="./js/tinymce/jquery.tinymce.min.js"></script>
    <script src="./js/tinymce/tinymce.min.js"></script>

    <!--  <script src="./js/bootstrap.js" rel="script" ></script>-->
    <!--<script src="./js/jquery-ui.min.js" rel="script" ></script>-->
    <?php
}

function load_scripts($scriptList){
    echo "<script type='application/javascript'>";
    if(is_array($scriptList)){
        foreach($scriptList as $script){
            if(file_exists($script)) {
                echo "\n".file_get_contents($script)."\n";
            }
        }
    }
    echo "</script>";
}

function admin_head($title){
    favicon_meta();
    meta_tags($title);
    echo "<style type='text/css'>".
        file_get_contents("./css/bootstrap.min.css").
        "\n".
        file_get_contents("./css/product.css").
        "</style>";
    ?>
    <!-- <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/product.css" rel="stylesheet"> -->
    <?php
    if(isset($_GET['theme'])){
        echo "<link href=\"./css/theme-".$_GET['theme'].".css\" rel=\"stylesheet\">";
    }
    ?>

    <!-- <script src="./js/demiran.js"></script> -->
    <script type="application/javascript">
        <?php echo file_get_contents("./js/demiran.js"); ?>
        const navigate = function (url) {
            location.href = url;
        };

        const handleFiles = function (files,output, area) {
            const file = files[0];
            if (!file.type) {
                //dropArea.innerHTML = 'Error: The File.type property does not appear to be supported on this browser.';
                return;
            }
            if (!file.type.match('image.*')) {
                //dropArea.innerHTML = 'Error: The selected file does not appear to be an image.';
                return;
            }
            const reader = new FileReader();
            reader.addEventListener('load', event => {
                if(area){
                    area.style.backgroundImage = "url('"+event.target.result+"')";
                }
                output.setAttribute("value", event.target.result)
            });
            reader.readAsDataURL(file);

        };

        const setUserDetails = function (userIDs) {
            const ids = Object.keys(userIDs);


            document.querySelectorAll(".dragged .users, td.users").forEach(node=>{
                if (node){

                    const users = node.innerHTML.split(",");
                    node.innerHTML = "";
                    users.forEach(function (user) {
                        if(ids.includes(user)){
                            const username = userIDs[user];
                            const span = document.createElement("span");
                            span.classList.add("userSpan");
                            span.setAttribute("title",username);
                            span.innerHTML = username.charAt(0).toUpperCase() + username.charAt(1);
                            //console.log(Demiran.getStringColor(username));
                            span.style.backgroundColor = Demiran.getStringColor(username);
                            node.appendChild(span);
                        }
                    })
                }
            });

            document.querySelectorAll(".dragged .client, td.client").forEach(node=>{
                if(node){
                    const client = node.innerHTML.trim();
                    if(userIDs[client]) {
                        node.innerHTML = userIDs[client]
                    }

                }
            });
        }


    </script>

<?php

}

function admin_header_menu(){
    ?>

<header class="main">
    <nav class="navbar navbar-expand-lg navbar-dark"><a
                class="navbar-brand" href="#">
            <?php
                echo file_get_contents("./img/logo.svg");
            ?>
            <!-- <img class="d-inline-block align-top" height="30" src="./img/logo.svg" style="height: 35px; width: 100%;" alt="demiran Logo"> -->
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#headerMenu"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigatiopn"><span
                    class="navbar-toggler-icon"></span></button>


        <div id="headerMenu" class="collapse navbar-collapse">
            <div class="ml-auto navbar-nav clock-bar" style="font-size: 120%;">
                <div class="elapsed-time">...</div>
                <span class="stop-icon inactive-icon"> </span>
                <span class="pause-icon inactive-icon" style="display: none"> </span>
                <span class="play-icon"> </span>
                <?php

                //echo $_SESSION['role'];

            ?></div>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link index" href="index.php">Kezdőlap</a></li>
                <li class="nav-item"><a class="nav-link tasks" href="tasks.php">Feladatok</a></li>
                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                    <li class="nav-item"><a class="nav-link users" href="users.php">Felhasználók</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link projects" href="projects.php">Projektek</a></li>
                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                    <li class="nav-item"><a class="nav-link pages" href="pages.php">Oldalak</a></li>
                <?php endif; ?>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] != 'client'): ?>
                    <li class="nav-item"><a class="nav-link hours" href="hours.php">Óraszámok</a></li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="users.php?id=<?php echo $_SESSION['id'];?>">Profilom</a>
                        <a class="dropdown-item" href="messages.php">Üzenetek</a>
                        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                            <a class="dropdown-item" href="settings.php">Beállítások</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Kijelentkezés</a>
                    </div>
                </li>



            </ul>

        </div>
    </nav>
</header>

<script>

    Date.prototype.toCurrentTimeString = function() {
        var tzo = -this.getTimezoneOffset(),
            dif = tzo >= 0 ? '+' : '-',
            pad = function(num) {
                var norm = Math.floor(Math.abs(num));
                return (norm < 10 ? '0' : '') + norm;
            };
        return this.getFullYear() +
            '-' + pad(this.getMonth() + 1) +
            '-' + pad(this.getDate()) +
            'T' + pad(this.getHours()) +
            ':' + pad(this.getMinutes()) +
            ':' + pad(this.getSeconds()) +
            dif + pad(tzo / 60) +
            ':' + pad(tzo % 60);
    };

    function activateNavigationDropdown() {
        document.querySelectorAll("li.nav-item.dropdown").forEach(function(node){
            const subNode = node.querySelector("div.dropdown-menu");
            if(subNode){
                node.onclick = function(){
                    if(node.classList.contains("show")){
                        node.classList.remove("show");
                        subNode.classList.remove("show");
                    } else {
                        node.classList.add("show");
                        subNode.classList.add("show");
                    }
                }
            }
        })
    }
    activateNavigationDropdown();

    let con;

    function homeLog(data) {
        con = document.querySelector(".console");
        if(con){
            if(Array.isArray(data)){
                data.forEach(function (d){
                    con.innerHTML += d + ", ";
                });
            } else if(typeof data === "object") {
                con.innerHTML += JSON.stringify(data);
            } else {
                con.innerHTML += data;
            }
            con.innerHTML += "<br>";
        } else {
            console.log(data);
        }
    }

    const headerButton = document.querySelector("button.navbar-toggler");
    if (headerButton) {
        headerButton.onclick = function () {
            const a = document.querySelector(".navbar-expand-lg .navbar-collapse");
            const b = document.querySelector(".navbar-expand-lg .navbar-collapse .navbar-nav.ml-auto");
            if(a && b) {
                if (a.style.display === "block") {
                    a.style.display= "none";
                    b.style.display= "none";
                } else {
                    a.style.display= "block";
                    b.style.display= "block";
                }

            }
        };
    }
    const home = {log:homeLog};
    const selected = location.pathname.split("/").pop().split(".")[0];

    const node = document.querySelector("#headerMenu li a."+selected);
    if(selected && node){
        document.querySelectorAll("#headerMenu li").forEach(function (node) {
            node.classList.remove("active");
        });
        node.parentElement.classList.add("active");
    }

    const stopIcon = document.querySelector(".stop-icon");
    const startIcon = document.querySelector(".play-icon");
    //const pauseIcon = document.querySelector(".pauseIcon");
    const elapsed = document.querySelector(".elapsed-time");

    let interval = null;
    const startTimer = function(fromTime){
        const date = new Date(fromTime).getTime();
        console.log(new Date(fromTime));
        console.log(new Date());
        clearInterval(interval);

        function secondsToHms(d) {
            d = Number(d);
            const h = Math.floor(d / 3600);
            const m = Math.floor(d % 3600 / 60);
            const s = Math.floor(d % 3600 % 60);

            const hDisplay = h > 0 ? h + ":"  : "00:";
            const mDisplay = m > 0 ? m + ":"  : "00:";
            const sDisplay = s > 0 ? s + ""  : "00";
            return hDisplay + mDisplay + sDisplay;
        }

        interval = setInterval(function(){
            const now = (new Date()).getTime();
            const dt = Math.floor((now - date)/1000);

            console.log("Hour:"+Math.floor(dt / 3600).toPrecision());
            if(elapsed){
                elapsed.innerHTML = secondsToHms(dt);
            }


        },1000);

    };

    if(stopIcon && startIcon/* && pauseIcon*/){
        //Start counter backend
        const nowDateTime = new Date().toCurrentTimeString().slice(0, 19).replace('T', ' ');
        startIcon.onclick = function(){

            const working = !startIcon.classList.contains("inactive-icon");
            home.log("Clicked Start, active: " + working);
            if(working){
                Demiran.call("manage_counter", 'task=work&counter=start&starttime='+encodeURIComponent(nowDateTime), function (e, result) {
                    console.log(result);

                    stopIcon.classList.remove("inactive-icon");
                    startIcon.classList.add("inactive-icon");
                    if(result !== "00:00:00"){
                        startTimer(result);
                    }
                });
            }
        };
        stopIcon.onclick = function(){
            const working = !stopIcon.classList.contains("inactive-icon");
            home.log("Clicked Stop, active: " + working);
            if(working){
                Demiran.call("manage_counter", 'task=work&counter=stop&starttime='+encodeURIComponent(nowDateTime), function (e, result) {
                    console.log(result);
                    if(result === "ok"){
                        clearInterval(interval);
                    }
                    stopIcon.classList.add("inactive-icon");
                    startIcon.classList.remove("inactive-icon");
                });
            }
        };
    }

    if(elapsed){
        <?php global $Demiran;

        echo "let result = \"";
        $Demiran->call("manage_counter",array("counter"=>"get", "task"=>"work"));
        echo "\"\n";
        ?>
        //Demiran.call("manage_counter", 'task=work&counter=get', function (e, result) {
            home.log("Munkaidő válasz a szervertől: " + result);
            const elapsed = document.querySelector(".elapsed-time");
            if(elapsed){
                //elapsed.innerHTML = result;
            }
            if(result !== "00:00:00"){
                <?php
                $work_end = false;
                if(isset($_SESSION['work_time']) && $_SESSION['work_time'] != ""){
                    $times = explode("-", $_SESSION['work_time']);
                    if(count($times) > 1){
                        $time = time();
                        $end = $times[1];
                        $currentTime = date("H:i", $time);

                        $time_parts = explode(":", $currentTime);
                        $end_parts = explode(":", $end);
                        if (count($end_parts) > 1 && count($time_parts) > 1) {

                            $current = intval($time_parts[0]) * 60 + intval($time_parts[1]);
                            $end = intval($end_parts[0]) * 60 + intval($end_parts[1]);

                            if ($current >= $end) {
                                $work_end = true;
                            }
                        }

                        echo "home.log('".$currentTime."');";
                        echo "home.log('".$_SESSION['work_time']."');";
                    }
                }

                if($work_end):
                ?>
                if(startIcon && stopIcon){
                    stopIcon.classList.remove("inactive-icon");
                    startIcon.classList.add("inactive-icon");
                }
                stopIcon.click();
                <?php else:
                ?>
                if(startIcon && stopIcon){
                    stopIcon.classList.remove("inactive-icon");
                    startIcon.classList.add("inactive-icon");
                }
                startTimer(result);
                <?php
                endif; ?>

            } else {
                <?php
                $geoSettings = sqlGetFirst("SELECT * FROM settings WHERE setting_name='geo_data';");
                $ipSettings = sqlGetFirst("SELECT * FROM settings WHERE setting_name='ip_stack';");


                if($ipSettings && $geoSettings):


                        ?>

                const ip = "<?php echo getIPAddress(); ?>";
                const ipSettings = "<?php echo $ipSettings["message"]; ?>";
                const geoSettings = "<?php echo $geoSettings["message"]; ?>";

                function IPtoNum(ip){
                    return Number(
                        ip.split(".")
                            .map(d => ("000"+d).substr(-3) )
                            .join("")
                    );
                }

                const ipList = ipSettings.split(";");
                let found = false;
                if(ipList.includes(ip)){
                    home.log("A te IP címed engedélyezve van");
                    found = true;
                } else {
                    ipList.forEach(function (ipOrRange){
                        if(ipOrRange && ipOrRange.includes("-")){
                            const ranges = ipOrRange.split("-");
                            if( IPtoNum(ranges[0]) < IPtoNum(ip) && IPtoNum(ranges[1]) > IPtoNum(ip) ) {
                                home.log("A te IP Blokkod engedélyezve van");
                                found = true;
                            }
                        }

                    });

                    <?php
                    $parts = array();
                    if(isset($_SESSION["geo_string"]) && $_SESSION["geo_string"] != ""){
                        $parts = explode(",", $_SESSION["geo_string"]);
                    }
                    $size = count($parts);
                    if($size > 2):
                    ?>
                    const lat = Number.parseFloat("<?php echo $parts[0]; ?>");
                    const lon = Number.parseFloat("<?php echo $parts[1]; ?>");
                    //const meter = Number.parseFloat("<?php echo $parts[2]; ?>");
                    home.log("Session Koordináták: " + lat + ", " +lon);
                    if(!found){
                        const geoList = geoSettings.split(";");

                        geoList.forEach(function (geo){
                            if(!found) {
                                const parts = geo.split(",");
                                if(parts.length > 2){
                                    const geoLat = parts[0];
                                    const geoLon = parts[1];
                                    const geoMeter = parts[2];
                                    const distance = getDistanceFromLatLonInM(lat,lon,geoLat,geoLon);

                                    if(geoMeter >= distance){
                                        home.log("A Te Földrajzi helyed engedélyezve van, mert "+distance+" méteren belül vagy és a limit: "+geoMeter);
                                        found = true;
                                    }
                                }
                            }

                        });
                    }

                    <?php  endif; ?>

                }
                if(!found){
                    const topBox = document.querySelector(".clock-bar");
                    if(topBox){
                        topBox.style.display = "none";
                    }
                } else if(startIcon){
                    <?php   if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in'] === true) {
                    $_SESSION['just_logged_in'] = false;
                    if(isset($_SESSION['work_time']) && $_SESSION['work_time'] !== "" && $_SESSION['work_time'] !== "-") {
                        $times = explode("-", $_SESSION['work_time']);
                        $time = time();


                        if(count($times) > 1) {
                            $start = $times[0];
                            $end = $times[1];
                            $currentTime = date("H:i", $time);

                            $start_parts = explode(":", $start);
                            $end_parts = explode(":", $end);
                            $time_parts = explode(":", $currentTime);

                            if (count($start_parts) > 1 && count($end_parts) > 1 && count($time_parts) > 1) {

                                $start = intval($start_parts[0]) * 60 + intval($start_parts[1]);
                                $current = intval($time_parts[0]) * 60 + intval($time_parts[1]);
                                $end = intval($end_parts[0]) * 60 + intval($end_parts[1]);


                                if($start <= $current && $current <= $end) {
                                    echo "startIcon.click();";
                                } else {
                                    echo "home.log('".$start."-".$current."-".$end."')";
                                }
                            } else {
                                echo "home.log('".$start."-".$currentTime."-".$end."')";
                            }
                        } else {
                            echo "home.log('Munkaidő: '".$_SESSION['work_time'].");";
                        }

                    } else {
                        echo "home.log('Hozzád nincs munkaidő beállítva, így a számláló nem tud elindulni!');";
                    }

                } else {
                    echo "home.log('Munkaidő ellenőrzés manuális, mert nem most jelentkeztél be.');";
                    if(isset($_SESSION['work_time']) && $_SESSION['work_time'] !== "" && $_SESSION['work_time'] !== "-") {
                        echo "home.log('A te alapértelmezett munkaidőd: ".$_SESSION['work_time']."');";
                    } else {
                        echo "home.log('Hozzád nincs rendelve munkaidő!');";
                    }

                } ?>

                }
                //home.log(ip +","+ ipSettings+","+ geoSettings);
                <?php

                endif;
                ?>
            }


            //elapsed.innerHTML = result;
        //});
    }
</script>

<?php
}

function add_task_form($project_id = null){
    ?>
    <div class="addTaskDiv" style="display: none">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">

                <label for="title">Cím
                    <input type="text" class="form-control" name="title" id="title" placeholder="Cím" required/>
                </label>
                <label for="project_id">Projekt</label>
                <!--  <input type="hidden" name="project_id" id="project_id" value="<?php echo $project_id ?>"> -->
                <select class="form-control" name="project_id" id="project_id" >
                    <?php echo geProjectsAsOptions($project_id); ?>
                </select>
                <label for="users">Hozzárendelt felhasználók</label>

                <select class="form-control" name="users[]" id="users" multiple>
                    <?php echo geUsersAsOptions(); ?>
                </select>


                <label for="repeat">Ismétlés
                    <select class="form-control" name="repeat" id="repeat" >
                        <option value="once">Egyszeri</option>
                        <option value="frequently">Rendszeres</option>
                    </select></label>

                <label for="priority">Prioritás
                    <select class="form-control" name="priority" id="priority" >
                        <option value="low">Alacsony</option>
                        <option value="medium">Közepes</option>
                        <option value="high">Magas</option>
                    </select></label>

                <label for="state">Státusz
                    <select class="form-control" name="state" id="state" >
                        <option value="open">Nyitott</option>
                        <option value="in_progress">Folyamatban</option>
                        <option value="review">Átnézésre vár</option>
                        <option value="closed">Lezárt</option>
                    </select></label>

                <label>Kezdés
                    <input type="date" class="form-control" name="start_time" value="<?php echo date("Y-m-d"); ?>" min="2020-10-01" max="2030-12-31">
                </label>
                <label>Határidő
                    <input type="date" class="form-control" name="deadline" value="<?php echo date("Y-m-d",strtotime('first day of +1 month')); ?>" min="2020-10-01" max="2030-12-31">
                </label>
                <input type="text" class="form-control" name="addprojecttask" style="display:none;" value="1"/>

            </div>

        </form>

    </div>
    <script type="application/javascript">
        const addTaskButton = document.querySelector(".addTask");
        if(addTaskButton){
            addTaskButton.onclick = function () {
                const form = document.querySelector(".addTaskDiv form");

                if(form){
                    const cln = form.cloneNode(true);

                    const popup = Demiran.openPopUp("Hozzáadás", cln, [
                        {
                            value:"Hozzáad",
                            onclick: (closeDialog, modalID)=>{
                                const modal = document.querySelector("#"+modalID);
                                if(modal){
                                    const form = modal.querySelector("form");
                                    if(form){
                                        const title = form.querySelector("#title");
                                        if(title && !title.value){
                                            Demiran.alert("Kérlek adj meg egy címet!");
                                            return;
                                        }
                                        const project_id = form.querySelector("#project_id");
                                        if(project_id && !project_id.value){
                                            Demiran.alert("Kérlek válassz ki egy projektet!");
                                            return;
                                        }
                                        //form.submit();
                                        closeDialog();
                                        Demiran.call("add_project_task",Demiran.convertToFormEncoded(form),function(error,result){
                                            if(!error && result.trim() === "OK"){
                                                location.reload();
                                            } else {
                                                Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                console.log(result,error);
                                            }
                                        });
                                        return;
                                    }
                                }
                                Demiran.alert("Kritikus hiba a DOM-ban!");
                            }
                        },
                        {
                            value:"Vissza",
                            type:"close"
                        }
                    ]);
                }
            }
        }

    </script>
    <?php
}

function edit_task_form($project_id = null){
    ?>
    <div id="editTaskDivOuter" style="display: none">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Cím
                    <input type="text" class="form-control" name="title" placeholder="Cím" required/>
                </label>
                <label for="task-project_id">Projekt</label>
                <select class="form-control" name="project_id" id="task-project_id" >
                    <?php echo geProjectsAsOptions($project_id); ?>
                </select>
                <label for="task-users">Hozzárendelt felhasználók</label>

                <select class="form-control" name="users[]" id="task-users" multiple>
                    <?php echo geUsersAsOptions(); ?>
                </select>


                <label>Ismétlés
                    <select class="form-control" name="repeat" >
                        <option value="once">Egyszeri</option>
                        <option value="frequently">Rendszeres</option>
                    </select></label>

                <label>Prioritás
                    <select class="form-control" name="priority" >
                        <option value="low">Alacsony</option>
                        <option value="medium">Közepes</option>
                        <option value="high">Magas</option>
                    </select></label>

                <label>Státusz
                    <select class="form-control" name="state" >
                        <option value="open">Nyitott</option>
                        <option value="in_progress">Folyamatban</option>
                        <option value="review">Átnézésre vár</option>
                        <option value="closed">Lezárt</option>
                    </select></label>

                <label>Kezdés
                    <input type="date" class="form-control" name="start_time" value="<?php echo date("Y-m-d"); ?>" min="2020-10-01" max="2030-12-31">
                </label>
                <label>Határidő
                    <input type="date" class="form-control" name="deadline" value="<?php echo date("Y-m-d",strtotime('first day of +1 month')); ?>" min="2020-10-01" max="2030-12-31">
                </label>
            </div>

        </form>

    </div>

    <?php
}