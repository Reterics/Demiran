<?php

$Demiran->add_method("nav_invoice_details", function ($arguments){
    header('Content-type: application/json');
    overrideNAVUser($arguments);
    echo json_encode(getTransactionDetails($arguments['transactionId']));
});
