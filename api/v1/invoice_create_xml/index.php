<?php
require_once('./../../../config.php');
session_start();
require_once('./../../../admin/backend/main.php');

require_once('../verify.php');

$data = array(
    "_plugin" => "invoices",
    "_call" => "generate_xml",
);

if(file_exists('./../../../plugins/'.$data['_plugin'].'/lib/'.$data['_call'].'.php')) {
    require_once('./../../../plugins/'.$data['_plugin'].'/lib/'.$data['_call'].'.php');
    require_once('./../../../plugins/'.$data['_plugin'].'/functions.php');
    global $Demiran;
    $d = $_POST;
    if(!isset($_POST['invoiceCategory'])){
        try {
            $code = file_get_contents('php://input');
            $d = json_decode(file_get_contents('php://input'), true);
            if(isset($d['json_content'])){
                $d = json_decode($d['json_content'], true);
            }
        }catch (Exception $e){
            echo $e;
        }

    }
    $Demiran->call($data['_call'], $d, array());
} else {
    echo "Nothing for". realpath('./../../../plugins/');
}