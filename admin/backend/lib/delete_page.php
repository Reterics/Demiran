<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:35
 */


$Demiran->add_method("delete_page", function ($arguments, $connection){
    if(isset($arguments['deletepage']) && $arguments['deletepage'] != ""){
        $query = "DELETE from `pages` WHERE id=".$arguments['deletepage'];
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
