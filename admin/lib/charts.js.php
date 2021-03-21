
<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 3/15/2021
 * Time: 5:15 PM
 */

require_once('../../config.php');
require_once("../auth.php");

if(isset($_POST['get_project_times'])) {
    $sql = "SELECT * FROM project_tasks;";
    $filterUser = null;
    if (isset($_POST['filter_user']) && $_POST['filter_user'] != "") {
        $filterUser = $_POST['filter_user'];
    }
    $filterProject = null;
    if (isset($_POST['filter_project']) && $_POST['filter_project'] != "") {
        $filterProject = $_POST['filter_project'];
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
}