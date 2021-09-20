<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:36
 */

$Demiran->add_method("add_page", function ($arguments, $connection) {
    $user = "";
    if(isset($arguments['user'])){
        $user = $arguments['user'];
    }
    $title = "";
    if(isset($arguments['title'])){
        $title = $arguments['title'];
    }
    $categories = "";
    if(isset($arguments['categories'])){
        $categories = $arguments['categories'];
    }
    $tags = "";
    if(isset($arguments['tags'])){
        $tags = $arguments['tags'];
    }
    $created = date("Y-m-d H:i:s");
    $modified = $created;

    $query = "INSERT into `pages` (user, title, categories, tags, image, details, created, modified) VALUES ('$user','$title','$categories','$tags','','','$created','$modified');";

    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "";
    } else {
        echo mysqli_connect_error();
    }
});
