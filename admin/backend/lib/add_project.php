<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:36
 */


$Demiran->add_method("add_project", function ($arguments, $connection){
    $users = "";
    $title = "";
    $category = "";
    $client = "";
    $billing = "";
    $price = "";
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
    if(isset($arguments['category'])){
        $category = $arguments['category'];
    }
    if(isset($arguments['client'])){
        $client = $arguments['client'];
    }
    if(isset($arguments['billing'])){
        $billing = $arguments['billing'];
    }
    if(isset($arguments['price'])){
        $price = $arguments['price'];
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
    $query = "INSERT into `project` (users, title, category, client, status, billing, price, details, created, start_time, deadline, `order`)
VALUES ('$users', '$title', '$category', '$client', 'open', '$billing', '$price', '', '$trn_date', '$start_date', '$end_date', '1')";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});
