<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-07
 * Time: 9:17
 */

$Demiran->add_method("get_gdpr_data", function (){
    $currentUserId = $_SESSION['id'];
    $currentUserName = $_SESSION["username"];

    $sql = "SELECT * FROM users WHERE id=".$currentUserId." LIMIT 1;";
    $usrData = sqlGetFirst($sql);

    $whereUsers = "WHERE users LIKE '".$currentUserId.",%'	OR users LIKE '%,".$currentUserId."' OR users LIKE ',".$currentUserId.",'";

    $sql = "SELECT title,category,id,`status`,created,start_time,deadline FROM project ".$whereUsers." LIMIT 1;";
    $projects = sqlGetAll($sql);
    $sql = "SELECT title,project,id,`repeat`,details,state,priority,start_time,deadline FROM project_tasks ".$whereUsers.";";
    $tasks = sqlGetAll($sql);
    $sql = "SELECT id,`user`,duration,start_time,end_time,note,task FROM shift_list WHERE `user`='".$currentUserId."';";
    $shifts = sqlGetAll($sql);

    $allUserData = array(
        "username"=>$currentUserName,
        "main"=>$usrData,
        "projects"=>$projects,
        "project_tasks"=>$tasks,
        "work_time"=>$shifts
    );

    header('Content-type: application/json');
    echo json_encode($allUserData);
});