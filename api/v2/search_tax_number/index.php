<?php
require_once('./../../../config.php');
session_start();
require_once('./../../../admin/backend/main.php');

require_once('../verify.php');

$data = array(
    "_plugin" => "invoices",
    "_call" => "search_tax_number",
);

if (file_exists('./../../../plugins/' . $data['_plugin'] . '/lib/' . $data['_call'] . '.php')) {
    require_once('./../../../plugins/' . $data['_plugin'] . '/lib/' . $data['_call'] . '.php');
    require_once('./../../../plugins/' . $data['_plugin'] . '/functions.php');
    global $Demiran;
    $d = $_POST;
    try {
        $code = file_get_contents('php://input');
        $d = json_decode(file_get_contents('php://input'), true);
    } catch (Exception $e) {
        echo $e;
    }

    if(isset($_GET['tax_number'])) {
        $d['tax_number'] = $_GET['tax_number'];
    } else if(isset($_GET['number'])) {
        $d['tax_number'] = $_GET['number'];
    } else if(isset($_GET['taxNumber'])) {
        $d['tax_number'] = $_GET['taxNumber'];
    }
    $Demiran->call($data['_call'], $d, array());
} else {
    echo "Nothing for" . realpath('./../../../plugins/');
}