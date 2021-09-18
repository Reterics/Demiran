<?php
require_once('../config.php');
require_once("./auth.php");
require_once("./template.php");

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Users - Secured Page</title>
    <?php admin_head(); ?>
</head>
<body>


<?php
admin_header_menu();
require_once "process.php";

$currentUserId = $_SESSION['id'];
$currentUserName = $_SESSION["username"];
$currentEmail = "";
if (isset($_GET['id'])) {
    $currentUserId = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id=".$currentUserId.";";
    $currentUserName = "Ismeretlen";
    if (isset($connection) && $connection) {
        $result = mysqli_query($connection, $sql);
        $userData = mysqli_fetch_array($result);
        if (isset($userData['username'])) {
            $currentUserName = $userData['username'];
        }
        if (isset($userData['email'])) {
            $currentEmail = $userData['email'];
        }

    }
}

$openedUser = false;
if(isset($_GET['id']) && $currentUserName === $_SESSION["username"]) {
    $sql = "SELECT shift_list.id, shift_list.user,  users.username, users.email, shift_list.start_time, shift_list.end_time, shift_list.note, shift_list.task FROM shift_list INNER JOIN users ON shift_list.user=users.id WHERE shift_list.user=" . $currentUserId . ";";
    $openedUser = true;
} else if(isset($_GET['id']) && $currentUserName != $_SESSION["username"] && isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')){
    $sql = "SELECT shift_list.id, shift_list.user,  users.username, users.email, shift_list.start_time, shift_list.end_time, shift_list.note, shift_list.task FROM shift_list INNER JOIN users ON shift_list.user=users.id WHERE shift_list.user=" . $currentUserId . ";";
    $openedUser = true;
} else if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin') && !isset($_GET['id'])) {
    $sql = "SELECT shift_list.id, shift_list.user, users.username, users.email, shift_list.start_time, shift_list.end_time, shift_list.note, shift_list.task FROM shift_list
INNER JOIN users ON shift_list.user=users.id;";
} else {
    $sql = "SELECT shift_list.id, shift_list.user,  users.username, users.email, shift_list.start_time, shift_list.end_time, shift_list.note, shift_list.task FROM shift_list INNER JOIN users ON shift_list.user=users.id WHERE shift_list.user=".$currentUserId.";";
    $openedUser = true;
}

//$sql = "SELECT id,user,start_time,end_time,note,task FROM shift_list;";
$allData = array();

$now = strtotime("now");
$weekDate = date('d.m.Y',strtotime( 'monday this week' ));
$todayDate = date("d.m.Y");
$year = strftime(strftime("01.01.%Y", $now));
$yearDate = date(strftime("01.01.%Y"));


$lastWeek = strtotime($weekDate);
$lastMonth = strtotime('first day of this month');
$lastDay = strtotime($todayDate);
$lastYear = strtotime(date(strftime("01.01.%Y")));
$monthDate = date('d.m.Y',$lastMonth);

$allTime = 0;
$yearTime = 0;
$dayTime = 0;
$monthTime = 0;
$weekTime = 0;

if (isset($connection) && $connection) :
    $result = mysqli_query($connection, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $first  = new DateTime( $row['start_time'] );
        $second = new DateTime( $row['end_time'] );

        $row['diff'] = $first->diff( $second );
        $differenceSeconds = intval($row['diff']->format("%s"));
        $differenceMinutes = intval($row['diff']->format("%s"));
        $differenceHours = intval($row['diff']->format("%s"));
        $differenceInteger =
            $row['diff']->s +
            $row['diff']->i * 60 +
            $row['diff']->h * 60*60 +
            $row['diff']->d * 24*60*60 +
            $row['diff']->m * 24*60*60*30 +
            $row['diff']->y * 24*60*60*365;

        $workDay = strtotime($row['start_time']);

        if( $workDay >= $lastDay ) {
            $dayTime += $differenceInteger;
        }
        if ( $workDay >= $lastWeek ) {
            $weekTime += $differenceInteger;
        }
        if ( $workDay >= $lastMonth ) {
            $monthTime += $differenceInteger;
        }
        if ( $workDay >= $lastYear ) {
            $yearTime += $differenceInteger;
        }
        $allTime += $differenceInteger;
        array_push($allData, $row);
    }


endif;


