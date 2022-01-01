<?php

$sql = "SELECT * FROM settings WHERE setting_name='api_auth' LIMIT 1";
$result = sqlGetFirst($sql);
global $connection;
if(isset($result) && $result && $result['message'] != '' && $result['message'] != 'inactive' ){
    if($result['message'] == 'basic'){

        if(!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }
        $query = "SELECT * FROM `users` WHERE username='".$_SERVER['PHP_AUTH_USER']."' and password='".md5($_SERVER['PHP_AUTH_PW'])."'";
        $result = mysqli_query($connection,$query) or die("Kapcsolódási hiba");
        $rows = mysqli_num_rows($result);
        if($rows!=1){
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }
    }
} else {
    header('Content-type: application/json');

    die(json_encode(array("error"=>"API is not active state", "code"=>500)));
}