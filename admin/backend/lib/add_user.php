<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-09-20
 * Time: 19:37
 */

$Demiran->add_method("add_user", function ($arguments, $connection, $files){
    function base64_to_image($base64_string)
    {
        //echo "Base64 data:".$base64_string."\n";
        $output_file = "./uploads/" . round(microtime(true) * 1000);
        $extension = "";


        $data = explode(',', $base64_string);

        if (count($data)) {
            $new = str_replace(array("data:", ";base64"), array("", ""), $data[0]);
            $parts = explode("/", $new);
            if (count($parts) > 1) {
                $extension = "." . $parts[1];
            }

            $ifp = fopen($output_file . $extension, 'wb');
            fwrite($ifp, base64_decode($data[1]));

            fclose($ifp);
            return $output_file . $extension;
        } else {
            return null;
        }
    }
    if(isset($arguments['username']) && isset($arguments['email']) && isset($arguments['password']) && isset($arguments['password_confirmation'])) {
        $username = $arguments['username'];
        $email = $arguments['email'];
        $password = $arguments['password'];
        $password_confirmation = $arguments['password_confirmation'];

        if($password_confirmation != $password) {
            echo "A jelszavak nem egyeznek!";
            return;
        }
    } else {
        echo "Hiba a kérés feldolgozása során: Hiányzó adatok!";
        return;
    }

    $trn_date = date("Y-m-d H:i:s");

    $role = "member";
    if (isset($arguments['role'])) {
        $role = $arguments['role'];
    }
    $image = "";
    if (isset($arguments['image'])) {
        $image = $arguments['image'];
    }
    $job = "";
    if (isset($arguments['job'])) {
        $job = $arguments['job'];
    }
    $work_time = "";
    if (isset($arguments['work_time'])) {
        $work_time = $arguments['work_time'];
    }

    $full_name = "";
    if (isset($arguments['full_name'])) {
        $full_name = $arguments['full_name'];
    }

    /*if (isset($files['image'])) {

        $time = date("Ymdhis");
        $image = $time . basename($files['image']['name']);

        $imageFolder = "./uploads/";
        if (!file_exists($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }
        $target = $imageFolder . $image;

        if (move_uploaded_file($files['image']['tmp_name'], $target)) {
            $msg = "Image uploaded successfully";
        } else {
            $msg = "Failed to upload image";
        }
    }*/
    $imgTarget = "";
    if($arguments['attachment'] != ""){
        $imgTarget = base64_to_image($arguments['attachment']);
    }

    $query = "INSERT into `users` (username, full_name, password, email, trn_date, role, image, job, details, work_time)
VALUES ('$username', '$full_name', '" . md5($password) . "', '$email', '$trn_date', '$role', '$imgTarget', '$job', '', '$work_time')";

    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
    }
});
