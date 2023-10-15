<?php
$availableAPIs = array(
    "endpoint"=>"/api",
    "available_endpoints"=>array("v1"=>"/api/v1/")
);
header('Content-type: application/json');
echo json_encode($availableAPIs);