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

?>

<div class="container-md">

    <div class="row">
        <div class="col-md-6">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Kiállító</h5>
                </div>
                <div class="body">
                    <div class="form-group">
                        <div class="col-md-12">
                        <label for="full_name">Teljes Név
                            <input type="text" class="form-control" name="supplierName" id="supplierName"
                                   placeholder="Teljes név" required/></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                        <label for="full_name">Adószám
                            <input type="text" class="form-control" placeholder="11111111-2-42" id="supplierTaxNumber" name="supplierTaxNumber" maxlength="13" pattern="[0-9]{8}[-]{1}[0-9]{1}[-]{1}[0-9]{2}">
                        </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="full_name">Irányítószám
                                <input type="number" min="1000" max="9999" alt="iranyitoszam" class="form-control" name="supplieriranyitoszam" id="supplieriranyitoszam" placeholder="8900">
                            </label>
                        </div>
                        <div class="col-md-8">
                            <label for="full_name">Település
                                <input type="text" class="form-control" name="full_name" id="full_name"
                                       placeholder="Teljes név" required/></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="full_name">Közterület
                                <input type="text" class="form-control" name="full_name" id="full_name"
                                       placeholder="Teljes név" required/></label>
                        </div>
                        <div class="col-md-3">
                            <label for="full_name">Jelleg
                                <select class="form-control" id="supplierStreet" name="supplierStreet">
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
                            <label for="full_name">Házszám
                                <input type="text" class="form-control" placeholder="20/a" id="supplierAddress" name="supplierAddress">
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="full_name">Bankszámlaszám
                                <input type="text" class="form-control" placeholder="88888888-66666666-12345678" id="supplierBankAccountNumber" name="supplierBankAccountNumber" pattern="[0-9]{8}[-][0-9]{8}[-][0-9]{8}|[0-9]{8}[-][0-9]{8}|[A-Z]{2}[0-9]{2}[0-9A-Za-z]{11,30}">
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
                            <label for="full_name">Teljes Név
                                <input type="text" class="form-control" name="customerName" id="customerName"
                                       placeholder="Teljes név" required/></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="full_name">Adószám
                                <input type="text" class="form-control" placeholder="11111111-2-42" id="customerTaxNumber" name="customerTaxNumber" maxlength="13" pattern="[0-9]{8}[-]{1}[0-9]{1}[-]{1}[0-9]{2}">
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="full_name">Irányítószám
                                <input type="number" min="1000" max="9999" alt="iranyitoszam" class="form-control" name="customeriranyitoszam" id="customeriranyitoszam" placeholder="8900">
                            </label>
                        </div>
                        <div class="col-md-8">
                            <label for="full_name">Település
                                <input type="text" class="form-control" placeholder="Zalaegerszeg" id="customerTown" name="customerTown">
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="full_name">Közterület
                                <input type="text" class="form-control" placeholder="Arany János" id="customerStreetName" name="customerStreetName">
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="full_name">Jelleg
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
                            <label for="full_name">Házszám
                                <input type="text" class="form-control" placeholder="20/a" id="customerAddress" name="customerAddress">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="container-md">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="body">
                    <div class="form-group">
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
                                    <option value="PAPER">PAPER</option>
                                    <option value="ELECTRONIC">ELECTRONIC</option>
                                    <option value="EDI">EDI</option>
                                    <option value="UNKNOWN">UNKNOWN</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="full_name">Pénznem
                                <select class="form-control" id="currencyCode" name="currencyCode">
                                    <option value="HUF">HUF</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="full_name">Fizetési Mód
                                <select class="form-control" id="currencyCode" name="currencyCode">
                                    <option value="CASH">Készpénz</option>
                                    <option value="TRANSFER">Átutalás</option>
                                    <option value="CARD">Bankkártya</option>
                                    <option value="DELIVERY">Utánvét</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="full_name">Számla Kelte
                                <input type="date" class="form-control" id="invoiceDeliveryDate" name="invoiceDeliveryDate" value="2021-10-17">
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="full_name">Számla Teljesítés
                                <input type="date" class="form-control" id="paymentDate" name="paymentDate" value="2021-10-17">
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label for="full_name">Számla Sorszáma
                                <input type="text" class="form-control" id="paymentDate" name="paymentDate" value="2021-10-16/0001">
                            </label>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="container-lg">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Tételek</h5>
                </div>
                <div class="body">
                    <table class="invoiceTable">
                        <thead>
                        <tr>
                            <th class="lineNatureIndicator">Típus</th>
                            <th class="productCodeCategory advanced">Kód Típus</th>
                            <th class="productCodeValue">Kód</th>
                            <th class="lineDescription">Név</th>
                            <th class="quantity">Mennyiség</th>
                            <th class="unitOfMeasure">Mértékegység</th>
                            <th class="unitPrice">Nettó Egységár</th>
                            <th class="lineNetAmount">Nettó Érték</th>
                            <th class="vatPercentage">ÁFA(%)</th>
                            <th class="lineVatAmount">ÁFA(Ft)</th>
                            <th class="lineGrossAmountNormal">Bruttó Ár</th>
                            <th>Műveletek</th>
                        </tr>
                        </thead>
                        <tbody>



                        <tr class="add_new_item">
                            <td><select class="form-control" id="lineNatureIndicator">
                                    <option value="PRODUCT">Termék</option>
                                    <option value="SERVICE">Szolgáltatás</option>
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
                            <td><input type="text" class="form-control" placeholder="02031110" id="productCodeValue"></td>
                            <td><input type="text" class="form-control" placeholder="Hűtött házi sertés (fél)" id="lineDescription"></td>
                            <td><input type="number" min="1" class="form-control" placeholder="1500" id="quantity"></td>
                            <td><select class="form-control" id="unitOfMeasure">
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


                            <td><input type="number" min="1" class="form-control" placeholder="400" id="unitPrice"></td>
                            <td><input type="number" min="1" class="form-control" placeholder="600000" id="lineNetAmountData"></td>

                            <td>
                                <select class="form-control" id="lineVatRate">
                                    <option value="0.2126">27%</option>
                                    <option value="0.1525">18%</option>
                                    <option value="0.0476">5%</option>
                                    <option value="0">0</option>
                                    <option value="0">TAM</option>
                                </select>

                            </td>
                            <td><input type="number" min="1" class="form-control" placeholder="30000" step="0.01" id="lineVatData"></td>
                            <td><input type="number" min="1" class="form-control" placeholder="630000" step="0.01" id="lineGrossAmountData"></td>
                            <td><button class="btn btn-default addButton" onclick="return addItem()" style="display: inline-block ;    border: 1px solid;">Hozzáadás</button></td>
                        </tr>

                        </tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-md">
    <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Összesítés az ÁFA tartalom szerint Ft</h5>
                </div>
                <div class="body">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label>ÁFA tartalom %
                                <input type="text" class="form-control" value="21.26%" disabled>
                                <input type="text" class="form-control" value="4.76%" disabled>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label>Összesen Ft
                                <input type="text" class="form-control" value="24000" disabled>
                                <input type="text" class="form-control" value="1000" disabled>

                            </label>
                        </div>
                    </div>


                </div>
                <div class="footer">
                    <h3>25000 Ft</h3>
                </div>
            </div>
        </div>

    </div>
</div>

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
                    unitPrice.innerHTML = "Bruttó Egységár";
                    if(lineVatRate){
                        lineVatRate.innerHTML = generateSelectOptions(
                            [
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
                    unitPrice.innerHTML = "Nettó Egységár";
                    if(lineVatRate){
                        lineVatRate.innerHTML = generateSelectOptions(
                            [
                                {name:"27%", value: "0.27"},
                                {name:"18%", value: "0.18"},
                                {name:"5%", value: "0.05"},
                                {name:"0", value: "0"},
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

    const addItem = function(){
        const table = document.querySelector(".invoiceTable");
        if(!table){
            return;
        }
        const addItemTr = table.querySelector("tr.add_new_item");
        if(!addItemTr){
            return;
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
                            values.push((num * 100).toFixed(2));
                            ids.push(input.id);
                            titles.push(null);
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

        values.forEach((value,i)=>{
            const td = document.createElement("td");
            if(titles[i]){
                td.innerHTML = titles[i];
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
            td.classList.add("product_line");
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

    }
</script>