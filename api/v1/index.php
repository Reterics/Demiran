<?php
$availableAPIs = array(
    "endpoint"=>"/api/v1",
    "available_endpoints"=>array(
        "invoice_list"=>"/api/v1/invoice_list/",
        "invoice_create_html"=>"/api/v1/invoice_create_html/",
        "invoice_create_xml"=>"/api/v1/invoice_create_xml/",
    )
);
header('Content-type: application/json');
echo json_encode($availableAPIs);