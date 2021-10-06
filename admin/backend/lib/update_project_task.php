<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-06
 * Time: 12:01
 */

$Demiran->add_method("update_project_task", function ($arguments, $connection){
    $query = "UPDATE project_tasks SET ";
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

    $query .= "users='".$users."', title='".$title."', `repeat`='".$repeat."', priority='".$priority."', state='".$state."', project='".$project_id."', start_time='".$start_date."', deadline='".$end_date."' ";
    $query .= "WHERE id='".$arguments['id']."'";

    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo $query;
        echo mysqli_connect_error();
    }
});
