<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:38
 */

$Demiran->add_method("change_user_pass", function ($arguments, $connection){
    $old_pass = $arguments["change_user_pass"];
    $new_pass = $arguments["new_pass"];
    $new_pass_again = $arguments["new_pass_again"];
    $username = $_SESSION["username"];
    if($new_pass != $new_pass_again) {
        echo "A jelszavak nem egyeznek";
    }

    $query = "SELECT * FROM `users` WHERE username='$username' and password='".$old_pass."'";
    $result = mysqli_query($connection,$query) or die("Kapcsolódási hiba az adatbázissal");
    $rows = mysqli_num_rows($result);
    if($rows==1){
        $query = "UPDATE `users` SET password='".$new_pass."' WHERE username='$username' and password='".$old_pass."'";
        $result = mysqli_query($connection, $query) or die("Kapcsolódási hiba az adatbázissal");
        if($result){
            echo "OK";
        } else {
            echo "Jelszó módosítása sikertelen, kérlek vedd fel a kapcsolatot az adminisztrátorral";
        }
    } else {
        echo "A jelenlegi jelszó nem egyezik, bejelentkezés sikertelen!";
    }
});

