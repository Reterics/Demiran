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

?> <div class="top_outer_div">

    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Számlák</h5>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        <span class="plus-icon addInvoice"></span>
                    <?php endif; ?>
                </div>

                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">UnixID</div>
                        <div class="supplierName">Eladó Név</div>
                        <div class="supplierTaxNumber">Eladó Adószám</div>
                        <div class="invoiceIssueDate">Kiállítási Dátum</div>
                        <div class="invoiceDeliveryDate">Teljesítési Dátum</div>
                        <div class="invoiceNumber">Iktatási szám</div>
                        <div class="transactionID long">Tranzakció ID</div>
                        <div class="actions">Műveletek</div>
                    </div>

                    <?php

                    $sql = "SELECT * FROM invoice_list;";

                    if (isset($connection)) :
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_array($result)) {
                            ?>
                            <div class="dragged">
                                <button class="dragButton"><span class="toggler-icon"></span></button>
                                <div class="unixID short"><?php echo $row['unixID']; ?></div>
                                <div class="supplierName"><?php echo $row['supplierName']; ?></div>
                                <div class="supplierTaxNumber"><?php echo $row['supplierTaxNumber']; ?></div>
                                <div class="invoiceIssueDate"><?php echo $row['invoiceIssueDate']; ?></div>
                                <div class="invoiceDeliveryDate"><?php echo $row['invoiceDeliveryDate']; ?></div>
                                <div class="invoiceNumber" ><?php echo $row['invoiceNumber']; ?></div>

                                <div class="transactionID long"><?php echo $row['transactionID']; ?></div>
                                <div class="actions">
                                    <span class="hoverIcon seeDetails details-icon" onclick="openInvoice('<?php echo $row['id'] ?>')"></span>
                                </div>
                            </div>

                            <?php

                        }
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