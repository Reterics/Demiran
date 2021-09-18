<?php


$methods = array();

/**
 * @deprecated
 * @param {array} $object - Ideally thi sis
 * @return array
 */
function get_functions_for_obj($object) {
    global $methods;
    $selected = array();
    foreach ($methods as $method_details) {
        if (is_array($method_details['vars']) && count($method_details['vars']) > 0) {
            $match = true;
            foreach ($method_details['vars'] as $variable) {
                if (!isset($object[$variable])) {
                    $match = false;
                }
            }
            if ($match && isset($method_details['method'])) {
                array_push($selected, $method_details['method']);
            }
        }
    }
    return $selected;
}

/**
 * @deprecated
 * @param $object
 */
function run_methods_for_obj($object) {
    $method_list = get_functions_for_obj($object);
    foreach ($method_list as $method) {
        if(is_callable($method)) {
            $method($object);
        }
    }
}
/**
 * @deprecated
 * @param {array} $variables
 * @param {function} $method
 */
function add_backend_method($variables, $method) {
    global $methods;
    $method_object = array(
        "vars" => $variables,
        "method" => $method
    );
    array_push($methods, $method_object);
}

function handlePassword($pass){
    global $connection;
    $password = stripslashes($pass);
    $password = mysqli_real_escape_string($connection, $password);
    return md5($password);
}

class DemiranBackend {
    protected array $methods;

    /**
     * @param {string} $task_name
     * @param {function} $method
     * @return bool
     */
    function add_method($task_name, $method){
        if(is_callable($method) && isset($task_name) && $task_name != "" && !isset($this->methods[$task_name])){
            $this->methods[$task_name] = $method;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param {array} $method_list
     * @return bool
     */
    function methods($method_list) {
        $success = true;
        foreach ($method_list as $task_name=>$method) {
            if(!$this->add_method($task_name, $method)){
                $success = false;
            }
        }
        return $success;
    }

    function call($task_name, $argument) {
        if(isset($this->methods[$task_name]) && is_callable($this->methods[$task_name])) {
            $this->methods[$task_name]($argument);
        } else {
            echo "There is no function with name: ".$task_name;
        }
    }
}

$Demiran = new DemiranBackend();

$Demiran->add_method("manage_counter", function (){
    global $connection;
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
});
$Demiran->add_method("change_user_pass", function (){
    global $connection;
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
});

//add_backend_method(array("counter", "task", "role", "id"), );

add_backend_method(array("deleteuser"), function () {
    global $connection;
    $query = "DELETE from `users` WHERE id=".$_POST['deleteuser'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});

add_backend_method(array("username", "adduser"), function () {
    global $connection;
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
});

add_backend_method(array("addproject", "title"), function (){
    global $connection;
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
});

add_backend_method(array("deleteproject"), function (){
    global $connection;
    $query = "DELETE from `project` WHERE id=".$_POST['deleteproject'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});

add_backend_method(array("deleteproject_task"), function (){
    global $connection;
    $query = "DELETE from `project_tasks` WHERE id=".$_POST['deleteproject_task'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});

add_backend_method(array("addpage", "title"), function () {
    global $connection;
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
});

add_backend_method(array("deletepage"), function (){
    global $connection;
    $query = "DELETE from `pages` WHERE id=".$_POST['deletepage'];
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});

add_backend_method(array("main-settings", "id1", "id2"), function (){
    global $connection;
    $ips = $_POST["id1"];
    $geo = $_POST["id2"];
    $sql = "UPDATE `settings` SET message='".$ips."' WHERE id=1";
    mysqli_query($connection, $sql);
    $sql = "UPDATE `settings` SET message='".$geo."' WHERE id=2";
    mysqli_query($connection, $sql);
    echo "ok";
});

add_backend_method(array("updateworktime", "from", "to", "id"), function (){
    global $connection;
    $work_time = $_POST['from']."-".$_POST['to'];
    if (isset($connection)) {
        $sql = "UPDATE `users` SET work_time='".$work_time."' WHERE id=".$_POST['id'];
        mysqli_query($connection, $sql);
        echo "ok";
    } else {
        echo "fail";
    }
});

add_backend_method(array("addprojecttask", "title"), function (){
    global $connection;
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
});

