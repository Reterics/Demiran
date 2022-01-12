<?php

$Demiran->add_method("send_invoice", function ($arguments){
    header('Content-type: application/json');
    global $config;
    global $reporter;
    $xml = null;
    $transactionId = null;
    $result = array(
        "error"=>null,
        "transaction_id"=>null,
        "xml"=>null
    );
    if(isset($arguments['technical_user_override'])){
        if(isset($arguments['nav_tax_number']) &&
            isset($arguments['nav_login']) &&
            isset($arguments['nav_password']) &&
            isset($arguments['sign_key']) &&
            isset($arguments['exchange_key'])){
            $userData = array(
                "login" => $arguments['nav_login'],
                "password" => $arguments['nav_password'],
                // "passwordHash" => "...", // Opcionális, a jelszó már SHA512 hashelt változata. Amennyiben létezik ez a változó, akkor az authentikáció során ezt használja
                "taxNumber" => $arguments['nav_tax_number'],
                "signKey" => $arguments['sign_key'],
                "exchangeKey" => $arguments['exchange_key'],
            );
            global $softwareData;
            global $apiUrl;
            try {
                $config = new NavOnlineInvoice\Config($apiUrl, $userData, $softwareData);
                $config->setCurlTimeout(60);
                //$config->verifySSL = false;
                $reporter = new NavOnlineInvoice\Reporter($config);
            }catch (Exception $e){
                echo $e;
            }
        }

    }
    switch ($arguments['invoiceCategory']) {
        case 'SIMPLIFIED':
            if(file_exists('../plugins/invoices/invoice_types/simplified.php')) {
                require('../plugins/invoices/invoice_types/simplified.php');
            } else if(file_exists('./../../../plugins/invoices/invoice_types/simplified.php')) {
                require('./../../../plugins/invoices/invoice_types/simplified.php');
            }
            file_put_contents("_temp_json_invoice.json", json_encode($arguments));
            $xml = createSimplifiedInvoice($arguments);
            $temporaryFileName = "temp_".time().".xml";
            file_put_contents($temporaryFileName, $xml);
            $invoiceXml = simplexml_load_file( $temporaryFileName);
            if($invoiceXml){
                try {
                    $transactionId = $reporter->manageInvoice($invoiceXml, "CREATE");
                    $result["transaction_id"] = $transactionId;
                }catch (Exception $ex){
                    $result["error"] = get_class($ex) . ": " . $ex->getMessage();
                    $result["xml"] = $invoiceXml;
                }
            }

            unlink($temporaryFileName);
    }

    echo json_encode($result);
});