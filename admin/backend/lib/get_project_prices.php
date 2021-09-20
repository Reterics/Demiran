<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:34
 */

$Demiran->add_method("get_project_prices", function ($arguments, $connection){
    $sql = "SELECT id, users, price, deadline FROM project";
    $filterUser = null;
    if (isset($arguments['filter_user']) && $arguments['filter_user'] != "") {
        $filterUser = $arguments['filter_user'];
    }
    echo "[";
    $query = mysqli_query($connection,$sql);
    if ($query) {
        $i = 0;
        $found = false;
        while ($row = mysqli_fetch_array($query)) {
            if ($filterUser == null || isset($row["users"])) {
                $pieces = explode(",", $row['users']);

                foreach ($pieces as $userID) {
                    if ($filterUser == null || $filterUser == $userID) {
                        $found = true;
                    }
                }

                if ($found || $filterUser == null) {
                    if ($i != 0) {
                        echo ",";
                    }
                    if($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin'){
                        echo '{"price":"'.$row['price'].'","deadline":"'.$row['deadline'].'","id":"'.$row['id'].'"}';

                    } else {
                        echo '{"price":1,"deadline":"'.$row['deadline'].'","id":"'.$row['id'].'"}';

                    }
                    $i = $i + 1;
                }
            }
        }
    }
    echo "]";
});

