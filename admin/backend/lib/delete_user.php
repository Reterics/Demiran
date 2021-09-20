<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:37
 */

$Demiran->add_method("delete_user", function ($arguments, $connection) {
    if (isset($arguments['deleteuser']) && $arguments['deleteuser'] != "") {
        $query = "DELETE from `users` WHERE id=" . $_POST['deleteuser'];
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
