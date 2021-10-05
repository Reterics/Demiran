<?php

require_once('../config.php');
require_once('./auth.php');

require_once('./backend/main.php');

if(isset($_POST['_call'])) {
    $task_name = stripslashes($_POST['_call']);
    if( strpos($task_name, '.') !== false || strpos($task_name, '/') !== false ){
        die("Special characters are not allowed in task name.");
    }
    global $Demiran;
    //Import only the needed Task to save resources
    if(file_exists(dirname(__FILE__).'/backend/lib/'.$task_name.'.php')) {
        require_once(dirname(__FILE__).'/backend/lib/'.$task_name.'.php');
    }

    $Demiran->call($task_name, $_POST, $_FILES);
}