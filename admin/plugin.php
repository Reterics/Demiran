<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-16
 * Time: 18:52
 */

require('../config.php');
include("./auth.php");
require_once("./template.php");

?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Bővítmények - Demiran</title>
        <?php admin_head("Bővítmények - Demiran"); ?>
    </head>
<body>
<?php
require_once('./backend/main.php');
admin_header_menu();
require_once "process.php";
$pluginDir = '../plugins/';

if (isset($_GET['name']) && $_GET['name'] != ""):
    $currentPage = "index";
    if(isset($_GET['page']) && $_GET['page'] != "" ) {
        $currentPage = $_GET['page'];
    }
    if(file_exists($pluginDir . $_GET['name']) && file_exists($pluginDir . $_GET['name'] . '/pages/' . $currentPage . '.php')):
        if(file_exists($pluginDir . $_GET['name'] . '/functions.php')) {
            require_once($pluginDir . $_GET['name'] . '/functions.php');
        }
    ?>
        <!-- <div class="top_outer_div"> -->
        <?php
            require_once ($pluginDir . $_GET['name'] . '/pages/' . $currentPage . '.php');
         ?>
        <!-- </div> -->
        <?php
    else:
        ?>
        <div class="top_outer_div">
            <div class="row">
                <div class="col-md-12">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">Üzenet</h5>
                        </div>
                        <div class="body">
                            Az alábbi ID-vel nem rendelkezel Bővítménnyel
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endif;

else:
    $plugins = scandir($pluginDir);

    ?>
    <div class="top_outer_div">
        <div class="row">
            <div class="col-md-12">
                <div class="lio-modal">
                    <div class="header">
                        <h5 class="title">Oldalak</h5>
                        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'developer')): ?>
                            <span class="plus-icon addP"></span>
                        <?php endif; ?>
                    </div>
                    <div class="body users drag-container"
                         style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">
                        <div class="dragTitle">
                            <button class="dragButton"><span class="toggler-icon"></span></button>
                            <div class="id short">ID</div>
                            <div class="title">Név</div>
                            <div class="desc long">Leírás</div>
                            <div class="author">Szerző</div>
                            <div class="version">Verzió</div>
                            <div class="date long">Dátum</div>
                        </div>
                        <?php
                        $i = 1;
                        foreach ($plugins as $plugin) {
                            $pluginInfoFile = $pluginDir . $plugin . "/index.json";
                            if (is_dir($pluginDir . $plugin) && $plugin != "." && $plugin != ".." && file_exists($pluginInfoFile)) {
                                $pluginObject = json_decode(file_get_contents($pluginDir . $plugin . "/index.json"), true);
                                ?>
                                <div class="dragged" onclick="openPlugin('<?php echo $plugin ?>')" style="cursor:pointer">
                                    <button class="dragButton"><span class="toggler-icon"></span></button>

                                    <div class="id short"><?php echo $i ?></div>
                                    <div class="title"><?php echo $pluginObject['name'] ?></div>
                                    <div class="desc long"><?php echo $pluginObject['desc'] ?></div>
                                    <div class="author"><?php echo $pluginObject['author'] ?></div>
                                    <div class="version"><?php echo $pluginObject['version'] ?></div>
                                    <div class="date long"><?php echo $pluginObject['date'] ?></div>


                                </div>
                                <?php
                                $i++;
                            }

                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        function openPlugin(name){
            const source = location.href.split("plugin.php")[0];
            location.href = source + "plugin.php?name="+encodeURIComponent(name);
        }
    </script>
<?php
endif;
?>