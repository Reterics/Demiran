<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:36
 */


$Demiran->add_method("delete_project", function ($arguments, $connection) {
    if (isset($arguments['deleteproject']) && $arguments['deleteproject'] != "") {
        $query = "DELETE from `project` WHERE id=" . $_POST['deleteproject'];
        $result = mysqli_query($connection, $query);
        if ($result) {
            echo "OK";
        } else {
            echo mysqli_connect_error();
        }
    } else {
        echo "Hiányzó bemeneti adat!";
    }
});
