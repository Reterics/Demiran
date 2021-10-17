<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 15:21
 */

function createSimplifiedInvoice($data) {
    //echo dirname(__FILE__)."\n";
    //echo getcwd()."\n";
    if(isset($data['productCodeCategory']) && is_array($data['productCodeCategory'])) {
        $invoiceLines = array();
        for($i = 0; $i < count($data['productCodeCategory']); $i++){
            array_push($invoiceLines, array(
                "id" => $i+1,
                "productCodeCategory" => $data['productCodeCategory'][$i],
                "productCodeValue" => $data['productCodeValue'][$i],
                "lineNatureIndicator" => $data['lineNatureIndicator'][$i],
                "lineDescription" => $data['lineDescription'][$i],
                "quantity" => $data['quantity'][$i],
                "unitOfMeasure" => $data['unitOfMeasure'][$i],
                "unitPrice" => $data['unitPrice'][$i],
                "lineGrossAmountData" => $data['lineGrossAmountData'][$i],
                "lineVatRate" => $data['lineVatRate'][$i],
            ));
        }
        $data['invoiceLines'] = $invoiceLines;
    }

    $xmlContent = file_get_contents(dirname(__FILE__)."/simplified.xml");

    $xmlContent = xmlStringParser($xmlContent, $data);

    file_put_contents(dirname(__FILE__)."/simplified_export.xml", $xmlContent);
    return $xmlContent;
}