<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-08
 * Time: 21:51
 */

$Demiran->add_method("send_new_message", function ($arguments, $connection){
    function base64_to_image($base64_string)
    {
        //echo "Base64 data:".$base64_string."\n";

        if(!file_exists("./uploads")) {
            mkdir("./uploads");
        }
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

    if (isset($arguments['target']) && isset($arguments['message']) && isset($arguments['attachment'])
        && ($arguments['message'] != "" || $arguments['attachment'] != "")) {
        $source = $_SESSION['id'];
        $target = $arguments['target'];
        //if(isset($_POST['user-input']) && $_POST['user-input'] == "yes") {
            //In this case the target is a username or email
            //$sql = "SELECT id FROM users WHERE 'email'='".$_POST['user-input']."' OR 'username'='".$_POST['user-input']."';";
        //}
        $msg_id = "";
        if(isset($arguments['msg_id'])){
            $msg_id = $arguments['msg_id'];
        }

        $message = $arguments['message'];
        //$attachment = $_POST['attachment'];
        /*if (isset($_FILES['attachment'])) {
            $time = date("Ymdhis");
            $attachment = $time . "-" .basename($_FILES['attachment']['name']);

            $target = "./uploads/" . $attachment;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                $msg = "Image uploaded successfully";
            } else {
                $msg = "Failed to upload image";
            }
        }*/
        $imgTarget = "";
        if($arguments['attachment'] != ""){
            $imgTarget = base64_to_image($arguments['attachment']);
        }

        $create = date("Y-m-d H:i:s");
        $query = "INSERT into `messages` (source, target, created, message, status, project, attachment, msg_id)
VALUES ('$source', '$target', '$create', '$message', 'sent', '', '$imgTarget', '$msg_id')";

        $result = mysqli_query($connection, $query);
        if($result) {
            echo "OK";
        } else {
            echo "Az adatok mentése sikertelen!";
        }
    } else {
        echo "Rossz bemeneti adatok";
    }
});