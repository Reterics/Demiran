<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 3/22/2021
 * Time: 8:30 PM
 */


$author = "Attila Reterics";
$email = "reterics.attila@gmail.com";
$version = 1.2;

$path = "./builds";
$builds = scandir($path);
?>

<img class="d-inline-block align-top" height="30" src="./img/logo_black.svg" style="height: 35px; width: 100%;" alt="demiran Logo">
<p style="text-align:center;margin-bottom:0">Verzió: <?php echo $version; ?> </p>
<p style="text-align:center">2021, Reterics Attila - Minden Jog Fenntartva </p>

<?php
foreach($builds as $build) {
    if($build != "" && $build != "." && $build != ".."){
        $buildVersion = floatval($build);

        if (floatval($buildVersion) > $version) {
            echo "<h4>Új verzió elérhető:".$buildVersion."</h4><a class='btn btn-outline-black' href='?tab=2&extract=".$build."'>Frissítés</a>";
        } else if(floatval($buildVersion) == $version) {
            echo "<h4>A Rendszer naprakész.</h4>";
        } else {
            echo "<h4>Fejlesztői verzió.</h4>";
        }

    }

}

if (isset($_GET['extract'])) {
    $zip = new ZipArchive;
    if(!file_exists($path . '/' . $_GET['extract'])) {
        echo "<br>File is not found: ".$path . $_GET['extract'];
    }
    if ($zip->open($path . '/' . $_GET['extract']) === TRUE) {
        $zip->extractTo('./');
        $zip->close();
        echo '<br>Kicsomagolás sikeres....';
    } else {
        echo '<br>Kicsomagolási hiba:'.($zip->getStatusString( ));
    }
}