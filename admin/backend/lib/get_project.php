<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-06
 * Time: 11:01
 */

$Demiran->add_method("get_project", function ($arguments){
    if(isset($arguments["projectid"])) {
        $sql = "SELECT id,title,category,users,client,billing,price,start_time,deadline FROM project WHERE id='".$arguments["projectid"]."' LIMIT 1;";

        $project = sqlGetFirst($sql);
        header('Content-type: application/json');
        echo json_encode($project);
    } else {
        echo "null";
    }
});