<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 10:31
 */

if (!isset($_SESSION["username"])) {
    die("Az oldal megtekintesehez be kell jelentkezned");
}
$isAPI = isset($_SESSION["api_mode"]) && $_SESSION["api_mode"] == true;

$maxId = "SELECT MAX(id) as last_id FROM invoice_list;";
$result = sqlGetFirst($maxId);
$lastId = "001";
$today = date("Y-m-d");
if($result && isset($result['last_id'])) {
    $lastId = $result['last_id'];
}
$len = strlen($lastId);
if($len < 2){
    $lastId = "00".$lastId;
} else if($len < 3) {
    $lastId = "0".$lastId;
}

$newInvoiceId = $today."/".$lastId;

global $technicalUser;
global $missingData;
global $missingDataList;

$supplierTaxpayerId = "";
$supplierVatCode = "";
$supplierCountyCode = "";
if(isset($technicalUser) && isset($technicalUser['supplierTaxNumber'])){
    $parts = explode("-", $technicalUser['supplierTaxNumber']);
    if(count($parts) < 3) {
        $missingData = true;
    }
    $supplierTaxpayerId = $parts[0];
    $supplierVatCode = $parts[1];
    $supplierCountyCode = $parts[2];
}
?>
<form id="invoiceForm" onsubmit="return false" method="post" style="max-width: 1270px; margin-left: auto; margin-right: auto;">
    <div class="top_outer_div">
        <h3 style="padding: 10px; display: none">Új számla (sorszám: <?php echo $newInvoiceId; ?>)</h3>

        <div class="row">
            <div class="col-md-12">
                <div class="lio-modal">
                    <div class="header">
                        <h5>
                            <?php if($isAPI) {
                                echo "Új számla (sorszám: ".$newInvoiceId.")";
                            } else {
                                echo "Számla beállítások";
                            } ?>
                            </h5>
                    </div>
                    <div class="body">
                        <div class="form-group invoice-header-group">
                            <div class="col-md-12">
                                <label for="nav_status">NAV Kapcsolat
                                    <input type="text" class="form-control" id="nav_status" name="nav_status" value="<?php
                                    $result = getNAVToken();
                                    if(!$result['error']){
                                        echo "Sikeres kapcsolat! Token: ". $result['result'];
                                    } else {
                                        if(strlen($result['error']) > 90){
                                            echo "Sikertelen: ".substr(str_replace("\n","",stripslashes($result['error'])), 0,90);
                                        } else {
                                            echo "Sikertelen: ".str_replace("\n","",stripslashes($result['error']));
                                        }

                                    }
                                    ?>" disabled>
                                </label>


                            </div>
                            <div class="col-md-3" <?php if($isAPI) {echo 'style="display:none"';}?>>
                                <label for="full_name">Kiállító
                                    <select class="form-control" id="technicalUser" name="invoiceTechnicalUser">
                                        <?php echo getTechnicalUsersAsOptions($_GET['user']); ?>
                                    </select>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label for="full_name">Számla Típus
                                    <select class="form-control" id="invoiceCategory" name="invoiceCategory">
                                        <option value="SIMPLIFIED">Egyszerüsített</option>
                                        <option value="NORMAL">Normál</option>
                                        <option value="AGGREGATE" disabled style="display: none">AGGREGATE</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label for="full_name">Számla Megjelenés
                                    <select class="form-control" id="invoiceAppearance" name="invoiceAppearance">
                                        <option value="PAPER">Papír alapú</option>
                                        <option value="ELECTRONIC">Elektronikus</option>
                                        <option value="EDI">EDI</option>
                                        <option value="UNKNOWN">Ismeretlen</option>
                                    </select>
                                </label>
                            </div>

                            <div class="col-md-3" <?php if(!$isAPI) {echo 'style="display:none"';}?>>
                                <label for="full_name">Pénznem
                                    <select class="form-control" id="currencyCode" name="currencyCode">
                                        <option value="HUF">HUF</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label for="full_name">Fizetési Mód
                                    <select class="form-control" id="paymentMethod" name="paymentMethod">
                                        <option value="CASH">Készpénz</option>
                                        <option value="TRANSFER">Átutalás</option>
                                        <option value="CARD">Bankkártya</option>
                                        <option value="VOUCHER">VOUCHER</option>
                                        <option value="Other">Egyéb</option>
                                    </select>
                                </label>
                            </div>

                            <div class="col-md-3">
                                <label for="full_name">Számla Kelte
                                    <input type="date" class="form-control" id="invoiceIssueDate" name="invoiceIssueDate" value="<?php echo $today; ?>">
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label for="full_name">Számla Teljesítés
                                    <input type="date" class="form-control" id="invoiceDeliveryDate" name="invoiceDeliveryDate" value="<?php echo $today; ?>">
                                </label>
                            </div>
                            <div class="col-md-3" style="display: none">
                                <label for="full_name">Számla Sorszáma
                                    <input type="text" class="form-control" id="invoiceNumber" name="invoiceNumber" value="<?php echo $newInvoiceId; ?>">
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<div class="top_outer_div">

    <div class="row">
        <div class="col-md-6">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">
                        <span class="back-icon" style="margin-top: -5px;height: 1.3em;width: 1.3em;" onclick="location.href='./plugin.php?name=invoices'"></span>
                        Kiállító</h5>
                </div>
                <div class="body">
                    <div class="form-group">
                        <div class="col-md-12">
                        <label for="full_name">Teljes Név
                            <input type="text" class="form-control" name="supplierName" id="supplierName"
                                   placeholder="Teljes név" value="<?php echo getDataIfThere($technicalUser, "supplierName"); ?>" required/></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                        <label>Adószám
                            <input type="text" class="form-control" placeholder="11111111-2-42" id="supplierTaxNumber" value="<?php echo getDataIfThere($technicalUser, "supplierTaxNumber"); ?>" name="supplierTaxNumber" maxlength="13" pattern="[0-9]{8}[-]{1}[0-9]{1}[-]{1}[0-9]{2}">
                            <input type="hidden" id="supplierTaxpayerId" name="supplierTaxpayerId" value="<?php echo getDataIfThere($technicalUser, "supplierTaxpayerId") ?>">
                            <input type="hidden" id="supplierVatCode" name="supplierVatCode" value="<?php echo getDataIfThere($technicalUser, "supplierVatCode") ?>">
                            <input type="hidden" id="supplierCountyCode" name="supplierCountyCode" value="<?php echo getDataIfThere($technicalUser, "supplierCountyCode") ?>">
                        </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="supplierPostCode">Irányítószám
                                <input type="number" min="1000" max="9999" alt="PostCode" class="form-control" value="<?php echo getDataIfThere($technicalUser, "supplierPostCode"); ?>" name="supplierPostCode" id="supplierPostCode" placeholder="8900">
                            </label>
                        </div>
                        <div class="col-md-8">
                            <label for="supplierTown">Település
                                <input type="text" class="form-control" name="supplierTown" id="supplierTown" value="<?php echo getDataIfThere($technicalUser, "supplierTown"); ?>"
                                       placeholder="Zalaegerszeg" required/></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="supplierStreetName">Közterület
                                <input type="text" class="form-control" name="supplierStreetName" id="supplierStreetName" value="<?php echo getDataIfThere($technicalUser, "supplierStreetName"); ?>"
                                       placeholder="Kossuth" required/></label>
                        </div>
                        <div class="col-md-3">
                            <label for="supplierStreet">Jelleg
                                <select class="form-control" id="supplierStreet" name="supplierStreet">
                                    <option value="<?php echo getDataIfThere($technicalUser, "supplierStreet"); ?>"><?php echo getDataIfThere($technicalUser, "supplierStreet"); ?></option>
                                    <option value="út">út</option>
                                    <option value="utca">utca</option>
                                    <option value="útja">útja</option>
                                    <option value="allé">allé</option>
                                    <option value="alsó rakpart">alsó rakpart</option>
                                    <option value="alsósor">alsósor</option>
                                    <option value="bekötőút">bekötőút</option>
                                    <option value="dűlő">dűlő</option>
                                    <option value="fasor">fasor</option>
                                    <option value="felső rakpart">felső rakpart</option>
                                    <option value="felsősor">felsősor</option>
                                    <option value="főtér">főtér</option>
                                    <option value="főút">főút</option>
                                    <option value="gát">gát</option>
                                    <option value="határ">határ</option>
                                    <option value="határsor">határsor</option>
                                    <option value="határút">határút</option>
                                    <option value="ipartelep">ipartelep</option>
                                    <option value="kert">kert</option>
                                    <option value="kertsor">kertsor</option>
                                    <option value="korzó">korzó</option>
                                    <option value="környék">környék</option>
                                    <option value="körönd">körönd</option>
                                    <option value="körtér">körtér</option>
                                    <option value="körút">körút</option>
                                    <option value="köz">köz</option>
                                    <option value="lakópark">lakópark</option>
                                    <option value="lakótelep">lakótelep</option>
                                    <option value="lejtő">lejtő</option>
                                    <option value="lépcső">lépcső</option>
                                    <option value="lépcsősor">lépcsősor</option>
                                    <option value="liget">liget</option>
                                    <option value="major">major</option>
                                    <option value="mélyút">mélyút</option>
                                    <option value="negyed">negyed</option>
                                    <option value="oldal">oldal</option>
                                    <option value="országút">országút</option>
                                    <option value="park">park</option>
                                    <option value="part">part</option>
                                    <option value="pincesor">pincesor</option>
                                    <option value="puszta">puszta</option>
                                    <option value="rakpart">rakpart</option>
                                    <option value="sétány">sétány</option>
                                    <option value="sikátor">sikátor</option>
                                    <option value="sor">sor</option>
                                    <option value="sugárút">sugárút</option>
                                    <option value="szállás">szállás</option>
                                    <option value="szektor">szektor</option>
                                    <option value="szél">szél</option>
                                    <option value="szer">szer</option>
                                    <option value="sziget">sziget</option>
                                    <option value="szőlőhegy">szőlőhegy</option>
                                    <option value="tag">tag</option>
                                    <option value="tanya">tanya</option>
                                    <option value="telep">telep</option>
                                    <option value="tér">tér</option>
                                    <option value="tető">tető</option>
                                    <option value="udvar">udvar</option>
                                    <option value="üdülőpart">üdülőpart</option>
                                    <option value="üdülősor">üdülősor</option>
                                    <option value="üdülőtelep">üdülőtelep</option>
                                    <option value="vár">vár</option>
                                    <option value="várkert">várkert</option>
                                    <option value="város">város</option>
                                    <option value="villasor">villasor</option>
                                    <option value="völgy">völgy</option>
                                    <option value="zug">zug</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="supplierAddress">Házszám
                                <input type="text" class="form-control" placeholder="20/a" id="supplierAddress" name="supplierAddress" value="<?php echo getDataIfThere($technicalUser, "supplierAddress"); ?>">
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="supplierBankAccountNumber">Bankszámlaszám
                                <input type="text" class="form-control" placeholder="88888888-66666666-12345678" id="supplierBankAccountNumber" name="supplierBankAccountNumber" value="<?php echo getDataIfThere($technicalUser, "supplierBankAccountNumber"); ?>" pattern="[0-9]{8}[-][0-9]{8}[-][0-9]{8}|[0-9]{8}[-][0-9]{8}|[A-Z]{2}[0-9]{2}[0-9A-Za-z]{11,30}">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Vevő</h5>
                </div>
                <div class="body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="customerVatStatus">Adózói Státusz
                                <select class="form-control" id="customerVatStatus" name="customerVatStatus">
                                    <option value="PRIVATE_PERSON">Magánszemély</option>
                                    <option value="DOMESTIC">Belföldi adózó</option>
                                </select>
                            </label>

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="customerName">Teljes Név
                                <input type="text" class="form-control" name="customerName" id="customerName"
                                       placeholder="Teljes név" required/></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="customerPostCode">Irányítószám
                                <input type="number" min="1000" max="9999" alt="PostCode" class="form-control" name="customerPostCode" id="customerPostCode" placeholder="8900">
                            </label>
                        </div>
                        <div class="col-md-8">
                            <label for="customerTown">Település
                                <input type="text" class="form-control" placeholder="Zalaegerszeg" id="customerTown" name="customerTown">
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="customerStreetName">Közterület
                                <input type="text" class="form-control" placeholder="Arany János" id="customerStreetName" name="customerStreetName">
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="customerStreet">Jelleg
                                <select class="form-control" id="customerStreet" name="customerStreet">
                                    <option value="út">út</option>
                                    <option value="utca">utca</option>
                                    <option value="útja">útja</option>
                                    <option value="allé">allé</option>
                                    <option value="alsó rakpart">alsó rakpart</option>
                                    <option value="alsósor">alsósor</option>
                                    <option value="bekötőút">bekötőút</option>
                                    <option value="dűlő">dűlő</option>
                                    <option value="fasor">fasor</option>
                                    <option value="felső rakpart">felső rakpart</option>
                                    <option value="felsősor">felsősor</option>
                                    <option value="főtér">főtér</option>
                                    <option value="főút">főút</option>
                                    <option value="gát">gát</option>
                                    <option value="határ">határ</option>
                                    <option value="határsor">határsor</option>
                                    <option value="határút">határút</option>
                                    <option value="ipartelep">ipartelep</option>
                                    <option value="kert">kert</option>
                                    <option value="kertsor">kertsor</option>
                                    <option value="korzó">korzó</option>
                                    <option value="környék">környék</option>
                                    <option value="körönd">körönd</option>
                                    <option value="körtér">körtér</option>
                                    <option value="körút">körút</option>
                                    <option value="köz">köz</option>
                                    <option value="lakópark">lakópark</option>
                                    <option value="lakótelep">lakótelep</option>
                                    <option value="lejtő">lejtő</option>
                                    <option value="lépcső">lépcső</option>
                                    <option value="lépcsősor">lépcsősor</option>
                                    <option value="liget">liget</option>
                                    <option value="major">major</option>
                                    <option value="mélyút">mélyút</option>
                                    <option value="negyed">negyed</option>
                                    <option value="oldal">oldal</option>
                                    <option value="országút">országút</option>
                                    <option value="park">park</option>
                                    <option value="part">part</option>
                                    <option value="pincesor">pincesor</option>
                                    <option value="puszta">puszta</option>
                                    <option value="rakpart">rakpart</option>
                                    <option value="sétány">sétány</option>
                                    <option value="sikátor">sikátor</option>
                                    <option value="sor">sor</option>
                                    <option value="sugárút">sugárút</option>
                                    <option value="szállás">szállás</option>
                                    <option value="szektor">szektor</option>
                                    <option value="szél">szél</option>
                                    <option value="szer">szer</option>
                                    <option value="sziget">sziget</option>
                                    <option value="szőlőhegy">szőlőhegy</option>
                                    <option value="tag">tag</option>
                                    <option value="tanya">tanya</option>
                                    <option value="telep">telep</option>
                                    <option value="tér">tér</option>
                                    <option value="tető">tető</option>
                                    <option value="udvar">udvar</option>
                                    <option value="üdülőpart">üdülőpart</option>
                                    <option value="üdülősor">üdülősor</option>
                                    <option value="üdülőtelep">üdülőtelep</option>
                                    <option value="vár">vár</option>
                                    <option value="várkert">várkert</option>
                                    <option value="város">város</option>
                                    <option value="villasor">villasor</option>
                                    <option value="völgy">völgy</option>
                                    <option value="zug">zug</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="customerAddress">Házszám
                                <input type="text" class="form-control" placeholder="20/a" id="customerAddress" name="customerAddress">
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label>Adószám
                                <input type="text" class="form-control" placeholder="11111111-2-42" id="customerTaxNumber" name="customerTaxNumber" maxlength="13" pattern="[0-9]{8}[-]{1}[0-9]{1}[-]{1}[0-9]{2}">
                                <input type="hidden" id="customerTaxpayerId" name="customerTaxpayerId" >
                                <input type="hidden" id="customerVatCode" name="customerVatCode" >
                                <input type="hidden" id="customerCountyCode" name="customerCountyCode" >
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="application/javascript">
            <?php
            if($missingData == true){
                echo "Demiran.alert('Hiányzó adatok a Technikai felhasználónál: <br>".implode("<br>",$missingDataList)."');";
            }
            if($result['error']){
                echo 'console.log("'.str_replace("\n","",stripslashes($result['error'])).'");';
            }
            ?>
        </script>

    </div>

