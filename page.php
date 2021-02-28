<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 2020. 10. 11.
 * Time: 15:47
 */

require_once "config.php";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Oldalak</title>
    <link rel="stylesheet" href="./style.css"/>

    <?php head(); ?>
</head>
<body>
<?php headerHTML(); ?>
<?php

if (isset($_GET['id'])) :
    $row  = sqlGetFirst("SELECT * FROM pages WHERE id='" . $_GET['id'] . "';");

    if (!$row):
        ?>
        <div style="padding: 1em;">
            <div class="row" style="justify-content: center;">
                <div class="col-md-3">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">Hiba </h5>
                        </div>
                        <div class="body">
                            404 - A keresett oldal nem található!
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php
    else:
        ?>
        <div style="padding: 1em;">
            <div class="row">
                <div class="col-md-3">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title"> <?php echo $row['title']; ?></h5>
                            <span>
                        <?php echo $row['user'] . " - " . $row['modified']; ?>
                    </span>
                        </div>
                        <div class="body">
                            <?php echo html_entity_decode($row['details']); ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    <?php
    endif;
else:

    ?>
    <div style="padding: 1em;">
        <div class="row" style="justify-content: center">
            <div class="col-md-4">
                <form class="form" method="get" style="text-align: center">
                    <h2 style="color:black;">Oldalak Keresése</h2>
                    <label for="page"> Keresőszó </label>
                    <input type="text" class="form-control" value="page" id="page">

                    <input type="button" value="Keresés" class="btn btn-outline-black" style="margin-top: 5px">
                </form>

            </div>
        </div>
        <div class="row" style="justify-content: center">
            <div class="col-md-8" style="display: flex;flex-wrap: wrap">
                <?php

                $categories = "SELECT COUNT(categories), categories FROM pages GROUP BY categories";
                $pagesByCategories = "SELECT id, categories, title 
  FROM pages 
 WHERE id IN (
               SELECT MAX(id)
                 FROM pages 
                GROUP BY categories
             )";

                $result = mysqli_query($connection, $pagesByCategories);

                while ($row = mysqli_fetch_array($result)) {

                    ?>
                    <div class="lio-modal" style="max-width: 300px;margin: 5px;height: auto;">
                        <div class="header">
                            <h5 class="title"><?php
                                if($row['categories'] != "" ) {
                                    echo $row['categories'];
                                }else {
                                    echo "Kategorizálatlan";
                                }

                                ?></h5>
                        </div>
                        <div class="body">
                            <a href="?id=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></a>
                        </div>
                    </div>

                    <?php
                }


                ?>
            </div>
        </div>

        <div class="row" style="justify-content: center">
            <div class="col-md-8">
                <section class="articles">
                    <?php

                        $sql = "SELECT * FROM pages ORDER BY id desc limit 10;";

                        $result = mysqli_query($connection, $pagesByCategories);

                        while ($row = mysqli_fetch_array($result)) {

                        }
                    ?>
                </section>
            </div>
        </div>
    </div>

<?php
endif;

?>
</body>
</html>