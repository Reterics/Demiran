<?php
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="style.css" />
    <?php head(); ?>
</head>
<body>
<?php
headerHTML();
session_start();
if (isset($_POST['username']) && isset($connection)){
    $username = mysqli_real_escape_string($connection,stripslashes($_REQUEST['username']));
    $password = mysqli_real_escape_string($connection,stripslashes($_REQUEST['password']));
    $query = "SELECT * FROM `users` WHERE username='$username' and password='".md5($password)."'";
    $result = mysqli_query($connection,$query) or die("Kapcsolódási hiba");
    $rows = mysqli_num_rows($result);
    if($rows==1){
        $row = mysqli_fetch_array($result);
        $_SESSION['username'] = $username;
        if($row && isset($row['id'])){
            $_SESSION['id'] = $row['id'];
        }
        if($row && isset($row['role'])){
            $_SESSION['role'] = $row['role'];
        }
        if($row && isset($row['full_name'])){
            $_SESSION['full_name'] = $row['full_name'];
        }
        if($row && isset($row['work_time'])){
            $_SESSION['work_time'] = $row['work_time'];
        }
        if(isset($_POST["geo_string"]) && $_POST["geo_string"] != "") {
            $_SESSION["geo_string"] = $_POST["geo_string"];
        }
        $_SESSION['just_logged_in'] = true;
        header("Location: admin/index.php");
    }else{
        echo "<style>div.message {display: block !important;}</style>";
    }
} else {
    echo "<style>div.message{display: none}</style>";
}

    ?>
<div class="row" style="justify-content: center; margin-top: 20vh">
    <div class="col-md-6">
        <div class="logo">
            <img src="./admin/img/logo_black.svg" style="height: 100%; width: 100%;" alt="demiran Logo">
        </div>

        <form class="login" action="" method="post" name="login"
              style="    text-align: center;    max-width: 440px;margin-left: auto;margin-right: auto">
            <h1>Bejelentkezés</h1>
            <label>Felhasználói név:
            <input type="text" class="form-control" name="username" placeholder="Felhasználói név" autofocus></label>
            <label>Jelszó:
            <input type="password" class="form-control" name="password" placeholder="Jelszó"></label>
            <input id="geo_string" type="hidden" class="form-control" name="geo_string" value="">
            <input type="submit" value="Bejelentkezés" name="submit" class="btn btn-outline-black" >

            <div class="message">
                Hibás bejelentkezési adatok!
            </div>
            <p class="login-lost" style="display: none">Új vagy? <a href="registration.php">Regisztrálj</a></p>
        </form>
    </div>
</div>

<script>
    function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        const node = document.querySelector("#geo_string");
        if(node){
            node.value = latitude + "," + longitude + ",";
            node.setAttribute("value",latitude + "," + longitude + ",");
        }
    }

    function error() {
        console.log('Unable to retrieve your location');
    }

    if(!navigator.geolocation) {
        console.log('Geolocation is not supported by your browser');
    } else {
        navigator.geolocation.getCurrentPosition(success, error);
    }
</script>
<?php footer(); ?>
</body>
</html>