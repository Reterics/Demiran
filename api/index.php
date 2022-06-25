<?php
$availableAPIs = array(
    "endpoint"=>"/api",
    "available_endpoints"=>array(
        "v1"=>"/api/v1/",
        "v2"=>"/api/v2/"
    )
);
header('Content-type: application/json');
echo json_encode($availableAPIs);