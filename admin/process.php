<?php

require_once('../config.php');
require_once('./auth.php');

require_once('./lib/methods.php');

if(isset($_POST['_call'])) {
    global $Demiran;
    $Demiran->call($_POST['_call'], $_POST);
} else {
    // Backward compatibility, support version 1.1
    run_methods_for_obj($_POST);
}
