<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:35
 */


$Demiran->add_method("save_main_settings", function ($arguments, $connection){
    $ips = $_POST["id1"];
    $geo = $_POST["id2"];
    $sql = "UPDATE `settings` SET message='".$ips."' WHERE id=1";
    mysqli_query($connection, $sql);
    $sql = "UPDATE `settings` SET message='".$geo."' WHERE id=2";
    mysqli_query($connection, $sql);
    echo "ok";
});

