<?php
require_once('./../../../config.php');
session_start();
require_once(__DIR__.'/../../../admin/backend/main.php');

require_once(__DIR__.'/../verify.php');

$data = array(
    "_plugin" => "invoices",
    "_call" => "get_nav_invoice_list",
);

if(file_exists(__DIR__ . '/../../../plugins/'.$data['_plugin'].'/lib/'.$data['_call'].'.php')) {
    require_once(__DIR__ . '/../../../plugins/'.$data['_plugin'].'/lib/'.$data['_call'].'.php');
    require_once(__DIR__ . '/../../../plugins/'.$data['_plugin'].'/functions.php');
    global $Demiran;
    $d = $_POST;
    try {
        $d = json_decode(file_get_contents('php://input'), true);
    }catch (Exception $e){
        echo $e;
    }

    $Demiran->call($data['_call'], $d, array());
} else {
    echo "Nothing for". realpath('./../../../plugins/');
}