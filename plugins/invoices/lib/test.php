<?php

$Demiran->add_method("test", function ($arguments){
    header('Content-type: application/json');
    global $softwareData;

    echo $_GET["user"]."-".$softwareData["softwareName"];
});