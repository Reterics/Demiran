<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

$sql = "SELECT * FROM settings WHERE setting_name='api_auth' LIMIT 1";
$result = sqlGetFirst($sql);
global $connection;
$headers = getallheaders();
$apacheHeaders = apache_request_headers();
function admin_digest_auth(){
    if (substr(PHP_SAPI, 0, 3) == 'cgi') {
        return admin_basic_auth();
    }
    if (!isset($_SERVER['PHP_AUTH_USER']) && !try_alternative()) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    } else if(isset($_SERVER['PHP_AUTH_USER']) && !login_try($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])){
        header('HTTP/1.0 401 Unauthorized');
        exit('Unauthorized');
    }
}

function admin_basic_auth(){
    // split the user/pass parts
    if(isset($_SERVER['REDIRECT_HTTP_AUTH']) && !empty($_SERVER['REDIRECT_HTTP_AUTH'])){
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
            explode(':' , base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTH'], 6)));
    }

    if (!isset($_SERVER['PHP_AUTH_USER']) && !try_alternative()) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        exit('Unauthorized');

    } else if(isset($_SERVER['PHP_AUTH_USER']) && !login_try($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])){
        header('HTTP/1.0 401 Unauthorized');
        exit('Unauthorized');
    }
    return TRUE;
}

function try_alternative() {
    $bool = false;

    if(isset($_POST['demiran_token'])) {
        echo $_POST['demiran_token'];
        $decoded = base64_decode($_POST['demiran_token']);
        list($username,$password) = explode(":",$decoded);
        $bool = login_try($username, $password);
    }
    return $bool;
}

function login_try($user, $pass)
{
    global $connection;
    $query = "SELECT * FROM `users` WHERE username='".$user."' and password='".md5($pass)."'";
    $result = mysqli_query($connection,$query) or die("Kapcsolódási hiba");
    $rows = mysqli_num_rows($result);
    if($rows!=1){
        return false;
    } else {
        $_SESSION["username"] = $user;
        $_SESSION["api_mode"] = true;
        return true;
    }
}

if(isset($result) && $result && $result['message'] != '' && $result['message'] != 'inactive' ){
    if($result['message'] == 'basic'){
        admin_digest_auth();

        /*if(!isset($headers['Authorization'])) {
            var_dump($headers);
            var_dump($apacheHeaders);
            //header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }
        $token = str_replace("Basic ", "", $headers['Authorization']);
        $decoded = base64_decode($token);
        list($username,$password) = explode(":",$decoded);*/

    }
} else {
    header('Content-type: application/json');
    die(json_encode(array("error"=>"API is not active state", "code"=>500)));
}