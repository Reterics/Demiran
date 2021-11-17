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
    $data['SummaryVatContent'] = "";
    $data['SummaryVatExemption'] = "";
    $data['vatAmountSummary'] = 0;
    $data['invoiceAmountSummary'] = 0;
    $data['summarySimplifiedData'] = array();
    if(isset($data['productCodeCategory']) && is_array($data['productCodeCategory'])) {
        $invoiceLines = array();
        for($i = 0; $i < count($data['productCodeCategory']); $i++){

            $lineVatRateContent = "";
            if($data['lineVatRate_exception'][$i] != "") {
                $data['SummaryVatExemption'] = $data['lineVatRate_exception'][$i];
                if($data['SummaryVatExemption'] == 'AAM'){
                    $data['SummaryVatReason'] = "Alanyi Adómentes";
                } else if($data['SummaryVatExemption'] == 'TAM'){
                    $data['SummaryVatReason'] = "Tárgyi Adómentes";
                }
                if(!isset($data['summarySimplifiedData'][$data['SummaryVatExemption']])) {
                    $data['summarySimplifiedData'][$data['SummaryVatExemption']] = array(
                        "vatAmountSummary" => floatval($data['lineGrossAmountData'][$i]),
                        "lineVatRate" => "<vatExemption>".
                                            "<case>".$data['SummaryVatExemption']."</case>".
                                            "<reason>".$data['SummaryVatReason']."</reason>".
                                            "</vatExemption>"
                    );
                } else {
                    $data['summarySimplifiedData'][$data['SummaryVatExemption']]["vatAmountSummary"] += floatval($data['lineGrossAmountData'][$i]);
                }
                $lineVatRateContent = $data['summarySimplifiedData'][$data['SummaryVatExemption']]["lineVatRate"];
            } else if($data['lineVatRate'][$i] != "" && $data['lineVatRate'][$i] != "0" && $data['lineVatRate'][$i] != 0){
                $data['SummaryVatContent'] = $data['lineVatRate'][$i];
                if(!isset($data['summarySimplifiedData'][$data['SummaryVatContent']])) {
                    $data['summarySimplifiedData'][$data['SummaryVatContent']] = array(
                        "vatAmountSummary" => floatval($data['lineGrossAmountData'][$i]),
                        "lineVatRate" => "<vatContent>".$data['lineVatRate'][$i]."</vatContent>"
                    );
                } else {
                    $data['summarySimplifiedData'][$data['SummaryVatContent']]["vatAmountSummary"] += floatval($data['lineGrossAmountData'][$i]);
                }
                $lineVatRateContent = $data['summarySimplifiedData'][$data['SummaryVatContent']]["lineVatRate"];
            }
            $data['vatAmountSummary'] += floatval($data['lineVatData'][$i]);
            $data['invoiceAmountSummary'] += floatval($data['lineGrossAmountData'][$i]);

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
                "lineVatRate_exception" => $data['lineVatRate_exception'][$i],
                "lineVatRateContent" => $lineVatRateContent
            ));

        }
        $data['invoiceLines'] = $invoiceLines;
    }
    if(isset($data['customerVatStatus']) && $data['customerVatStatus'] == "PRIVATE_PERSON") {
        //Nem lehetnek kitöltve:  customerVatData - customerName - customerAddress
        $data['customerAddressAvailable'] = "";
    } else {
        $data['customerAddressAvailable'] = "yes";
    }

    $data['summaryLines'] = array();
    $i = 0;
    foreach ($data['summarySimplifiedData'] as $row){
        $data['summaryLines'][$i] = $row;
        $i++;
    }
    $data['summarySimplifiedData'] = null;
    $xmlContent = file_get_contents(dirname(__FILE__)."/simplified.xml");
    $xmlContent = xmlStringParser($xmlContent, $data);

    file_put_contents(dirname(__FILE__)."/simplified_export.xml", $xmlContent);
    return $xmlContent;
}