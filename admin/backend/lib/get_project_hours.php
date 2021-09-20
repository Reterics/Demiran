<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:21
 */

$Demiran->add_method("get_project_hours", function ($arguments, $connection){
    $sql = "SELECT shift_list.id, shift_list.user as user, shift_list.start_time as start_time, shift_list.end_time as end_time, users.username as username FROM shift_list LEFT JOIN users ON shift_list.user=users.id";
    $filterUser = null;
    if (isset($arguments['filter_user']) && $arguments['filter_user'] != "") {
        $sql .= " WHERE user=".$arguments['filter_user'];
    }
    echo "[";
    $query = mysqli_query($connection,$sql);
    if ($query) {
        $i = 0;
        while ($row = mysqli_fetch_array($query)) {
            if ($i != 0) {
                echo ",";
            }
            echo '{"username":"'.$row['username'].'","start_time":"'.$row['start_time'].'","end_time":"'.$row['end_time'].'"}';
            $i = $i + 1;

        }
    }
    echo "]";
});