</div>


<div class="top_outer_div">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="body">
                    <table class="invoiceTable">
                        <thead>
                        <tr>
                            <th class="lineNatureIndicator">Típus</th>
                            <th class="productCodeCategory advanced">Kód Típus</th>
                            <th class="productCodeValue">Kód</th>
                            <th class="lineDescription">Megnevezés</th>
                            <th class="quantity">Mennyiség</th>
                            <th class="unitOfMeasure">Egység</th>
                            <th class="unitPrice">Nettó Egységár</th>
                            <th class="lineNetAmount">Nettó Érték</th>
                            <th class="vatPercentage">ÁFA(%)</th>
                            <th class="lineVatAmount">ÁFA(Ft)</th>
                            <th class="lineGrossAmountNormal">Bruttó Ár</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>



                        <tr class="add_new_item">
                            <td><select class="form-control" id="lineNatureIndicator">
                                    <option value="SERVICE">Szolgáltatás</option>
                                    <option value="PRODUCT">Termék</option>
                                    <option value="OTHER">Egyéb</option>
                                </select></td>
                            <td class="advanced"><select class="form-control" id="productCodeCategory">
                                    <option value="OWN">OWN</option>
                                    <option value="VTSZ">VTSZ</option>
                                    <option value="SZJ">SZJ</option>
                                    <option value="KN">KN</option>
                                    <option value="AHK">AHK</option>
                                    <option value="CSK">CSK</option>
                                    <option value="KT">KT</option>
                                    <option value="EJ">EJ</option>
                                    <option value="TESZOR">TESZOR</option>

                                    <option value="OTHER">OTHER</option>
                                </select></td>
                            <td><input type="text" class="form-control" placeholder="WF001" id="productCodeValue" style="max-width: 120px;"></td>
                            <td><input type="text" class="form-control" placeholder="Weboldal Fejlesztés" id="lineDescription"></td>
                            <td><input type="number" min="1" class="form-control" placeholder="1" id="quantity" style="max-width: 80px;"></td>
                            <td><select class="form-control" id="unitOfMeasure" style="max-width: 70px;">
                                    <option value="PIECE">DB</option>
                                    <option value="KILOGRAM">KG</option>
                                    <option value="TON">T</option>
                                    <option value="KWH">KWh</option>
                                    <option value="DAY">Nap</option>
                                    <option value="HOUR">Óra</option>
                                    <option value="MINUTE">Perc</option>
                                    <option value="MONTH">Hónap</option>
                                    <option value="LITER">Liter</option>
                                    <option value="KILOMETER">Km</option>
                                    <option value="CUBIC_METER">KB3</option>
                                    <option value="METER">Méter</option>
                                    <option value="LINEAR_METER">Folyóméter</option>
                                    <option value="CARTON">Karton</option>
                                    <option value="PACK">Csomag</option>
                                    <option value="OWN">Egyéni</option>
                                </select></td>


                            <td><input type="number" min="1" class="form-control" placeholder="400" id="unitPrice" style="max-width: 130px;"><span class="huf">Ft</span> </td>
                            <td><input type="number" min="1" class="form-control" placeholder="600000" id="lineNetAmountData" style="max-width: 130px;"><span class="huf">Ft</span></td>

                            <td>
                                <select class="form-control" id="lineVatRate">
                                    <option value="0.2126">27%</option>
                                    <option value="0.1525">18%</option>
                                    <option value="0.0476">5%</option>
                                    <option value="0">0</option>
                                    <option value="0">TAM</option>
                                </select>

                            </td>
                            <td><input type="number" min="1" class="form-control" placeholder="30000" step="0.01" id="lineVatData" style="max-width: 130px;"><span class="huf">Ft</span></td>
                            <td><input type="number" min="1" class="form-control" placeholder="630000" step="0.01" id="lineGrossAmountData" style="max-width: 130px;"><span class="huf">Ft</span></td>
                            <td><button class="btn btn-default addButton" onclick="return addItem()" style="display: inline-block ;    border: 1px solid;">+</button></td>
                        </tr>

                        </tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="top_outer_div">
    <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Összesítés</h5>
                </div>
                <div class="body">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label id="vatLabel">ÁFA tartalom %
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label id="vatAmountLabel">Összesen Ft
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                       
                        <div class="col-md-12">
                            <h3 id="sumAmount" style="text-align: right">0 Ft</h3>
                        </div>
                    </div>

                </div>
                <div class="footer">
                    <button class="btn btn-outline-black" onclick="downloadXML()">XML Letöltés</button>
                    <button class="btn btn-outline-black" onclick="sendNAV()">Beküldés</button>

                </div>
            </div>
        </div>

    </div>
