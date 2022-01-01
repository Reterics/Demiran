<?php
$availableAPIs = array(
    "endpoint"=>"/api/v1",
    "available_endpoints"=>array(
        "invoice_list"=>"/api/v1/invoice_list/"
    )
);
header('Content-type: application/json');
echo json_encode($availableAPIs);