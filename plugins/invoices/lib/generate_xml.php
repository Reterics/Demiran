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
            require('../invoice_types/simplified.php');
            $xml = createSimplifiedInvoice($arguments);
    }

    echo json_encode($xml);
});