</div>
<input type="hidden" name="_plugin" value="invoices">
<script type="application/javascript">

    const generateSelectOptions = function (array){
        const select = document.createElement('select');
        array.forEach(function (element){
            if(element.name && element.value) {
                const option = document.createElement('option');
                option.value = element.value;
                option.innerHTML = element.name;
                select.appendChild(option);
            }
        });
        return select;
    };

    const setUnitPrice = function(){
        const invoiceCategory = document.getElementById("invoiceCategory");
        const unitPrice = document.querySelector("tr th.unitPrice");
        const lineVatRate = document.querySelector("#lineVatRate");
        if(invoiceCategory && unitPrice){
            switch(invoiceCategory.value){
                case "SIMPLIFIED":
                    unitPrice.innerHTML = "Br. Egységár";
                    if(lineVatRate){
                        lineVatRate.innerHTML = generateSelectOptions(
                            [
                                {name:"AAM", value: "0"},
                                {name:"27%", value: "0.2126"},
                                {name:"18%", value: "0.1525"},
                                {name:"5%", value: "0.0476"},
                                {name:"0", value: "0"},
                                {name:"TAM", value: "0"}

                            ]
                        ).innerHTML;
                    }
                    break;
                case "NORMAL":
                    unitPrice.innerHTML = "Nt. Egységár";
                    if(lineVatRate){
                        lineVatRate.innerHTML = generateSelectOptions(
                            [
                                {name:"AAM", value: "0"},
                                {name:"27%", value: "0.27"},
                                //{name:"25%", value: "0.25"},
                                //{name:"20%", value: "0.20"},
                                {name:"18%", value: "0.18"},
                                //{name:"12%", value: "0.12"},
                                //{name:"7%", value: "0.07"},
                                {name:"5%", value: "0.05"},
                                {name:"0%", value: "0"},
                                {name:"TAM", value: "0"}
                            ]
                        ).innerHTML;
                    }
                    break;
            }
        }
    };

    const invoiceCategory = document.getElementById("invoiceCategory");
    if(invoiceCategory){
        invoiceCategory.onchange = setUnitPrice;
        setUnitPrice();
    }

    const applyTaxNumbers = function(prefix){
        if(!prefix){
            prefix = "supplier";
        }
        const TaxNumber = document.getElementById(prefix+"TaxNumber");
        const TaxpayerId = document.getElementById(prefix+"TaxpayerId");
        const VatCode = document.getElementById(prefix+"VatCode");
        const CountyCode = document.getElementById(prefix+"CountyCode");
        TaxNumber.onchange = function (){
            const parts = TaxNumber.value.split("-");
            if(parts.length === 3 && parts[0].length === 8 && parts[1].length === 1 && parts.length === 2){
                TaxpayerId.value = parts.shift();
                VatCode.value = parts.shift();
                CountyCode.value = parts.shift();
            }
        }
    };
    applyTaxNumbers("customer");
    applyTaxNumbers("supplier");

    const customerVatStatus = document.querySelector("#customerVatStatus");
    if(customerVatStatus){
        const changeVatStatus = function(){
            const customerVatStatus = document.querySelector("#customerVatStatus");
            const customerTaxNumber = document.querySelector("#customerTaxNumber");

            if(customerVatStatus && customerTaxNumber){
                const vatStatus = customerVatStatus.value;

                switch(vatStatus){
                    case "DOMESTIC":
                        customerTaxNumber.parentElement.parentElement.parentElement.style.display = null;
                        break;
                    case "PRIVATE_PERSON":
                        customerTaxNumber.parentElement.parentElement.parentElement.style.display = "none";
                        break;
                }
            }
        };
        changeVatStatus();
        customerVatStatus.onchange = changeVatStatus;
    }

    const inputStyles = {
        verifyValue: function (input) {
            if (!input) {
                return false;
            }
            const value = input.value;
            const valueAttribute = input.getAttribute("value");
            const valueWrong = !value && typeof value !== "number";
            const valueAttributeWrong = !valueAttribute && typeof valueAttribute !== "number";

            const allowedTypes = ["customerTaxNumber","supplierBankAccountNumber","vevo_email","megjegyzes","bankszamla"];
            if(input.id === "productCodeValue" && input.value && input.value.length < 3){
                input.style.border = "1px solid red";
                return false;
            }
            if (allowedTypes.includes(input.id)) {
                return true;
            }
            if(valueWrong && valueAttributeWrong) {
                input.style.border = "1px solid red";
                return false;
            } else {
                input.style.border = null;
                return true;
            }
        }
    };

    const getInvoiceTableItems = function() {
        const table = document.querySelector(".invoiceTable");
        if(!table){
            return;
        }
        const lines = table.querySelectorAll("tr");
        if (lines.length < 3) {
            return [];
        }
        const product_details = [
            "lineNatureIndicator",
            "productCodeCategory",
            "productCodeValue",
            "lineDescription",
            "quantity",
            "unitOfMeasure",
            "unitPrice",
            "lineNetAmount",
            "vatPercentage",
            "lineVatAmount",
            "lineGrossAmountNormal"];

        const items = [];
        lines.forEach((line,index) => {
            if (index && index < lines.length - 1) {
                const fields = line.querySelectorAll("td");
                let fieldData = {};
                fields.forEach((field,index) => {
                    if (product_details[index]) {
                        if(product_details[index] === "productCodeValue") {
                            fieldData[product_details[index]] = field.querySelector("input").value.toUpperCase();
                        } else {
                            fieldData[product_details[index]] = field.querySelector("input").value;
                        }
                        if(product_details[index] === "vatPercentage") {
                            const exceptionInput = field.querySelectorAll("input")[1];
                            if(exceptionInput) {
                                const value = exceptionInput.value;
                                if(value){
                                    fieldData.lineVatRate_exception = value;
                                }
                            }
                        }
                    }
                });
                items.push(fieldData);
            }

        });
        return items;
    };
    const calculateSummary = function() {
        const items = getInvoiceTableItems();
        let summaryDetails = {
            vatRates:{

            },
            invoiceNetAmount: 0.00,
            invoiceNetAmountHUF: 0.00,
            invoiceVatAmount: 0.00,
            invoiceVatAmountHUF: 0.00,
            invoiceGrossAmount:0.00,
            invoiceGrossAmountHUF:0.00

        };
        items.forEach(item => {
            if (!item) {
                return;
            }

            item.vatRate = Number.parseFloat(item["vatPercentage"]) || 0;

            const vatRateCategory = item["lineVatRate_exception"] ? item["lineVatRate_exception"] : item["vatPercentage"];
            if(!summaryDetails.vatRates[vatRateCategory]) {
                summaryDetails.vatRates[vatRateCategory] = {
                    vatPercentage : Number(item["vatPercentage"]),
                    vatRate : item.vatRate,
                    vatException : item["lineVatRate_exception"] || null,
                    vatRateNetAmount : Number(item["lineNetAmount"]),
                    vatRateNetAmountHUF : Number(item["lineNetAmount"]),
                    vatRateVatAmount : Number(item["lineVatAmount"]),
                    vatRateVatAmountHUF : Number(item["lineVatAmount"]),
                    vatRateGrossAmount : Number(item["lineGrossAmountNormal"]),
                    vatRateGrossAmountHUF : Number(item["lineGrossAmountNormal"]),
                };
                switch(item["lineVatRate_exception"]){
                    case "TAM":
                        summaryDetails.vatRates[vatRateCategory].exceptionReason = "Adómentes ÁFA tv. 86.§ (1)";
                        break;
                    case "AAM":
                        summaryDetails.vatRates[vatRateCategory].exceptionReason = "Alanyi adómentes";
                        break;
                    case "KBAET":
                        summaryDetails.vatRates[vatRateCategory].exceptionReason = "adómentes Közösségen belüli termékértékesítés";
                        break;
                }
            } else {

                summaryDetails.vatRates[vatRateCategory].vatRateNetAmount += Number(item["lineNetAmount"]);
                summaryDetails.vatRates[vatRateCategory].vatRateNetAmountHUF += Number(item["lineNetAmount"]);
                summaryDetails.vatRates[vatRateCategory].vatRateVatAmount += Number(item["lineVatAmount"]);
                summaryDetails.vatRates[vatRateCategory].vatRateVatAmountHUF += Number(item["lineVatAmount"]);
                summaryDetails.vatRates[vatRateCategory].vatRateGrossAmount += Number(item["lineGrossAmountNormal"]);
                summaryDetails.vatRates[vatRateCategory].vatRateGrossAmountHUF += Number(item["lineGrossAmountNormal"]);
            }

            summaryDetails.invoiceNetAmount += Number(item["lineNetAmount"]);
            summaryDetails.invoiceNetAmountHUF += Number(item["lineNetAmount"]);
            summaryDetails.invoiceVatAmount += Number(item["lineVatAmount"]);
            summaryDetails.invoiceVatAmountHUF += Number(item["lineVatAmount"]);
            summaryDetails.invoiceGrossAmount += Number(item["lineGrossAmountNormal"]);
            summaryDetails.invoiceGrossAmountHUF += Number(item["lineGrossAmountNormal"]);

        });
        summaryDetails.invoiceNetAmount = summaryDetails.invoiceNetAmount.toFixed(2);
        summaryDetails.invoiceNetAmountHUF = summaryDetails.invoiceNetAmountHUF.toFixed(2);
        summaryDetails.invoiceVatAmount = summaryDetails.invoiceVatAmount.toFixed(2);
        summaryDetails.invoiceVatAmountHUF = summaryDetails.invoiceVatAmountHUF.toFixed(2);
        summaryDetails.invoiceGrossAmount = summaryDetails.invoiceGrossAmount.toFixed(2);
        summaryDetails.invoiceGrossAmountHUF = summaryDetails.invoiceGrossAmountHUF.toFixed(2);

        // Clear VATNodes
        const vatAmountLabel = document.getElementById('vatAmountLabel');
        const vatLabel = document.getElementById('vatLabel');
        if(vatAmountLabel && vatLabel) {
            vatAmountLabel.querySelectorAll("input").forEach(n=>n.outerHTML = "");
            vatLabel.querySelectorAll("input").forEach(n=>n.outerHTML = "");

            Object.keys(summaryDetails.vatRates).forEach(rate=>{
                const data = summaryDetails.vatRates[rate];
                const vatRateGrossAmount = data.vatRateGrossAmount;
                const vatPercentage = (data.vatPercentage * 100).toFixed(2);

                const inputPercentage = document.createElement("input");
                inputPercentage.setAttribute("type", "text");
                inputPercentage.classList.add("form-control");
                inputPercentage.disabled = true;
                if(data.vatException) {
                    inputPercentage.value = "0% (" + data.vatException + ")";
                } else {
                    inputPercentage.value = vatPercentage + "%";
                }

                vatLabel.appendChild(inputPercentage);

                const inputAmount = document.createElement("input");
                inputAmount.setAttribute("type", "text");
                inputAmount.classList.add("form-control");
                inputAmount.disabled = true;
                inputAmount.value = vatRateGrossAmount + " Ft";
                vatAmountLabel.appendChild(inputAmount);
            });

        }
        const sumAmount = document.getElementById("sumAmount");
        if(sumAmount){
            sumAmount.innerHTML = summaryDetails.invoiceGrossAmount + " Ft";
        }

        return summaryDetails;
    };

    const addItem =
        function(){
        const table = document.querySelector(".invoiceTable");
        if(!table){
            return false;
        }
        const addItemTr = table.querySelector("tr.add_new_item");
        if(!addItemTr){
            return false;
        }

        const tds = addItemTr.querySelectorAll("td");
        const values = [];
        const ids = [];
        const titles = [];
        let missing = false;
        tds.forEach((td,index) => {
            if (index !== tds.length-1) {
                const input = td.querySelector("input, select");
                if (inputStyles.verifyValue(input)) {
                    if (input.id === "lineVatRate") {
                        const num = parseFloat(input.value);
                        if (!Number.isNaN(num) && num) {
                            values.push(input.value);
                            //values.push((num * 100).toFixed(2));
                            ids.push(input.id);
                            if(!input.options[input.selectedIndex].text.includes("%")) {
                                titles.push(input.options[input.selectedIndex].text);
                            } else {
                                titles.push(null);
                            }
                            return
                        }

                    }
                    if(input.tagName.toLowerCase() === "select") {
                        titles.push(input.options[input.selectedIndex].text);
                    } else {
                        titles.push(null);
                    }
                    values.push(input ? input.value : "");
                    ids.push(input.id);
                } else {
                    missing = true;
                }
            }
        });
        if (missing) {
            Demiran.alert("Kérlek töltsd ki az összes mezőt helyesen", "Hiányzó értékek");
            return false;
        }

        //Create TRs
        const tr = document.createElement("tr");
        tr.classList.add("product_line");
        values.forEach((value,i)=>{
            const td = document.createElement("td");
            const exception = document.createElement("input");
            exception.type = "hidden";
            exception.setAttribute("name", ids[i] + "_exception[]");
            if(titles[i]){
                td.innerHTML = titles[i];
                if(ids[i] === "lineVatRate") {
                    exception.value = titles[i];
                }

            } else {
                td.innerHTML = value;
            }

            if(i === 1){
                td.classList.add("advanced");
            }
            const input = document.createElement("input");
            input.type = "hidden";
            input.value = value;
            input.setAttribute("name", ids[i] + "[]");
            td.appendChild(input);
            td.appendChild(exception);
            tr.appendChild(td);
        });

        const td = document.createElement("td");
        const button = document.createElement("button");
        button.setAttribute("type","button");
        button.setAttribute("class","close");
        button.setAttribute("aria-label","Close");
        button.innerHTML = "<span aria-hidden=\"true\">&times;</span>";
        button.onclick = (e)=>{
            let target = e.target;
            if (target.tagName.toLowerCase() === "span") {
                target = target.parentElement;
            }
            let tr = target.parentElement.parentElement;
            if (tr.tagName.toLowerCase() === "td") {
                tr = tr.parentElement;
            } else if (tr.tagName.toLowerCase() === "tbody") {
                tr = target.parentElement;
            }

            if (tr.tagName.toLowerCase() === "tr") {
                Demiran.confirm("Biztosan törölni szeretnéd ezt a tételt?", "Törlés Jóváhagyása", result => {
                    if (result) {
                        tr.outerHTML = "";
                        if(!table.querySelectorAll("tr.product_line").length) {
                            const vatRate = document.querySelector("#lineVatRate");
                            const invoiceCategory = document.querySelector("#invoiceCategory");
                            if (vatRate && invoiceCategory) {
                                invoiceCategory.disabled = false;
                                //vatRate.disabled = false;
                            }
                        }
                        calculateSummary();
                    }
                })
            }
        };

        td.appendChild(button);
        tr.appendChild(td);
        //Put TR into the table;
        (table.querySelector("tbody") || table).insertBefore(tr,addItemTr);

        // Remove data from inputs
        tds.forEach((td,index) => {
            if (index !== tds.length-1) {
                const input = td.querySelector("input, select");
                if (input.tagName.toLowerCase() === "input") {
                    //if (input.id !== "lineVatRate") {
                        input.value = null;
                    //}
                } else {
                    /*if (input.id !== "lineVatRate") {
                        input.selectedIndex = 0;
                    } else {
                        input.disabled = true;
                    }*/
                }
            }
        });
        const invoiceCategory = document.querySelector("#invoiceCategory");
        if (invoiceCategory) {
            invoiceCategory.disabled = true;
        }
        calculateSummary();
        return false;
    };

    const calculateTaxes = function(){
        const table = document.querySelector(".invoiceTable");
        if(!table){
            return;
        }
        const addItemTr = table.querySelector("tr.add_new_item");
        if(!addItemTr){
            return;
        }

        const quantityNode = addItemTr.querySelector("#quantity");
        const unitPriceNode = addItemTr.querySelector("#unitPrice");
        const lineNetAmountDataNode = addItemTr.querySelector("#lineNetAmountData");
        const lineVatRateNode = addItemTr.querySelector("#lineVatRate");
        const lineVatDataNode = addItemTr.querySelector("#lineVatData");
        const lineGrossAmountDataNode = addItemTr.querySelector("#lineGrossAmountData");
        const invoiceCategory = document.querySelector("#invoiceCategory");

        if(quantityNode && unitPriceNode && lineNetAmountDataNode && lineVatRateNode && lineVatDataNode &&
        lineGrossAmountDataNode && invoiceCategory) {
            const calculateFromUnitPrice = function () {
                if (quantityNode.value && quantityNode.value.includes(",")) {
                    quantityNode.value = quantityNode.value.replace(",",".");
                }
                if (unitPriceNode.value && unitPriceNode.value.includes(",")) {
                    unitPriceNode.value = unitPriceNode.value.replace(",",".");
                }

                const lineNetAmountData = parseInt(lineNetAmountDataNode.value);
                const lineVatRate = parseFloat(lineVatRateNode.value);
                const ratePercentage  = parseFloat((lineVatRate*100).toFixed(2));

                const invoiceCategoryValue = invoiceCategory.value || invoiceCategory.getAttribute("value") || null;
                const quantity = parseFloat(quantityNode.value);
                const unitPrice = parseInt(unitPriceNode.value);

                if (invoiceCategoryValue === "SIMPLIFIED") {
                    //lineGrossAmountDataNode.value = lineNetAmountData;
                    if (!Number.isNaN(quantity) && !Number.isNaN(unitPrice)) {
                        console.log("Brutto Ar: ", quantity * unitPrice);
                        lineGrossAmountDataNode.value = quantity * unitPrice
                    } else {
                        console.log(quantity,unitPrice);
                    }
                    const lineGrossAmountData = parseFloat(lineGrossAmountDataNode.value);
                    if (!Number.isNaN(lineGrossAmountData) && !Number.isNaN(lineVatRate)) {
                        console.log(ratePercentage);
                        const vatValue = (lineGrossAmountData * lineVatRate).toFixed(2);

                        lineVatDataNode.value = vatValue;
                        lineNetAmountDataNode.value = lineGrossAmountData - vatValue;
                    }
                }else{
                    if (!Number.isNaN(quantity) && !Number.isNaN(unitPrice)) {
                        lineNetAmountDataNode.value = quantity * unitPrice
                    } else {
                        console.log(quantity,unitPrice);
                    }

                    if (!Number.isNaN(lineNetAmountData) && !Number.isNaN(lineVatRate)) {
                        console.log(ratePercentage);
                        lineVatDataNode.value = (lineNetAmountData * lineVatRate).toFixed(2)
                    }

                    const lineVatData = parseFloat(lineVatDataNode.value);


                    lineGrossAmountDataNode.value = lineVatData + lineNetAmountData;

                }
            };
            quantityNode.onchange = calculateFromUnitPrice;
            unitPriceNode.onchange = calculateFromUnitPrice;
            lineVatRateNode.onchange = calculateFromUnitPrice;
        } else {
            console.log(quantityNode,unitPriceNode,lineNetAmountDataNode, lineVatRateNode, lineVatDataNode, lineGrossAmountDataNode);
        }

    };

    calculateTaxes();

    const downloadXML = function (){
        const invoiceForm = document.getElementById('invoiceForm');
        if(invoiceForm && invoiceForm instanceof HTMLFormElement){
            const disabledNodes = document.querySelectorAll("[disabled]");
            disabledNodes.forEach(function(node){
                node.disabled = !node.disabled;
            });
            const formData = new FormData(invoiceForm);
            disabledNodes.forEach(function(node){
                node.disabled = !node.disabled;
            });
            Demiran.confirm("XML Adatok letöltése","Biztosan letöltöd az adatokat?", result=>{
                if(result){
                    Demiran.call("generate_xml", formData, function(error,result) {
                        if(!error && result) {
                            Demiran.downloadData(Math.floor(new Date().getTime()/360000) + ".xml", result);
                        } else {
                            console.error(error);
                            console.error(result);
                        }
                    })
                }
            })

        } else {
            Demiran.alert("Űrlap nem található");
        }
    }

    const sendNAV = function (){
        const invoiceForm = document.getElementById('invoiceForm');
        if(invoiceForm && invoiceForm instanceof HTMLFormElement){
            const disabledNodes = document.querySelectorAll("[disabled]");
            disabledNodes.forEach(function(node){
                node.disabled = !node.disabled;
            });
            const formData = new FormData(invoiceForm);
            disabledNodes.forEach(function(node){
                node.disabled = !node.disabled;
            });
            Demiran.confirm("XML Adatok letöltése","Biztosan letöltöd az adatokat?", result=>{
                if(result){
                    Demiran.call("send_invoice", formData, function(error,result) {
                        if(!error && result) {
                            Demiran.alert(result);
                        } else {
                            console.error(error);
                            console.error(result);
                        }
                    })
                }
            })

        } else {
            Demiran.alert("Űrlap nem található");
        }
    }
</script>
</form>