<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-06
 * Time: 11:18
 */

$Demiran->add_method("update_project", function ($arguments, $connection){
    $query = "UPDATE project SET ";
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
    $query .= "users='".$users."', title='".$title."', category='".$category."', client='".$client."', billing='".$billing."', price='".$price."', start_time='".$start_date."', deadline='".$end_date."'";
    $query .= "WHERE id='".$arguments['id']."'";

    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});