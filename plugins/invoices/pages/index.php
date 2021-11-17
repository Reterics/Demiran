<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 10:00
 */
if (!isset($_SESSION["username"])) {
    die("Az oldal megtekintesehez be kell jelentkezned");
}

$fromDate = date("Y-m-d", strtotime('first day of this month'));
$toDate =   date("Y-m-d", strtotime('last day of this month'));

if (isset($_POST["start-date"]) && isset($_POST["end-date"])) {
    $fromDate = $_POST["start-date"];
    $toDate = $_POST["end-date"];
} else if(isset($_GET["start-date"])&& isset($_GET["end-date"])){
    $fromDate = $_GET["start-date"];
    $toDate = $_GET["end-date"];
}

$insDate = [
    "dateTimeFrom" => $fromDate."T00:00:00Z",
    "dateTimeTo" => $toDate."T23:59:59Z",
];
$page = 1;
$transactionListResult = $reporter->queryTransactionList($insDate, $page);
$table = "";
if(isset($transactionListResult->transaction)) {
    $table .= "<table style='width: 100%'><thead><tr><th>ID</th><th>Date</th><th>User</th><th>Source</th><th>Version</th></tr></thead>";
    foreach($transactionListResult->transaction as $item) {
        $table .= "<tr onclick='downloadTransactionData(\"".$item->transactionId."\")'>";
        $table .= "<td>".$item->transactionId."</td>";
        $table .= "<td>".$item->insDate."</td>";
        $table .= "<td>".$item->insCusUser."</td>";
        $table .= "<td>".$item->source."</td>";
        $table .= "<td>" .$item->originalRequestVersion."</td>";
        $table .= "</tr>";



    }

    $table .= "</table>";
}else{
    $table .= "Nincsenek tranzakciók";
}

