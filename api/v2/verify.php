<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

global $connection;
if(!isset($connection) || !$connection) {
    header("HTTP/1.1 404 Not Found");
    exit('Internal Server Error');
}

if (! authenticate()) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header("HTTP/1.1 401 Unauthorized");
    exit('Unauthorized');
}


function authenticate() {
    global $connection;
    try {
        switch(true) {
            case array_key_exists('HTTP_AUTHORIZATION', $_SERVER) :
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $_SERVER) :
                $authHeader = $_SERVER['Authorization'];
                break;
            default :
                $authHeader = null;
                break;
        }
        preg_match('/Basic\s(\S+)/', $authHeader, $matches);

        $username = mysqli_real_escape_string($connection,stripslashes($_SERVER['PHP_AUTH_USER']));
        $password = mysqli_real_escape_string($connection,stripslashes($_SERVER['PHP_AUTH_PW']));
        $query = "SELECT * FROM `users` WHERE username='$username' and password='".md5($password)."'";
        $result = mysqli_query($connection,$query) or die("Kapcsolódási hiba");
        $rows = mysqli_num_rows($result);
        if ($rows==1) {
            $row = mysqli_fetch_array($result);
            if ($row && isset($row['id'])) {
                $_SESSION['id'] = $row['id'];
            }
            $_SESSION['username'] = $username;
            $_SESSION['just_logged_in'] = true;
            return true;
        }
    } catch (\Exception $e) {
        return false;
    }
}