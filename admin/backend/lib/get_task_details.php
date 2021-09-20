<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:38
 */

$Demiran->add_method("get_task_details", function(){
    global $connection;
    $query = "SELECT * FROM `project_tasks` WHERE id=".$_POST['get_task_details']." LIMIT 1";
    $result = mysqli_query($connection, $query);
    if($result) {
        $row = mysqli_fetch_array($result);
        echo json_encode($row);
    } else {
        echo "false";
    }
});

