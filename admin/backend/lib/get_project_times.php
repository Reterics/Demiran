<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:34
 */


$Demiran->add_method("get_project_times", function ($arguments, $connection){
    $sql = "SELECT * FROM project_tasks;";
    $filterUser = null;
    if (isset($arguments['filter_user']) && $arguments['filter_user'] != "") {
        $filterUser = $arguments['filter_user'];
    }
    $filterProject = null;
    if (isset($arguments['filter_project']) && $arguments['filter_project'] != "") {
        $filterProject = $arguments['filter_project'];
    }
    echo "[";
    $query = mysqli_query($connection,$sql);
    if ($query) {
        $i = 0;
        $found = false;
        while ($row = mysqli_fetch_array($query)) {
            if ($filterUser == null || isset($row["users"])) {
                if(
                    ($filterProject != null && isset($row["project"]) && $row["project"] == $filterProject)
                    || $filterProject == null) {

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
                        echo '{"start_time":"'.$row['start_time'].'","deadline":"'.$row['deadline'].'","id":"'.$row['id'].'"}';
                        $i = $i + 1;
                    }
                }


            }
        }
    }
    echo "]";
});

