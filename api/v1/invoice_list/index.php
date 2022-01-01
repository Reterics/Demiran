<?php
require_once('./../../../config.php');
session_start();
require_once('./../../../admin/backend/main.php');

require_once('../verify.php');

$data = array(
    "_plugin" => "invoices",
    "_call" => "get_invoice_list",
);

if(file_exists('./../../../plugins/'.$data['_plugin'].'/lib/'.$data['_call'].'.php')) {
    require_once('./../../../plugins/'.$data['_plugin'].'/lib/'.$data['_call'].'.php');
    require_once('./../../../plugins/'.$data['_plugin'].'/functions.php');
    global $Demiran;
    $Demiran->call($data['_call'], array(), array());
} else {
    echo "Nothing for". realpath('./../../../plugins/');
}