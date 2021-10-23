<?php

$Demiran->add_method("add_technical_user", function ($arguments){
    header('Content-type: application/json');
    global $connection;
    $supplierName= $arguments['supplierName'];
    $supplierTaxNumber= $arguments['supplierTaxNumber'];
    $supplierPostCode= $arguments['supplierPostCode'];
    $supplierTown= $arguments['supplierTown'];
    $supplierStreetName= $arguments['supplierStreetName'];
    $supplierStreet= $arguments['supplierStreet'];
    $supplierAddress= $arguments['supplierAddress'];
    $supplierBankAccountNumber= $arguments['supplierBankAccountNumber'];
    $login= $arguments['login'];
    $password= $arguments['password'];

    $signKey= $arguments['signKey'];
    $exchangeKey= $arguments['exchangeKey'];

    $query = "INSERT into `invoice_user` (supplierName, supplierTaxNumber, supplierPostCode, supplierTown, supplierStreetName, supplierStreet, supplierAddress, login, supplierBankAccountNumber, password, signKey, exchangeKey)
VALUES ('$supplierName', '$supplierTaxNumber', '$supplierPostCode', '$supplierTown', '$supplierStreetName', '$supplierStreet', '$supplierAddress', '$login','$supplierBankAccountNumber', '$password', '$signKey', '$exchangeKey')";
    $result = mysqli_query($connection, $query);
    if($result) {
        echo "OK";
    } else {
        echo "Az adatok mentése sikertelen!\n".json_encode(mysqli_error($connection));
    }
});