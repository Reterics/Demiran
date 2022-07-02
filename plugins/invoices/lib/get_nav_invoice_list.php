<?php

$Demiran->add_method("get_nav_invoice_list", function ($arguments) {
    header('Content-type: application/json');
    overrideNAVUser($arguments);
    echo json_encode(getTransactionList($arguments['from'] ?? null, $arguments['to'] ?? null));
});