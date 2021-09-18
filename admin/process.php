<?php

require_once('../config.php');
require_once("auth.php");

global $connection;
function handlePassword($pass){
    global $connection;
    $password = stripslashes($pass);
    $password = mysqli_real_escape_string($connection, $password);
    return md5($password);
}

if(isset($_POST['deleteuser'])){
    $query = "DELETE from `users` WHERE id=".$_POST['deleteuser'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if (isset($_POST['username']) && isset($_POST['adduser'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($connection, $username);
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($connection, $email);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($connection, $password);
    $trn_date = date("Y-m-d H:i:s");

    $role = "member";
    if (isset($_POST['role'])) {
        $role = $_POST['role'];
    }
    $image = "";
    if (isset($_POST['image'])) {
        $image = $_POST['image'];
    }
    $job = "";
    if (isset($_POST['job'])) {
        $job = $_POST['job'];
    }
    $work_time = "";
    if (isset($_POST['work_time'])) {
        $work_time = $_POST['work_time'];
    }

    if (isset($_FILES['image'])) {
        $time = date("Ymdhis");
        $image = $time . basename($_FILES['image']['name']);

        $target = "./uploads/" . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $msg = "Image uploaded successfully";
        } else {
            $msg = "Failed to upload image";
        }
    }

    $query = "INSERT into `users` (username, password, email, trn_date, role, image, job, details, exp, work_time, level)
VALUES ('$username', '" . md5($password) . "', '$email', '$trn_date', '$role', '$image', '$job', '', '0', '$work_time' ,'1')";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST['addproject']) && isset($_POST['title'])) {

    $users = "";
    $title = "";
    $category = "";
    $client = "";
    $billing = "";
    $price = "";
    if(isset($_POST['users'])){

        $i = 0;
        foreach ($_POST['users'] as $user) {
            if($i != 0){
                $users .= ",";
            }
            $users .= mysqli_real_escape_string($connection,$user);
            $i = $i + 1;
        }

    }
    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }
    if(isset($_POST['category'])){
        $category = $_POST['category'];
    }
    if(isset($_POST['client'])){
        $client = $_POST['client'];
    }
    if(isset($_POST['billing'])){
        $billing = $_POST['billing'];
    }
    if(isset($_POST['price'])){
        $price = $_POST['price'];
    }
    $trn_date = date("Y-m-d H:i:s");

    $start_date = $trn_date;
    if(isset($_POST['start_time'])){
        $start_date = $_POST['start_time'];
    }
    $end_date = $trn_date;
    if(isset($_POST['deadline'])){
        $end_date = $_POST['deadline'];
    }
    $query = "INSERT into `project` (users, title, category, client, status, billing, price, details, created, start_time, deadline, `order`)
VALUES ('$users', '$title', '$category', '$client', 'open', '$billing', '$price', '', '$trn_date', '$start_date', '$end_date', '1')";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST['deleteproject'])){
    $query = "DELETE from `project` WHERE id=".$_POST['deleteproject'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST['deleteproject_task'])){
    $query = "DELETE from `project_tasks` WHERE id=".$_POST['deleteproject_task'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST['addpage']) && isset($_POST['title'])) {
    $user = "";
    if(isset($_POST['user'])){
        $user = $_POST['user'];
    }
    $title = "";
    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }
    $categories = "";
    if(isset($_POST['categories'])){
        $categories = $_POST['categories'];
    }
    $tags = "";
    if(isset($_POST['tags'])){
        $tags = $_POST['tags'];
    }
    $created = date("Y-m-d H:i:s");
    $modified = $created;

    $query = "INSERT into `pages` (user, title, categories, tags, image, details, created, modified) VALUES ('$user','$title','$categories','$tags','','','$created','$modified');";

    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST['deletepage'])){
    $query = "DELETE from `pages` WHERE id=".$_POST['deletepage'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST['counter']) && isset($_POST['task']) && isset($_SESSION['role']) && isset($_SESSION['id'])){
    //date_default_timezone_set('Europe/Budapest');
    $userId = $_SESSION['id'];
    if (isset($_POST['user']) && $_POST['user'] != "" && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')) {
        $userId = $_POST['user'];
    }
    function getUnfinishedJobForUser($userId) {
        global $connection;
        if(!isset($connection) || !$connection) {
            return null;
        }
        $sql = "SELECT * FROM `shift_list` WHERE start_time IS NOT NULL AND end_time IS NULL AND `user`=".$userId;
        $unfinished = mysqli_query($connection, $sql);
        $unfinishedRow = null;
        if ($unfinished) {
            $unfinishedRow = mysqli_fetch_array($unfinished);
        }
        return $unfinishedRow;
    }

    function stopUnfinishedJob($userId, $jobId, $now) {
        if(!isset($now) || !$now) {
            $now = date("Y-m-d H:i:s");
        }
        global $connection;
        if(!isset($connection) || !$connection) {
            return "fail";
        }
        $sql = "UPDATE `shift_list` SET end_time='".$now."' WHERE id=".$jobId." AND user=".$userId;
        mysqli_query($connection, $sql);
        return "ok";
    }

    $sql = "SELECT * FROM `shift_list` WHERE start_time IS NOT NULL AND end_time IS NULL AND `user`=".$userId;
    $unfinished = mysqli_query($connection, $sql);
    $unfinishedRow = null;
    if ($unfinished) {
        $unfinishedRow = mysqli_fetch_array($unfinished);
    }
    if(isset($_POST['starttime']) && $_POST['starttime'] !== "") {
        $mysql_date_now = $_POST['starttime'];
        //echo $mysql_date_now;
    } else {
        $mysql_date_now = date("Y-m-d H:i:s");
    }

    if($_POST['counter'] === "start"){
        if($unfinishedRow){
            $sql = "UPDATE `shift_list` SET end_time='".$mysql_date_now."' WHERE id=".$unfinishedRow['id']." AND user=".$userId;
            mysqli_query($connection, $sql);
        }
        $task = mysqli_real_escape_string($connection, $_POST['task']);
        $sql = "INSERT INTO `shift_list` (user,start_time,note,task) VALUES ('".$userId."','".$mysql_date_now."','','".$task."');";
        mysqli_query($connection, $sql);
        echo $mysql_date_now;
    } else if($_POST['counter'] === "stop"){
        if($unfinishedRow){

            $sql = "UPDATE `shift_list` SET end_time='".$mysql_date_now."' WHERE id=".$unfinishedRow['id']." AND user=".$userId;
            mysqli_query($connection, $sql);
        }
        echo "ok";
    } else if($_POST['counter'] === "get"){
        if($unfinishedRow){
            echo $unfinishedRow['start_time'];
        } else {
            echo "00:00:00";
        }
    }


} else if(isset($_POST["main-settings"]) && isset($connection)){
    $ips = $_POST["id1"];
    $geo = $_POST["id2"];
    $sql = "UPDATE `settings` SET message='".$ips."' WHERE id=1";
    mysqli_query($connection, $sql);
    $sql = "UPDATE `settings` SET message='".$geo."' WHERE id=2";
    mysqli_query($connection, $sql);
    echo "ok";
} else if(isset($_POST['updateworktime']) && isset($_POST['from']) && isset($_POST['to']) && isset($_POST['id'])) {
    $work_time = $_POST['from']."-".$_POST['to'];
    if (isset($connection)) {
        $sql = "UPDATE `users` SET work_time='".$work_time."' WHERE id=".$_POST['id'];
        mysqli_query($connection, $sql);
        echo "ok";
    } else {
        echo "fail";
    }

} else if(isset($_POST['addprojecttask']) && isset($_POST['title'])) {
    $title = "";
    $users = "";
    $repeat = "";
    $priority = "";
    $project_id = "";
    $state = "open";
    if(isset($_POST['users'])){
        $i = 0;
        foreach ($_POST['users'] as $user) {
            if($i != 0){
                $users .= ",";
            }
            $users .= mysqli_real_escape_string($connection,$user);
            $i = $i + 1;
        }
    }
    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }
    if(isset($_POST['repeat'])){
        $repeat = $_POST['repeat'];
    }
    if(isset($_POST['priority'])){
        $priority = $_POST['priority'];
    }
    if(isset($_POST['state'])){
        $state = $_POST['state'];
    }

    if(isset($_POST['project_id'])){
        $project_id = $_POST['project_id'];
    }

    $trn_date = date("Y-m-d H:i:s");

    $start_date = $trn_date;
    if(isset($_POST['start_time'])){
        $start_date = $_POST['start_time'];
    }

    $end_date = $trn_date;
    if(isset($_POST['deadline'])){
        $end_date = $_POST['deadline'];
    }
    $query = "INSERT into `project_tasks` (users, title, project, visibility, `repeat`, image, details, attachments, state, priority, start_time, deadline, `order`)
