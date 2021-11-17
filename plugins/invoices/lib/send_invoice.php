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
    switch ($arguments['invoiceCategory']) {
        case 'SIMPLIFIED':
            require('../plugins/invoices/invoice_types/simplified.php');

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