?>
<div class="top_outer_div">

    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Számlák</h5>
                    <div class="filter-group" style="display: flex; flex-direction: row;width: 550px">
                        <label for="start-date" style="margin-bottom: 0; margin-left: 10px;line-height: 1.8;">Kezdő Dátum</label>
                            <input type="date" name="start-date" id="start-date" value="<?php echo $fromDate; ?>" />

                        <label for="end-date" style="margin-bottom: 0; margin-left: 10px;line-height: 1.8;">Záró Dátum</label>
                            <input type="date" name="end-date" id="end-date" value="<?php echo $toDate; ?>" />

                    </div>

                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        <span class="plus-icon" onclick="location.href='./plugin.php?name=invoices&page=create<?php if(isset($_GET["user"])){ echo "&user=".$_GET["user"];}?>'"></span>
                    <?php endif; ?>
                </div>

                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">UnixID</div>
                        <div class="supplierName">Eladó Név</div>
                        <div class="invoiceIssueDate">Kiállítási Dátum</div>
                        <div class="invoiceDeliveryDate">Teljesítési Dátum</div>
                        <div class="invoiceNumber">Iktatási szám</div>
                        <div class="transactionID long">Tranzakció ID</div>
                        <div class="state long">Státusz</div>
                        <div class="actions">Műveletek</div>
                    </div>

                    <?php
                    $sql = "SELECT * FROM invoice_list";

                    if (isset($connection)) :
                        $result = mysqli_query($connection, $sql);
                        if($result):
                            while ($row = mysqli_fetch_array($result)):
                            ?>
                            <div class="dragged">
                                <button class="dragButton"><span class="toggler-icon"></span></button>
                                <div class="unixID short"><?php echo $row['unixID']; ?></div>
                                <div class="supplierName"><?php echo $row['supplierName']; ?></div>
                                <div class="invoiceIssueDate"><?php echo $row['invoiceIssueDate']; ?></div>
                                <div class="invoiceDeliveryDate"><?php echo $row['invoiceDeliveryDate']; ?></div>
                                <div class="invoiceNumber"><?php echo $row['invoiceNumber']; ?></div>
                                <div class="state"></div>

                                <div class="transactionID long"><?php echo $row['transactionID']; ?></div>
                                <div class="actions">
                                    <span class="hoverIcon seeDetails details-icon" onclick="openInvoice('<?php echo $row['id'] ?>')"></span>
                                </div>
                            </div>

                            <?php
                            endwhile;
                            mysqli_free_result($result);
                        else:
                            echo "Az adatok töltése sikertelen!\n".json_encode(mysqli_error($connection));
                        endif;
                    endif;



                    ?>


                    <script type="application/javascript">
                        const openInvoice = function (id) {
                            navigate("./plugin.php?name=invoices&page=invoice&id=" + id);
                            return false;
                        };
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="top_outer_div">

    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Technikai Felhasználók</h5>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        <span class="plus-icon" onclick="addTechnicalUser()"></span>
                    <?php endif; ?>
                </div>

                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">id</div>
                        <div class="supplierName">Név</div>
                        <div class="supplierTaxNumber">Adószám</div>
                        <div class="invoiceIssueDate">Irányítószám</div>
                        <div class="supplierTown">Település</div>
                        <div class="login">Felhasználónév</div>
                        <div class="actions">Műveletek</div>
                    </div>

                    <?php


                    $sql = "SELECT * FROM invoice_user";

                    if (isset($connection)) :
                        $result = mysqli_query($connection, $sql);
                        if($result):
                            while ($row = mysqli_fetch_array($result)):
                                ?>
                                <div class="dragged">
                                    <button class="dragButton"><span class="toggler-icon"></span></button>
                                    <div class="id short"><?php echo $row['id']; ?></div>
                                    <div class="supplierName"><?php echo $row['supplierName']; ?></div>
                                    <div class="supplierTaxNumber"><?php echo $row['supplierTaxNumber']; ?></div>
                                    <div class="supplierPostCode"><?php echo $row['supplierPostCode']; ?></div>
                                    <div class="supplierTown"><?php echo $row['supplierTown']; ?></div>
                                    <div class="login"><?php echo $row['login']; ?></div>
                                    <div class="actions">
                                    </div>
                                </div>

                            <?php

                            endwhile;
                            mysqli_free_result($result);
                        else:
                            echo "Az adatok töltése sikertelen!\n".json_encode(mysqli_error($connection));
                        endif;
                    endif;
                    ?>
                    <div style="display: none">
                        <form id="addUserForm" style="padding: 10px 15px;flex-direction: column;" method="post"
                              enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label>Név
                                        <input type="text" class="form-control" name="supplierName" placeholder="Név" required/>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label>Adószám
                                        <input type="text" class="form-control" placeholder="11111111-2-42"
                                               name="supplierTaxNumber" maxlength="13"
                                               pattern="[0-9]{8}[-]{1}[0-9]{1}[-]{1}[0-9]{2}">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="supplierPostCode">Irányítószám
                                        <input type="number" min="1000" max="9999" alt="PostCode" class="form-control"
                                               name="supplierPostCode" placeholder="8900">
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <label for="supplierTown">Település
                                        <input type="text" class="form-control" name="supplierTown"
                                               placeholder="Zalaegerszeg" required/></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="supplierStreetName">Közterület
                                        <input type="text" class="form-control" name="supplierStreetName"
                                               placeholder="Kossuth" required/></label>
                                </div>
                                <div class="col-md-3">
                                    <label for="supplierStreet">Jelleg
                                        <select class="form-control" name="supplierStreet">
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
                                        <input type="text" class="form-control" placeholder="20/a"
                                               name="supplierAddress">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label for="supplierBankAccountNumber">Bankszámlaszám
                                        <input type="text" class="form-control" placeholder="88888888-66666666-12345678"
                                               name="supplierBankAccountNumber"
                                               pattern="[0-9]{8}[-][0-9]{8}[-][0-9]{8}|[0-9]{8}[-][0-9]{8}|[A-Z]{2}[0-9]{2}[0-9A-Za-z]{11,30}">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label>NAV Login
                                        <input type="text" class="form-control" name="login" placeholder="NAV Login" required/>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label>NAV Jelszó
                                        <input type="password" class="form-control" name="password" placeholder="NAV Jelszó" required/>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label>Aláírókulcs
                                        <input type="text" class="form-control" name="signKey" placeholder="Aláírókulcs" required/>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label>Csere kulcs
                                        <input type="text" class="form-control" name="exchangeKey" placeholder="Csere kulcs" required/>
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="_plugin" value="invoices">
                        </form>
                    </div>
                    <script type="application/javascript">
                        function addTechnicalUser(){
                            const addUserForm = document.querySelector("form#addUserForm");
                            if(addUserForm){
                                const cln = addUserForm.cloneNode(true);
                                const popup = Demiran.openPopUp("Technikai felhasználó hozzáadása", cln, [
                                    {
                                        value:"Mentés",
                                        onclick: (closeDialog, modalID)=>{
                                            const modal = document.querySelector("#"+modalID);
                                            const form = modal.querySelector("form");

                                            closeDialog();
                                            Demiran.call("add_technical_user",Demiran.convertToFormEncoded(form),function(error,result){
                                                if(!error && result.trim() === "OK"){
                                                    location.reload();
                                                } else {
                                                    Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                    console.log(result,error);
                                                }
                                            });

                                            //form.submit();

                                        }
                                    },
                                    {
                                        value:"Bezárás",
                                        type:"close"
                                    }
                                ]);
                            }
                        }

                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="top_outer_div">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Számlák a NAV Rendszerben</h5>
                </div>
                <div class="body">
                    <?php echo $table; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    function downloadTransactionData(transactionId) {
        Demiran.call("nav_invoice_details", {
            _plugin:"invoices",
            transactionId: transactionId
        }, function (error,result){
           if(error){
               Demiran.alert(error, "Hiba");
           } else {
               Demiran.alert(result, "Részletek");
           }
        });
    }
</script>