VALUES ('$users', '$title', '$project_id', 'all', '$repeat', '', '', '', '$state', '$priority', '$start_date', '$end_date', '1')";
    $result = mysqli_query($connection, $query);
    echo "$query";
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
} else if(isset($_POST["change_user_pass"])) {
    // Security Vulnerability, because i handle passwords as plain text TODO: fix it
    $old_pass = handlePassword($_POST["change_user_pass"]);
    $new_pass = handlePassword($_POST["new_pass"]);
    $new_pass_again = handlePassword($_POST["new_pass_again"]);
    $username = $_SESSION["username"];
    if($new_pass != $new_pass_again) {
        echo "A jelszavak nem egyeznek";
    }

    $query = "SELECT * FROM `users` WHERE username='$username' and password='".$old_pass."'";
    $result = mysqli_query($connection,$query) or die("Kapcsolódási hiba az adatbázissal");
    $rows = mysqli_num_rows($result);
    if($rows==1){
        $query = "UPDATE `users` SET password='".$new_pass."' WHERE username='$username' and password='".$old_pass."'";
        $result = mysqli_query($connection, $query) or die("Kapcsolódási hiba az adatbázissal");
        if($result){
            echo "OK";
        } else {
            echo "Jelszó módosítása sikertelen, kérlek vedd fel a kapcsolatot az adminisztrátorral";
        }
    } else {
        echo "A jelenlegi jelszó nem egyezik, bejelentkezés sikertelen!";
    }

}