<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:35
 */

$Demiran->add_method("update_worktime", function ($arguments, $connection){
    $work_time = $_POST['from']."-".$_POST['to'];
    if (isset($connection)) {
        $sql = "UPDATE `users` SET work_time='".$work_time."' WHERE id=".$_POST['id'];
        mysqli_query($connection, $sql);
        echo "ok";
    } else {
        echo "fail";
    }
});
