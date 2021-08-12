<?php

require_once "../config.php";

include("./auth.php");


if(isset($_POST['addtables'])):
    info("Installer", "MySQL adatok beállítása");
    /**
     * Add column:
     * ALTER TABLE Customers
    ADD Email varchar(255);
     */


    try {
        if (isset($connection) && $connection) {
            mysqli_select_db($connection, $dbName);
            mysqli_query($connection, "SET NAMES UTF8");

            $addTable = "CREATE TABLE IF NOT EXISTS `users` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `username` varchar(50) NOT NULL,
         `email` varchar(50) NOT NULL,
         `password` varchar(50) NOT NULL,
         `role` varchar(50) NOT NULL,
         `image` varchar(1500) NOT NULL,
         `job` varchar(100) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `exp` varchar(50) NOT NULL,
         `level` varchar(50) NOT NULL,
         `work_time` varchar(50),
         `trn_date` datetime NOT NULL,
         PRIMARY KEY (`id`)
         );

        CREATE TABLE IF NOT EXISTS `todo` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `users` varchar(50) NOT NULL,
         `title` varchar(50) NOT NULL,
         `category` varchar(50) NOT NULL,
         `status` varchar(50) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `image` varchar(5000) NOT NULL,
         `level` varchar(50) NOT NULL,
         `order` varchar(50) NOT NULL,
         `creation_date` datetime NOT NULL,
         PRIMARY KEY (`id`)
         );
         
         CREATE TABLE IF NOT EXISTS `project` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `users` varchar(250) NOT NULL,
         `title` varchar(50) NOT NULL,
         `category` varchar(50) NOT NULL,
         `client` varchar(50) NOT NULL,
         `status` varchar(50) NOT NULL,
         `billing` varchar(50) NOT NULL,
         `price` varchar(50) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `created` datetime NOT NULL,
         `start_time` datetime NOT NULL,
         `deadline` datetime NOT NULL,
         `order` varchar(50) NOT NULL,
         PRIMARY KEY (`id`)
         );  

         CREATE TABLE IF NOT EXISTS `project_tasks` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `users` varchar(50) NOT NULL,
         `title` varchar(50) NOT NULL,
         `project` varchar(50) NOT NULL,
         `visibility` varchar(50) NOT NULL,
         `repeat` varchar(50) NOT NULL,
         `image` varchar(1500) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `attachments` varchar(5000) NOT NULL,
         `elapsed` varchar(300) NOT NULL,
         `priority` varchar(50) NOT NULL,
         `start_time` datetime NOT NULL,
         `deadline` datetime NOT NULL,
         `order` varchar(50) NOT NULL,
         PRIMARY KEY (`id`)
         );

         CREATE TABLE IF NOT EXISTS `pages` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `user` varchar(50) NOT NULL,
         `title` varchar(50) NOT NULL,
         `categories` varchar(50) NOT NULL,
         `tags` varchar(50) NOT NULL,
         `image` varchar(1500) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `created` datetime NOT NULL,
         `modified` datetime NOT NULL,
         PRIMARY KEY (`id`)
         );

         CREATE TABLE IF NOT EXISTS `messages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `msg_id` varchar(32) NOT NULL UNIQUE,
            `source` int(11) NOT NULL,
            `target` int(11) NOT NULL,
            `created` datetime NOT NULL,
            `message` varchar(500) NOT NULL,
            `status` varchar(50) NOT NULL,
            `project` int(11) NOT NULL,
            `attachment` varchar(1500) NOT NULL,
         PRIMARY KEY (`id`)
         );
         
         CREATE TABLE IF NOT EXISTS `settings` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `setting_name` varchar(32) NOT NULL UNIQUE,
           `message` varchar(500) NOT NULL,
           `extra` varchar(500) NOT NULL,
        PRIMARY KEY (`id`)
         );
         
         CREATE TABLE IF NOT EXISTS `shift_list` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `user` varchar(32) NOT NULL,
           `duration` varchar(500),
           `start_time` datetime NOT NULL,
           `end_time` datetime,
           `note` varchar(500),
           `task` varchar(11),
           PRIMARY KEY (`id`)
         );

INSERT IGNORE INTO settings SET id=1,setting_name='ip_stack',message='::1',extra='';
INSERT IGNORE INTO settings SET id=2,setting_name='geo_data',message='46.8877312,16.8394752,100',extra='';
INSERT IGNORE INTO settings SET id=3,setting_name='timezone',message='Europe/Budapest',extra='';
INSERT IGNORE INTO settings SET id=4,setting_name='start_work_auto',message='true',extra='';
INSERT IGNORE INTO settings SET id=5,setting_name='stop_work_auto',message='true',extra='';
INSERT IGNORE INTO users SET id=1,username='testadmin',email='test@test.com',password='81dc9bdb52d04dc20036dbd8313ed055',role='admin',image='',job='',details='',exp='0',level='1',trn_date='2021-01-26 12:36:46',work_time='';

";

            $result = mysqli_multi_query($connection, $addTable);

            if($result){
                info("Installer", "MySQL adatok beállítása sikeres!");

            } else {
                info("Installer", "MySQL adatok beállítása sikertelen");
            }
        } else {
            die("Hiba történt az adatbázis kapcsolat során");
        }
    }catch (Exception $e){
        die($e->getMessage());
    }

else:

    require_once("./template.php");

    ?>


    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Oldalak</title>
        <?php admin_head(); ?>
    </head>
    <body>



    <?php
    admin_header_menu();

    ?>

    <div class="top_outer_div">
        <div class="row">
            <div class="col-md-12">
                <div class="lio-modal">
                    <div class="header">
                        <h5 class="title">Oldalak</h5>
    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        <span class="plus-icon addP"></span>
    <?php endif; ?>
                    </div>
                    <div class="body">
                        <form method="post">
                            <input type="hidden" value="true" name="addtables">
                            <input type="button" class="btn btn-outline-black"  id="formButton" value="Adatbázis Telepítése">
                        </form>




                        <script type="text/javascript">

                            const formButton = document.getElementById("formButton");
                            if(formButton){
                                formButton.onclick = function(e) {
                                    e.preventDefault();
                                    console.log(Demiran.convertToFormEncoded(formButton.parentElement));
                                    Demiran.post("",Demiran.convertToFormEncoded(formButton.parentElement),function(error,data){
                                        if(!error){

                                        }
                                        console.log(data,error);
                                    });
                                };
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

endif;

 footer(); ?>
    </body></html>