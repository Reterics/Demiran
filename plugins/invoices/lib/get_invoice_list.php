<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 10:11
 */

$Demiran->add_method("get_invoice_list", function (){
    $sql = "SELECT * FROM invoice_list;";
    header('Content-type: application/json');
    echo json_encode(sqlGetAll($sql));
});