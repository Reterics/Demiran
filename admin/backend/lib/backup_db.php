<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-09-20
 * Time: 20:36
 */

// Based on https://stackoverflow.com/a/21284229/2813238
$Demiran->add_method("backup_db", function ($arguments, $connection) {

    if($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin'){
        $tables = array("users", "shift_list", "settings", "project", "project_tasks", "pages", "messages");

        $return = "-- Demiran Projektmenedzsment Rendszer\n";
        $return .= "-- Verzió: 1.2\n";
        $return .= "-- Létrehozás ideje: ".date('Y-m-d')."\n";
        $return .= "\n";
        $return .= "-- \n";
        $return .= "-- Adatbázis: `" . DB_NAME . "`\n";
        $return .= "-- \n";
        $return .= "\n";
        //cycle through
        foreach($tables as $table)
        {
            $result = mysqli_query($connection, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);
            $num_rows = mysqli_num_rows($result);

            $return.= "--\n";
            $return.= "-- Tábla szerkezet ehhez a táblához `".$table."`\n";
            $return.= "--\n";
            $return.= "\n";
            $return.= "DROP TABLE IF EXISTS ".$table.";";
            $row2 = mysqli_fetch_row(mysqli_query($connection, 'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n";
            $counter = 1;

            while($row = mysqli_fetch_row($result))
            {
                if($counter == 1){
                    $return.= "--\n";
                    $return.= "-- A tábla adatainak kiíratása `".$table."`\n";
                    $return.= "--\n";
                    $return.= "\n";
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                } else{
                    $return.= '(';
                }

                //Over fields
                for($i=0; $i<$num_fields; $i++)
                {
                    $row[$i] = addslashes($row[$i]);
                    $row[$i] = str_replace('\n',"\\n",$row[$i]);
                    if (isset($row[$i])) { $return.= "'".$row[$i]."'" ; } else { $return.= "''"; }
                    if ($i<($num_fields-1)) { $return.= ','; }
                }

                if($num_rows == $counter){
                    $return.= ");\n";
                } else{
                    $return.= "),\n";
                }
                ++$counter;
            }
            $return.="\n-- --------------------------------------------------------\n\n";
        }

        echo $return;
    } else {
        echo "";
    }

});
