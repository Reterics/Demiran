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
      supplierTaxNumber varchar(15) NOT NULL,
      supplierPostCode varchar(10),
      supplierTown varchar(20),
      supplierStreetName varchar(60),
      supplierStreet varchar(60),
      supplierAddress varchar(60),
      supplierBankAccountNumber varchar(60),
      
      login varchar(60) UNIQUE,
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

    preg_match_all('/{#\w*}}(.|\n)*{\/#\w*}}/U', $xmlContent, $matches, PREG_OFFSET_CAPTURE);

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

    preg_match_all('/{>\w*}}(.|\n)*{\/>\w*}}/U', $xmlContent, $matches, PREG_OFFSET_CAPTURE);

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
                        if(isset($currentVariable[$forVariable])){
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

$apiUrl = "https://api-test.onlineszamla.nav.gov.hu/invoiceService/v3";

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
$selectedTechnicalId = null;
$technicalUserSQL = "SELECT * FROM invoice_user";
if(isset($_GET['user']) && $_GET['user'] != '') {
    $technicalUserSQL .= " WHERE id='".$_GET['user']."'";
} else {
    global $globalSettings;
    $defaultUser = $globalSettings->getSettingValueByName('api_default_user');
    if(isset($defaultUser) && $defaultUser){
        $technicalUserSQL .= " WHERE id='".$defaultUser."'";
    } else {
        $technicalUserSQL .= " LIMIT 1";
    }
}
$technicalUser = sqlGetFirst($technicalUserSQL);
if(isset($technicalUser) && $technicalUser && isset($technicalUser['id'])){
    $selectedTechnicalId = $technicalUser['id'];
}
$missingData = false;
$missingDataList = array();
function getDataIfThere($technicalUser, $name){
    global $missingData;
    global $missingDataList;
    if(isset($technicalUser) && $technicalUser && isset($technicalUser[$name]) && $technicalUser[$name] != "") {
        return $technicalUser[$name];
    } else {
        $missingData = true;
        array_push($missingDataList, $name);
        return "";
    }
}
if(isset($technicalUser) && isset($technicalUser['supplierTaxNumber'])){
    $parts = explode("-", $technicalUser['supplierTaxNumber']);
    if(count($parts) < 3) {
        $missingData = true;
    }
    $technicalUser['supplierTaxpayerId'] = $parts[0];
    $technicalUser['supplierVatCode'] = $parts[1];
    $technicalUser['supplierCountyCode'] = $parts[2];
}

//$hashed = hash("sha512", $password);
$userData = array(
    "login" => getDataIfThere($technicalUser, "login"),
    "password" => getDataIfThere($technicalUser, "password"),
    // "passwordHash" => "...", // Opcionális, a jelszó már SHA512 hashelt változata. Amennyiben létezik ez a változó, akkor az authentikáció során ezt használja
    "taxNumber" => getDataIfThere($technicalUser, "supplierTaxpayerId"),
    "signKey" => getDataIfThere($technicalUser, "signKey"),
    "exchangeKey" => getDataIfThere($technicalUser, "exchangeKey"),
);

require_once("load_nav_classes.php");
$config = null;
$reporter = null;
try {
    if($userData['login'] != "" && $userData['taxNumber'] != "" &&
        $userData['signKey'] != "" && $userData['exchangeKey'] != "") {
        $config = new NavOnlineInvoice\Config($apiUrl, $userData, $softwareData);
        $config->setCurlTimeout(60);
        //$config->verifySSL = false;
        $reporter = new NavOnlineInvoice\Reporter($config);
    }
}catch (Exception $e){
    echo $e;
}

$token = null;
function getTechnicalUsersAsOptions($id){
    global $connection;
    $sql = "SELECT * FROM invoice_user";
    if(isset($id) && $id != ''){
        $sql.= " WHERE id='".$id."'";
    } else {
        $sql.= " LIMIT 1";
    }
    $result = sqlGetAll($sql);
    $html = "";
    if(!$result || !is_array($result)){
        return $html;
    }
    foreach ($result as $user){
        if(isset($user['id']) && isset($user['supplierName'])) {
            $html .= "<option value='".$user['id']."'>".$user['supplierName']."</option>";
        }
    }
    return $html;
}

function getNAVToken(){
    global $reporter;
    global $token;
    $error = null;
    $result = null;
    try {
        if(isset($reporter) && $reporter != null){
            $t = $reporter->tokenExchange();
            $token = $t;
        } else {
            $error = "Nem megfelelő Technikai Felhasználó";
        }
    } catch(Exception $ex) {
        //$error =  get_class($ex) . ": " . $ex->getMessage();
        $error =  $ex->getCode() . ": ". $ex->getMessage();
    }
    return array(
        "error"=>$error,
        "result"=>$result
    );
}

function getTransactionDetails($transactionId) {
    global $reporter;
    $result = array(
        "error"=>null,
        "transaction_id"=> $transactionId,
        "status"=>"",
        "data"=>null
    );
    try {
        $statusXml = $reporter->queryTransactionStatus($result['transaction_id']);

        $result['data'] = $statusXml;
        if (isset($_GET['debug'])) {
            file_put_contents("status.json", json_encode($statusXml));
        }
        $result['status'] = $statusXml->processingResults->processingResult->invoiceStatus;
        $result['statusText'] = $result['status'];
        if ($statusXml->processingResults->processingResult->invoiceStatus == "DONE") {
            $result['statusText'] = "Feldolgozva";
        } else if ($statusXml->processingResults->processingResult->invoiceStatus == "ABORTED") {
            $result['statusText'] = "Megszakítva";
        } else if($statusXml->processingResults->processingResult->invoiceStatus == "PROCESSING") {
            $result['statusText'] = "Feldolgozás alatt";
        } else if($statusXml->processingResults->processingResult->invoiceStatus == "RECEIVED") {
            $result['statusText'] = "Elfogadva";
        } else if($statusXml->processingResults->processingResult->invoiceStatus == "SAVED") {
            $result['statusText'] = "Elmentve";
        }
        if(isset($statusXml->processingResults->processingResult->businessValidationMessages->message) &&
            $statusXml->processingResults->processingResult->businessValidationMessages->message){
            $result['message'] = $statusXml->processingResults->processingResult->businessValidationMessages->message;
        }
    } catch(Exception $ex) {
        $result['error'] = get_class($ex) . ": " . $ex->getMessage();
    }
    return $result;
}

function getTransactionList($dateFrom, $dateTo){
    global $reporter;
    if (!$reporter) {
        return array("error"=>true,"message"=>"You need to provide NAV User");
    }
    try {
        if (!isset($dateFrom ) || !$dateFrom) {
            $dateFrom = strtotime("-1 days");
        } else {
            $dateFrom = strtotime($dateFrom);
        }
        if (!isset($dateTo ) || !$dateTo) {
            $dateTo = strtotime("tomorrow");
        } else {
            $dateTo = strtotime($dateTo);
        }
        $page = 1;
        if (isset($_GET['page']) && $_GET['page']) {
            $page = intval($_GET['page']);
        }

        $dateFrom = str_replace(' ', 'T', date('Y-m-d H:i:s', $dateFrom)) . 'Z';
        $dateTo = str_replace(' ', 'T', date('Y-m-d H:i:s', $dateTo)) . 'Z';
        $insDate = [
            "dateTimeFrom" => $dateFrom, //"2020-03-01T06:00:00Z"
            "dateTimeTo" => $dateTo,
        ];

        return $reporter->queryTransactionList($insDate, $page);
    } catch(Exception $ex) {
        //print get_class($ex) . ": " . $ex->getMessage();
        return array("error"=>true,"message"=>get_class($ex) . ": " . $ex->getMessage());
    }
}

function overrideNAVUser($arguments) {
    global $apiUrl;
    global $softwareData;
    global $config;
    global $reporter;

    if(isset($arguments['softwareId']) && isset($arguments['softwareName'])
        && isset($arguments['softwareOperation']) && isset($arguments['softwareMainVersion'])
        && isset($arguments['softwareDevName']) && isset($arguments['softwareDevContact'])
        && isset($arguments['softwareDevCountryCode']) && isset($arguments['softwareDevTaxNumber'])) {
        $softwareData = array(
            "softwareId" => $arguments['softwareId'],
            "softwareName" => $arguments['softwareName'],
            "softwareOperation" => $arguments['softwareOperation'],
            "softwareMainVersion" => $arguments['softwareMainVersion'],
            "softwareDevName" => $arguments['softwareDevName'],
            "softwareDevContact" => $arguments['softwareDevContact'],
            "softwareDevCountryCode" => $arguments['softwareDevCountryCode'],
            "softwareDevTaxNumber" => $arguments['softwareDevTaxNumber'],
        );
    }

    if(isset($arguments['navTaxNumber']) && $arguments['navTaxNumber'] &&
        isset($arguments['navLogin']) && $arguments['navLogin'] &&
        isset($arguments['navPassword']) && $arguments['navPassword'] &&
        isset($arguments['signKey']) && $arguments['signKey'] &&
        isset($arguments['exchangeKey']) && $arguments['exchangeKey']) {
        $userData = array(
            "login" => $arguments['navLogin'],
            "password" => $arguments['navPassword'],
            // "passwordHash" => "...", // Opcionális, a jelszó már SHA512 hashelt változata. Amennyiben létezik ez a változó, akkor az authentikáció során ezt használja
            "taxNumber" => $arguments['navTaxNumber'],
            "signKey" => $arguments['signKey'],
            "exchangeKey" => $arguments['exchangeKey'],
        );

        if (isset($arguments['navApiUrl']) && $arguments['navApiUrl']) {
            $apiUrl = $arguments['navApiUrl'];
        }
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