<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-05
 * Time: 17:40
 */

$Demiran->add_method("get_user", function ($arguments){
    if(isset($arguments["userid"])) {
        $sql = "SELECT id,username,email,role,image,job,details,work_time FROM users WHERE id='".$arguments["userid"]."' LIMIT 1;";

        $users = sqlGetFirst($sql);
        if(isset($users['work_time']) && $users['work_time'] != "") {
            $parts = explode('-', $users['work_time']);
            if(count($parts) == 2) {
                $users['from'] = $parts[0];
                $users['to'] = $parts[1];
            }
        }
        header('Content-type: application/json');

        echo json_encode($users);
    } else {
        echo "null";
    }
});