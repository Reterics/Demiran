<?php
$Demiran->add_method("search_tax_number", function ($arguments){
    header('Content-type: application/json');
    $taxNumber = substr($arguments['tax_number'], 0, 8);
    overrideNAVUser($arguments);
    global $reporter;
    $transactionListResult = null;
    if ($reporter) {
        $transactionListResult = json_decode(json_encode($reporter->queryTaxpayer($taxNumber)), true);
    }
    $output = array(
        "valid" => false,
        "taxpayerName" => null,
        "taxpayerId" => null,
        "vatCode" => null,
        "countyCode" => null,
        "postalCode" => null,
        "city" => null,
        "streetName" => null,
        "publicPlaceCategory" => null,
        "number" => null,
        "floor" => null,
        "door" => null
    );
    if ($transactionListResult) {
        $output["valid"] = true;
        $output["taxpayerName"] = $transactionListResult['taxpayerName'] ?? null;
        $output["taxpayerId"] = $transactionListResult['taxNumberDetail']['taxpayerId'] ?? null;
        $output["vatCode"] = $transactionListResult['taxNumberDetail']['vatCode'] ?? null;
        $output["countyCode"] = $transactionListResult['taxNumberDetail']['countyCode'] ?? null;
        $output["postalCode"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['postalCode'] ?? null;
        $output["city"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['city'] ?? null;
        $output["streetName"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['streetName'] ?? null;
        $output["publicPlaceCategory"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['publicPlaceCategory'] ?? null;
        $output["number"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['number'] ?? null;
        $output["floor"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['floor'] ?? null;
        $output["door"] = $transactionListResult['taxpayerAddressList']['taxpayerAddressItem']['taxpayerAddress']['door'] ?? null;
    }

    echo json_encode($output);
});
