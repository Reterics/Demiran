<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-08
 * Time: 20:34
 */

$Demiran->add_method("get_user_list", function (){
    $sql = "SELECT id,username,full_name,email,job FROM users";

    if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'owner' && $_SESSION['role'] != 'admin'))
    {
        $sql .= " WHERE role != 'client';";
    } else {
        $sql .= ";";
    }
    $users = sqlGetAll($sql);
    header('Content-type: application/json');
    echo json_encode($users);
});