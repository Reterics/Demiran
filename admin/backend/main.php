<?php
/**
 * DEMIRAN Backend Core Class
 *
 * @author Attila Reterics
 * @license BSD-3-Clause
 * @copyright Attila Reterics
 * @date 18.09.2021
 * @contact reterics.attila@gmail.com
 * @version 1.0.0
 **/


class DemiranBackend {
    protected $methods;

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

    function get_permission_level(){
        $level = 0;

        if(isset($_SESSION['role'])) {
            switch($_SESSION['role']) {
                case "owner":
                    $level = 4;
                    break;
                case "admin":
                    $level = 3;
                    break;
                case "member":
                    $level = 2;
                    break;
                case "other":
                    $level = 1;
                    break;
                case "client":
                    $level = 1;
                    break;
            }
        }
        return $level;
    }

    function call($task_name, $arguments, $files = array()) {
        if(isset($this->methods[$task_name]) && is_callable($this->methods[$task_name])) {
            $filtered_arguments = array();
            $permission_level = $this->get_permission_level();
            $is_sensitive_method = strpos($task_name, "update_") === 0 || strpos($task_name, "delete_") === 0;
            if($permission_level < 3 && $is_sensitive_method) {
                echo "You don't have permission to execute this method.";
                return;
            }
            global $connection;
            if(isset($arguments) && is_array($arguments)) {
                foreach($arguments as $key => $value) {
                    if(strlen($key) < 30 && strpos($key, ".") === false) {
                        if(is_string($value)) {
                            $filtered_arguments[$key] = mysqli_real_escape_string($connection, stripslashes($value));
                        } else if(is_array($value)){
                            $filtered_arguments[$key] = array();
                            foreach($value as $v) {
                                if(is_numeric($v) || is_bool($v)){
                                    array_push($filtered_arguments[$key], mysqli_real_escape_string($connection, $v));
                                } else if(is_string($v)) {
                                    array_push($filtered_arguments[$key], mysqli_real_escape_string($connection, stripslashes($v)));
                                } //else {
                                    //Everything else is unsupported
                                //}
                            }
                        } else if(is_numeric($value) || is_bool($value)){
                            $filtered_arguments[$key] = mysqli_real_escape_string($connection, $value);
                        }
                    }
                }
            }
            $this->methods[$task_name]($filtered_arguments, $connection, $files);
        } else {
            echo "There is no function with name: ".$task_name;
        }
    }
}

$Demiran = new DemiranBackend();


$Demiran->add_method("manage_counter", function ($arguments, $connection){
    global $connection;
    //date_default_timezone_set('Europe/Budapest');
    $userId = $_SESSION['id'];
    if (isset($arguments['user']) && $arguments['user'] != "" && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')) {
        $userId = $arguments['user'];
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
    if(isset($arguments['starttime']) && $arguments['starttime'] !== "") {
        $mysql_date_now = $arguments['starttime'];
        //echo $mysql_date_now;
    } else {
        $mysql_date_now = date("Y-m-d H:i:s");
    }

    if($arguments['counter'] === "start"){
        if($unfinishedRow){
            $sql = "UPDATE `shift_list` SET end_time='".$mysql_date_now."' WHERE id=".$unfinishedRow['id']." AND user=".$userId;
            mysqli_query($connection, $sql);
        }
        $task = mysqli_real_escape_string($connection, $arguments['task']);
        $sql = "INSERT INTO `shift_list` (user,start_time,note,task) VALUES ('".$userId."','".$mysql_date_now."','','".$task."');";
        mysqli_query($connection, $sql);
        echo $mysql_date_now;
    } else if($arguments['counter'] === "stop"){
        if($unfinishedRow){

            $sql = "UPDATE `shift_list` SET end_time='".$mysql_date_now."' WHERE id=".$unfinishedRow['id']." AND user=".$userId;
            mysqli_query($connection, $sql);
        }
        echo "ok";
    } else if($arguments['counter'] === "get"){
        if($unfinishedRow){
            echo $unfinishedRow['start_time'];
        } else {
            echo "00:00:00";
        }
    }
});