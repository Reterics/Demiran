<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-09-21
 * Time: 17:56
 */

$Demiran->add_method("upload_sql", function ($arguments, $connection){
    if(isset($_FILES['import_file'])) {
        $fileName = basename($_FILES['import_file']["name"]);
        $tmpFile = $_FILES['import_file']["tmp_name"];
        $fileType = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
        if($fileType == "sql" ) {
            $sql_content = file_get_contents($tmpFile);
            // It seems to be a Demiran Export file
            if(strpos($sql_content, "Demiran Projektmenedzsment Rendszer") !== false &&
                strpos($sql_content, "INSERT INTO") !== false ) {
                $result = mysqli_multi_query($connection, $sql_content);
                if($result){
                    echo "<script type=\"application/javascript\">window.addEventListener(\"load\", ()=>{Demiran.alert(\"Feltöltés sikeres!\")});</script>";
                }
            }
        }
    }
});
