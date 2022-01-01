<?php
require_once('./../../../config.php');
require_once('./../../../admin/auth.php');

require_once('./../../../admin/backend/main.php');

require_once('../verify.php');
$data = array(
    "_plugin" => "invoices",
    "_page" => "create",
);

if(file_exists('./../../../plugins/'.$data['_plugin'].'/pages/'.$data['_page'].'.php')) {
    require_once('./../../../plugins/'.$data['_plugin'].'/functions.php');
    require_once('./../../../plugins/'.$data['_plugin'].'/pages/'.$data['_page'].'.php');
} else {
    echo "Nothing for". realpath('./../../../plugins/');
}