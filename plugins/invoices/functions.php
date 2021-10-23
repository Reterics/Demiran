<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 10:14
 */

global $connection;

$query = "CREATE TABLE IF NOT EXISTS invoice_list (
      id int(11) AUTO_INCREMENT,
      unixID varchar(50) NOT NULL,
      supplierName varchar(60) NOT NULL,
      supplierTaxNumber varchar(15) NOT NULL,
      supplierPostCode varchar(10) NOT NULL,
      supplierTown varchar(20) NOT NULL,
      supplierStreetName varchar(60) NOT NULL,
      supplierStreet varchar(60) NOT NULL,
      supplierAddress varchar(60) NOT NULL,

      customerName varchar(60) NOT NULL,
      customerTaxNumber varchar(15) NOT NULL,
      customerPostCode varchar(10) NOT NULL,
      customerTown varchar(20) NOT NULL,
      customerStreetName varchar(60) NOT NULL,
      customerStreet varchar(60) NOT NULL,
      customerAddress varchar(60) NOT NULL,
      invoiceIssueDate varchar(20) NOT NULL,
      invoiceDeliveryDate varchar(20) NOT NULL,
      invoiceNumber varchar(50) NOT NULL,
      transactionID varchar(60) NOT NULL,
      
      totalPrice varchar(20) NOT NULL,
      data TEXT,
      PRIMARY KEY  (id)
      );

      CREATE TABLE IF NOT EXISTS invoice_user (
      id int(11) AUTO_INCREMENT,
      supplierName varchar(60),
      supplierTaxNumber varchar(15) NOT NULL UNIQUE,
      supplierPostCode varchar(10),
      supplierTown varchar(20),
      supplierStreetName varchar(60),
      supplierStreet varchar(60),
      supplierAddress varchar(60),
      supplierBankAccountNumber varchar(60),
      
      login varchar(60),
      password varchar(60),
      signKey varchar(60),
      exchangeKey varchar(60),

      PRIMARY KEY  (id)
      )";
$result = mysqli_multi_query($connection, $query);
if(!$result) {
    echo "Probléma merült fel a táblák ellenőrzése során";
} else {
    while(mysqli_more_results($connection))
    {
        mysqli_next_result($connection);
    }
}

/**
 * Custom Handlebar based XML parser to create custom Templates for every use case
 * @param $xmlContent
 * @param $data
 * @return mixed
 */
function xmlStringParser($xmlContent, $data){
    preg_match_all('/{{\w*}}/', $xmlContent, $matches, PREG_OFFSET_CAPTURE);

    // Check pure variables
    if(count($matches) > 0){
        foreach($matches[0] as $match) {
            //echo $match[0] . "\n";
            $variable = substr($match[0], 2, -2);
            if(isset($data[$variable])) {
                $xmlContent = str_replace("{{".$variable."}}", $data[$variable], $xmlContent);
            } else {
                $xmlContent = str_replace("{{".$variable."}}", "", $xmlContent);
            }
            //array_push($handleVariables, substr($match[0], 2, -2));
        }
    }

    // Check if statements

    preg_match_all('/{#\w*}}(.|\n)*{\/#\w*}}/', $xmlContent, $matches, PREG_OFFSET_CAPTURE);

    if(count($matches) > 0){
        foreach($matches[0] as $match) {
            $name = substr(explode("}}", $match[0])[0], 2);
            if(isset($data[$name]) && $data[$name] != "") {
                $values = str_replace(array("{#".$name."}}","{/#".$name."}}"), array("", ""), $match[0]);
                $xmlContent = str_replace($match[0], $values, $xmlContent);
            } else {
                $xmlContent = str_replace($match[0], "", $xmlContent);
            }
        }
    }

    // Check For Loops

    preg_match_all('/{>\w*}}(.|\n)*{\/>\w*}}/', $xmlContent, $matches, PREG_OFFSET_CAPTURE);

    if(count($matches) > 0){
        foreach($matches[0] as $match) {
            $name = substr(explode("}}", $match[0])[0], 2);
            if(isset($data[$name]) && is_array($data[$name])) {
                $forContent = str_replace(array("{>".$name."}}","{/>".$name."}}"), array("", ""), $match[0]);


                preg_match_all('/{{\+\w*}}/', $forContent, $forMatches, PREG_OFFSET_CAPTURE);
                $forVariables = array();
                if(count($forMatches) > 0){
                    foreach($forMatches[0] as $forMatch) {
                        array_push($forVariables, substr($forMatch[0], 3, -2));
                    }
                }
                $invoices = array();

                for($i = 0; $i < count($data[$name]); $i++) {
                    $currentVariable = $data[$name][$i];
                    $currentFor = $forContent;
                    foreach($forVariables as $forVariable){
                        if($currentVariable[$forVariable]){
                            $currentFor = str_replace("{{+".$forVariable."}}", $currentVariable[$forVariable], $currentFor);
                        } else {
                            $currentFor = str_replace("{{+".$forVariable."}}", "", $currentFor);
                        }
                    }
                    array_push($invoices, $currentFor);
                }

                $xmlContent = str_replace($match[0], implode( "\n", $invoices), $xmlContent);
            } else {
                $xmlContent = str_replace($match[0], "", $xmlContent);
            }
        }
    }
    return $xmlContent;
}

$softwareData = array(
    "softwareId" => "HU57823357DEMIRAN1",
    "softwareName" => "Demiran Projektmenedzsment Szoftver",
    "softwareOperation" => "ONLINE_SERVICE",
    "softwareMainVersion" => "string",
    "softwareDevName" => "Reterics Attila",
    "softwareDevContact" => "attila@reterics.com",
    "softwareDevCountryCode" => "HU",
    "softwareDevTaxNumber" => "57823357",
);

require_once("load_nav_classes.php");
