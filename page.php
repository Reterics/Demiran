<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
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

    <?php head("Oldalak - Demiran"); ?>
</head>
<body>
<?php headerHTML(); ?>
<?php

if (isset($_GET['id'])) :
    $row  = sqlGetFirst("SELECT * FROM pages WHERE id='" . $_GET['id'] . "';");

    if (!$row):
        ?>
        <div class="top_outer_div">
            <div class="row" style="justify-content: center;">
                <div class="col-md-3">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">Hiba </h5>
                        </div>
                        <div class="body inner-padding">
                            404 - A keresett oldal nem található!
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php
    else:
        ?>
        <div class="top_outer_div">
            <div class="row">
                <div class="col-md-12">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title"> <?php echo $row['title']; ?></h5>
                            <span>
                        <?php echo $row['user'] . " - " . $row['modified']; ?>
                    </span>
                        </div>
                        <div class="body inner-padding">
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
    <div class="top_outer_div">
        <div class="row" style="justify-content: center">
            <div class="col-md-4">
                <form class="form center center-text" method="get">
                    <h2>Oldalak Keresése</h2>
                    <label for="searchfor"> Keresőszó </label>
                    <input type="text" class="form-control" value="<?php if(isset($_GET['searchfor'])) {echo $_GET['searchfor'];} ?>" id="searchfor" name="searchfor" placeholder="Keresőszó">

                    <input type="submit" value="Keresés" class="btn btn-outline-black" style="margin-top: 5px">
                </form>

            </div>
        </div>
        <div class="row center">
            <div class="col-md-12" style="display: flex;flex-wrap: wrap">
                <?php

                $categories = "SELECT COUNT(categories), categories FROM pages GROUP BY categories";

                if(isset($_GET['searchfor']) && $_GET['searchfor'] != "") {
                    $searchFor = mysqli_real_escape_string($connection, stripslashes($_GET['searchfor']));

                    $pagesByCategories = "SELECT pages.id as id, categories, title, users.username as username, pages.user
 FROM pages LEFT JOIN users ON pages.user = users.id 
 WHERE pages.id IN (
               SELECT id FROM pages GROUP BY categories ORDER BY id DESC
             ) AND title LIKE '%".$searchFor."%' LIMIT 20";
                } else {
                    $pagesByCategories = "SELECT pages.id as id, categories, title, users.username as username, pages.user
 FROM pages LEFT JOIN users ON pages.user = users.id 
 WHERE pages.id IN (
               SELECT id FROM pages GROUP BY categories ORDER BY id DESC
             ) LIMIT 20";
                }

                $result = mysqli_query($connection, $pagesByCategories);

                $titles = array();
                while ($row = mysqli_fetch_array($result)) {
                    if(!isset($titles[$row['categories']])) {
                        $titles[$row['categories']] = array();
                    }
                    array_push($titles[$row['categories']], $row);
                }
                foreach ($titles as $categories => $rows):

                    ?>
                    <div class="lio-modal" style="max-width: 300px;margin: 5px;height: auto;">
                        <div class="header">
                            <h5 class="title"><?php
                                if($categories != "" ) {
                                    echo $categories;
                                }else {
                                    echo "Kategorizálatlan";
                                }

                                ?></h5>
                        </div>
                        <div class="body">
                            <ul>
                            <?php
                                foreach ($rows as $row):
                                ?><li>
                                    <a href="?id=<?php echo $row['id'] ?>"><?php echo $row['title'] ?></a>
                                    </li>
                                <?php
                                endforeach;
                            ?>
                            </ul>
                        </div>
                    </div>

                    <?php
                endforeach;


                ?>
            </div>
        </div>

    </div>

<?php
endif;

?>
<?php footer(); ?>
</body>
</html>