?>
<div class="top_outer_div">
    <h2><?php
        if($openedUser){
            echo $currentUserName." óraszámai";
        }else{
            echo "Összes felhasználó óraszáma";
        }



        ?></h2>
    <div class="row">
        <div class="col-md-12" style="display: flex; flex-wrap: nowrap">
            <div class="status-box-4">
                <h5 class="title">Nap</h5>
                <h3 class="value"><?php echo floor($dayTime/3600); ?> óra</h3>
                <div class="value">Számítva innen: <?php echo $todayDate; ?></div>
            </div>
            <div class="status-box-4">
                <h5 class="title">Hét</h5>
                <h3 class="value"><?php echo floor($weekTime/3600); ?> óra</h3>
                <div class="value">Számítva innen: <?php echo $weekDate; ?></div>

            </div>
            <div class="status-box-4">
                <h5 class="title">Hónap</h5>
                <h3 class="value"><?php echo floor($monthTime/3600); ?> óra</h3>
                <div class="value">Számítva innen: <?php echo $monthDate; ?></div>

            </div>
            <div class="status-box-4">
                <h5 class="title">Év</h5>
                <h3 class="value"><?php echo floor($yearTime/3600); ?> óra</h3>
                <div class="value">Számítva innen: <?php echo $yearDate; ?></div>


            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Óraszámok</h5>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        <span class="plus-icon register"></span>
                    <?php endif; ?>
                </div>
                <style>

                </style>
                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">ID</div>
                        <div class="name">Név</div>
                        <div class="email long">E-mail</div>
                        <div class="role long">Kezdő idő</div>
                        <div class="job long">Vége idő</div>
                        <div class="job long">Munkaidő</div>
                        <div class="exp">Jegyzet</div>
                        <div class="level">Tevékenység típusa</div>
                    </div>



                    <?php
                    foreach ($allData as $row):
                            ?>

                            <div class="dragged">
                                <button class="dragButton"><span class="toggler-icon"></span></button>
                                <div class="id short"><?php echo $row['id'] ?></div>
                                <div class="name" data-id="<?php echo $row['user'] ?>"><?php echo $row['username'] ?></div>
                                <div class="email long"><?php echo $row['email'] ?></div>
                                <div class="long"><?php echo $row['start_time'] ?></div>
                                <div class="long"><?php echo $row['end_time'] ?></div>
                                <div class="long"><?php echo $row['diff']->format( '%H:%I:%S' ); // -> 00:25:25 ?></div>
                                <div class=""><?php echo $row['note'] ?></div>
                                <div class=""><?php
                                    if( $row['task'] == "work") {
                                        echo "Munkavégzés";
                                    } else {
                                        echo $row['task'];
                                    }
                                     ?></div>


                            </div>

                            <?php


                    endforeach;
                    ?>

                    <script>

                        //Demiran.applyDragNDrop(".drag-container", ".dragged");

                        document.querySelectorAll(".drag-container .dragged .name").forEach(function (div){
                            const dataId = div.getAttribute("data-id");
                            div.onclick = function (){
                                location.href = location.href.split("?")[0] + "?id=" + dataId;
                            };
                        })
                    </script>
                </div>



            </div>

        </div>
        <?php
        if ($openedUser):
            ?>

        <div class="col-sm-12" style="padding-top: 1em;">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Műveletek</h5>
                </div>
                <div class="body">
                    <div class="time-management" style=" display: flex;">
                        <div>Munkaidő jelenlegi állása </div>
                        <span class="user stop-icon-black inactive-icon"> </span>
                        <span class="user play-icon-black"> </span>
                        <div class="user-elapsed-time">...</div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function start(){
                const stopIcon = document.querySelector(".user.stop-icon-black");
                const startIcon = document.querySelector(".user.play-icon-black");
                const elapsed = document.querySelector(".user-elapsed-time");
                let userInterval = null;
                const startUserTimer = function(fromTime){
                    const date = new Date(fromTime).getTime();
                    clearInterval(userInterval);

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

                    userInterval = setInterval(function(){
                        const now = (new Date()).getTime();
                        const dt = Math.floor((now - date)/1000);

                        console.log("Hour:"+Math.floor(dt / 3600).toPrecision());
                        if(elapsed){
                            elapsed.innerHTML = secondsToHms(dt);
                        }

                    },1000);

                };

                if(stopIcon && startIcon){
                    const nowDateTime = new Date().toCurrentTimeString().slice(0, 19).replace('T', ' ');
                    startIcon.onclick = function(){

                        const working = !startIcon.classList.contains("inactive-icon");
                        home.log("Clicked Start, active: " + working);
                        if(working){
                            Demiran.call("manage_counter", 'task=work&counter=start&starttime='+encodeURIComponent(nowDateTime)+'&user=<?php echo $currentUserId; ?>', function (e, result) {
                                console.log(result);

                                stopIcon.classList.remove("inactive-icon");
                                startIcon.classList.add("inactive-icon");
                                if(result !== "00:00:00"){
                                    startUserTimer(result);
                                }
                            });
                        }
                    };
                    stopIcon.onclick = function(){
                        const working = !stopIcon.classList.contains("inactive-icon");
                        home.log("Clicked Stop, active: " + working);
                        if(working){
                            Demiran.call("manage_counter", 'task=work&counter=stop&starttime='+encodeURIComponent(nowDateTime)+'&user=<?php echo $currentUserId; ?>', function (e, result) {
                                console.log(result);
                                if(result === "ok"){
                                    clearInterval(userInterval);
                                }
                                stopIcon.classList.add("inactive-icon");
                                startIcon.classList.remove("inactive-icon");
                            });
                        }
                    };
                }

                if(elapsed){
                    Demiran.call("manage_counter", 'task=work&counter=get'+'&user=<?php echo $currentUserId; ?>', function (e, result) {
                        home.log("Munkaidő válasz a szervertől: " + result);

                        if (result !== "00:00:00") {
                            if (startIcon && stopIcon) {
                                stopIcon.classList.remove("inactive-icon");
                                startIcon.classList.add("inactive-icon");
                            }
                            startUserTimer(result);


                        }
                    })
                }

            }
            start();

        </script>



        <?php
        endif;
        ?>


    </div></div>
    <?php footer(); ?>
</body></html>