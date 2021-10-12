<?php

if(file_exists(dirname(__FILE__).'/env.php')) {
    header("Location: /index.php");
    exit;
}



if(isset($_POST['add_tables']) && isset($_POST['db_name']) && isset($_POST['database']) && isset($_POST['username']) && isset($_POST['password'])):
    /**
     * Add column:
     * ALTER TABLE Customers
    ADD Email varchar(255);
     */

    $database = $_POST['database'];
    $db_name = $_POST['db_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $port = isset($_POST['port']) ? $_POST['port'] : "3306";

    mysqli_report(MYSQLI_REPORT_STRICT);
    $connection = false;
    try{
        $connection = mysqli_connect($database, $username, $password, $db_name, $port);
    }catch(Exception $e) {

    }
    if(!$connection) {
        header("Location: /install.php?error=sql");
        exit;
    }


    try {
        if (isset($connection) && $connection) {
            mysqli_select_db($connection, $db_name);
            mysqli_query($connection, "SET NAMES UTF8");

            $addTable = "CREATE TABLE IF NOT EXISTS `users` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `username` varchar(50) NOT NULL,
         `full_name` varchar(50) NOT NULL,
         `email` varchar(50) NOT NULL,
         `password` varchar(50) NOT NULL,
         `role` varchar(50) NOT NULL,
         `image` varchar(1500) NOT NULL,
         `job` varchar(100) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `work_time` varchar(50),
         `trn_date` datetime NOT NULL,
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
         `repeat` varchar(50) NOT NULL,
         `image` varchar(1500) NOT NULL,
         `details` varchar(5000) NOT NULL,
         `attachments` varchar(5000) NOT NULL,
         `state` varchar(300) NOT NULL,
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
INSERT IGNORE INTO users SET id=1,username='admin',email='test@test.com',password='81dc9bdb52d04dc20036dbd8313ed055',role='admin',image='',job='',details='',trn_date='2021-01-26 12:36:46',work_time='';

";
// Default User Credentials: admin, 1234
            $result = mysqli_multi_query($connection, $addTable);

            if($result){
                $filewrite = file_put_contents('./env.php', "<?php\n// Host name of the MySQL Database\n".
                    "define(\"DB_HOST\", \"".$database."\");\n".
                    "// Username of the MySQL Database\n".
                    "define(\"DB_USER\", \"".$username."\");\n".
                    "// Password of the MySQL Database\n".
                    "define(\"DB_PASS\", \"".$password."\");\n".
                    "// Database Name of the MySQL Database\n".
                    "define(\"DB_NAME\", \"".$db_name."\");\n".
                    "// Post of the MySQL Database\n".
                    "define(\"DB_PORT\", \"".$port."\");\n".
                    "");
                if($filewrite === false) {
                    header("Location: /install.php?error=file_write_failed");
                } else {
                    header("Location: /index.php");
                }

                exit;

            } else {
                header("Location: /install.php?error=sql&details=failed_query");
                exit;
            }
        } else {
            header("Location: /install.php?error=sql&details=connection_error");
            exit;
        }
    }catch (Exception $e){
        die($e->getMessage());
    }

else:


    ?>


    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Telepítés</title>
        <link href="admin/css/bootstrap.min.css" rel="stylesheet">
        <link href="admin/css/product.css" rel="stylesheet">
        <?php
        if(isset($_GET['theme'])){
            echo "<link href=\"./css/theme-".$_GET['theme'].".css\" rel=\"stylesheet\">";
        }
        ?>
    </head>
    <body>



    <?php

    ?>

    <div class="top_outer_div">
        <div class="row">
            <div class="col-md-12">
                <div class="logo" style="margin: auto;width: 300px;">
                    <img src="admin/img/logo_black.svg" style="height: 100%; width: 100%;" alt="demiran Logo">
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 40px">
            <div class="col-md-12">
                <div class="lio-modal" style="display: block; margin: auto;width: 500px;">
                    <div class="header">
                        <h5 class="title">Telepítés</h5>
    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        <span class="plus-icon addP"></span>
    <?php endif; ?>
                    </div>
                    <div class="body" style="padding:5px">
                        <p>Itt adhatjuk meg az adatbázis tulajdonságait. Amennyiben nem vagyunk biztosak bennük, fel kell venni a kapcsolatot a tárhely szolgáltatóval, vagy a rendszergazdával.</p>
                        <form method="post">
                            <label>
                                Kiszolgáló Címe:
                                <input name="database" type="text" class="form-control" placeholder="localhost">
                            </label>
                            <label>
                                Kiszolgáló Port:
                                <input name="port" type="text" class="form-control" placeholder="3306">
                            </label>
                            <label>
                                Felhasználónév:
                                <input name="username" type="text" class="form-control" placeholder="root">
                            </label>
                            <label>
                                Jelszó:
                                <input name="password" type="password" class="form-control" placeholder="">
                            </label>

                            <label>
                                Adatbázis neve:
                                <input name="db_name" type="text" class="form-control" placeholder="dbname">
                            </label>

                            <input type="hidden" value="true" name="add_tables">
                            <input type="submit" class="btn btn-outline-black" id="formButton" value="Adatbázis Telepítése">
                        </form>




                        <script type="text/javascript">

                            /*const formButton = document.getElementById("formButton");
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
                            }*/
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    </body></html>