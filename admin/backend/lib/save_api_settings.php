<?php


$Demiran->add_method("save_api_settings", function ($arguments, $connection){
    $auth = $_POST["api_auth"];
    $sql = "UPDATE `settings` SET message='".$auth."' WHERE setting_name='api_auth'";
    mysqli_query($connection, $sql);

    $auth = $_POST["api_nav_url"];
    $sql = "UPDATE `settings` SET message='".$auth."' WHERE setting_name='api_nav_url'";
    mysqli_query($connection, $sql);

    $auth = $_POST["api_default_user"];
    $sql = "UPDATE `settings` SET message='".$auth."' WHERE setting_name='api_default_user'";
    mysqli_query($connection, $sql);


    echo "ok";
});

