<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-17
 * Time: 10:14
 */

global $connection;

$query = "CREATE TABLE invoice_list (
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
      )";
$result = mysqli_query($connection, $query);
if(!$result) {
    echo "Probléma merült fel a táblák ellenőrzése során";
}