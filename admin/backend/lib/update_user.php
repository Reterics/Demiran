<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-05
 * Time: 18:22
 */

$Demiran->add_method("update_user", function ($arguments, $connection, $files){
    $query = "UPDATE users SET ";
    $first = false;
    if(isset($arguments['password']) && isset($arguments['password_confirmation']) &&
        $arguments['password'] != "" && $arguments['password_confirmation'] != ""){
        $password = $arguments['password'];
        $password_confirmation = $arguments['password_confirmation'];

        if($password_confirmation != $password) {
            echo "A jelszavak nem egyeznek!";
            return;
        } else {
            $query .= "password='".md5($password)."'";
            $first = true;
        }
    }
    if (!isset($arguments['id']) || $arguments['id'] == "") {
        echo "ID megadása kötelező!";
        return;
    }
    if(isset($arguments['username']) && isset($arguments['email']) ) {
        if($first) {
            $query .= ", ";
        }
        $username = $arguments['username'];
        $query .= "username='".$username."'";

        $email = $arguments['email'];
        $query .= ", email='".$email."'";
    } else {
        echo "Hiba a kérés feldolgozása során: Hiányzó adatok!";
        return;
    }
    $role = "member";
    if (isset($arguments['role'])) {
        $role = $arguments['role'];
    }
    $job = "";
    if (isset($arguments['job'])) {
        $job = $arguments['job'];
    }
    $work_time = "";
    if (isset($arguments['work_time'])) {
        $work_time = $arguments['work_time'];
    }
    $full_name = "";
    if (isset($arguments['full_name'])) {
        $full_name = $arguments['full_name'];
    }
    $query .= ", role='".$role."', job='".$job."', work_time='".$work_time."', full_name='".$full_name."'";

    $query .= "WHERE id='".$arguments['id']."'";

    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});
