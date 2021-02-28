<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 2020. 10. 07.
 * Time: 18:47
 */


function getClientsAsOptions($connection){
    $sql = "SELECT id,username FROM users WHERE role = 'client'";
    $result = mysqli_query($connection, $sql);
    $html = "";
    while ($row = mysqli_fetch_array($result)) {
        $html .= "<option value='".$row['id']."'>".$row['username']."</option>";
    }
    return $html;
}
function geUsersAsOptions($connection){
    $sql = "SELECT id,username FROM users WHERE role != 'client'";
    $result = mysqli_query($connection, $sql);
    $html = "";
    while ($row = mysqli_fetch_array($result)) {
        $html .= "<option value='".$row['id']."'>".$row['username']."</option>";
    }
    return $html;
}