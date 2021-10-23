<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:35
 */

$Demiran->add_method("add_project_task", function ($arguments, $connection){
    $title = "";
    $users = "";
    $repeat = "";
    $priority = "";
    $project_id = "";
    $state = "open";
    if(isset($arguments['users'])){
        $i = 0;
        foreach ($arguments['users'] as $user) {
            if($i != 0){
                $users .= ",";
            }
            $users .= mysqli_real_escape_string($connection,$user);
            $i = $i + 1;
        }
    }
    if(isset($arguments['title'])){
        $title = $arguments['title'];
    }
    if(isset($arguments['repeat'])){
        $repeat = $arguments['repeat'];
    }
    if(isset($arguments['priority'])){
        $priority = $arguments['priority'];
    }
    if(isset($arguments['state'])){
        $state = $arguments['state'];
    }

    if(isset($arguments['project_id'])){
        $project_id = $arguments['project_id'];
    }

    $trn_date = date("Y-m-d H:i:s");

    $start_date = $trn_date;
    if(isset($arguments['start_time'])){
        $start_date = $_POST['start_time'];
    }

    $end_date = $trn_date;
    if(isset($arguments['deadline'])){
        $end_date = $arguments['deadline'];
    }
    $query = "INSERT into `project_tasks` (users, title, project, `repeat`, image, details, attachments, state, priority, start_time, deadline, `order`)
VALUES ('$users', '$title', '$project_id', '$repeat', '', '', '', '$state', '$priority', '$start_date', '$end_date', '1')";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});
