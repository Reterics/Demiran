<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 15:03
 */

$Demiran->add_method("generate_xml", function ($arguments){
    header('Content-type: application/json');
    $xml = null;
    switch ($arguments['invoiceCategory']) {
        case 'SIMPLIFIED':
            if(file_exists('../plugins/invoices/invoice_types/simplified.php')) {
                require('../plugins/invoices/invoice_types/simplified.php');
            } else if(file_exists('./../../../plugins/invoices/invoice_types/simplified.php')) {
                require('./../../../plugins/invoices/invoice_types/simplified.php');
            }
            file_put_contents(__DIR__."/_temp_json_invoice.json", json_encode($arguments));
            file_put_contents(__DIR__."/_temp_json_invoicep.json", json_encode($_POST));
            file_put_contents(__DIR__."/_temp_json_invoicer.json", json_encode($_REQUEST));

            $xml = createSimplifiedInvoice($arguments);
    }
    if($xml == ""){
        echo "No XML";
    }
    echo $xml;
});