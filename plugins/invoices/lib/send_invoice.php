<?php

$Demiran->add_method("send_invoice", function ($arguments){
    header('Content-type: application/json');
    if (isset($arguments['json_content'])) {
        unset($arguments['json_content']);
    }
    global $config;
    global $softwareData;
    global $apiUrl;
    global $reporter;
    $xml = null;
    $transactionId = null;
    $compression = $arguments['compression'] ?? true;
    $result = array(
        "error"=>null,
        "transaction_id"=>null,
        "xml"=>null
    );


    overrideNAVUser($arguments);

    switch ($arguments['invoiceCategory']) {
        case 'SIMPLIFIED':
            if(file_exists(__DIR__ . '../invoice_types/simplified.php')) {
                require(__DIR__ . '/../invoice_types/simplified.php');
            } else if(file_exists('./../../../plugins/invoices/invoice_types/simplified.php')) {
                require('./../../../plugins/invoices/invoice_types/simplified.php');
            }

            $xml = createSimplifiedInvoice($arguments);
            if (isset($_GET['debug'])) {
                file_put_contents(__DIR__."/_temp_json_invoice.json", json_encode($arguments));
                $temporaryFileName = "temp_".time().".xml";
                file_put_contents(__DIR__."/".$temporaryFileName, $xml);
            }
            //
            // $invoiceXml = simplexml_load_file( $temporaryFileName);
            $invoiceXml = simplexml_load_string($xml);
            if($invoiceXml){
                try {
                    $invoices = new NavOnlineInvoice\InvoiceOperations($compression);

                    // Maximum 100db Invoice
                    $invoices->add($invoiceXml);

                    $transactionId = $reporter->manageInvoice($invoices, "CREATE");
                    //$transactionId = $reporter->manageInvoice($invoiceXml, "CREATE");
                    $result["transaction_id"] = $transactionId;
                    if (isset($_GET['debug']) && $result['xml'] === null) {
                        $result["xml"] = $xml;
                    }

                    $result["transaction"] = getTransactionDetails($result["transaction_id"]);
                }catch (Exception $ex){
                    $result["error"] = get_class($ex) . ": " . $ex->getMessage();
                    $result["xml"] = $xml;
                }
            }

            //unlink($temporaryFileName);
    }

    echo json_encode($result);
});