<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-12
 * Time: 11:07
 */

$Demiran->add_method("get_project_list", function ($arguments){
    $sql = "SELECT id,users,title,category,client,status,start_time,deadline FROM project;";

    $project = sqlGetAll($sql);
    header('Content-type: application/json');
    echo json_encode($project);

});