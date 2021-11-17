<?php

$Demiran->add_method("nav_invoice_details", function ($arguments){
    header('Content-type: application/json');
    global $config;
    global $reporter;

    try {
        $transactionId =  $arguments["transactionId"];
        $statusXml = $reporter->queryTransactionStatus($transactionId);

        file_put_contents("status.json", json_encode($statusXml));
        if ($statusXml->processingResults->processingResult->invoiceStatus == "DONE") {
            echo "<strong>Számla állapot:</strong> Kész";
        } else if ($statusXml->processingResults->processingResult->invoiceStatus == "ABORTED") {
            echo "<strong>Számla állapot:</strong> Megszakítva";
        } else {
            echo "<strong>Számla állapot:</strong> ".$statusXml->processingResults->processingResult->invoiceStatus;
        }
        if($statusXml->processingResults->processingResult->businessValidationMessages->message){
            echo "<br><strong>Ellenőrzési megjegyzések:</strong> ".$statusXml->processingResults->processingResult->businessValidationMessages->message;
        }


    } catch(Exception $ex) {
        print get_class($ex) . ": " . $ex->getMessage();
    }

});
