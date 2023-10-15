<?php
require_once('./../../../config.php');
session_start();
require_once('./../../../admin/backend/main.php');

require_once('../verify.php');
$data = array(
    "_plugin" => "invoices",
    "_page" => "create",
);

if(file_exists('./../../../plugins/'.$data['_plugin'].'/pages/'.$data['_page'].'.php')) {
    require_once('./../../../plugins/'.$data['_plugin'].'/functions.php');
    header('Content-type: text/html');
    require_once('./../../../plugins/'.$data['_plugin'].'/pages/'.$data['_page'].'.php');
} else {
    echo "Nothing for". realpath('./../../../plugins/');
}