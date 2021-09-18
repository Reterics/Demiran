<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 2020. 10. 11.
 * Time: 15:16
 */
require('../config.php');
include("./auth.php");
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
require_once "process.php";

?>

<div class="top_outer_div">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Oldalak</h5>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'developer')): ?>
                    <span class="plus-icon addP"></span>
                    <?php endif; ?>
                </div>
                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">ID</div>
                        <div class="title">Cím</div>
                        <div class="users">Felhasználó</div>
                        <div class="categories">Kategória</div>
                        <div class="tags">Címkék</div>
                        <div class="date long">Határidő</div>
                        <div class="actions">Műveletek</div>
                    </div>

                    <?php
                    $sql = "SELECT id,user,title,categories,tags,modified FROM pages;";

                    if ($connection) :
                    $userIDs = array();
                    $result = mysqli_query($connection, $sql);



                    while ($row = mysqli_fetch_array($result)) {
                    ?>

                        <div class="dragged">
                            <button class="dragButton"><span class="toggler-icon"></span></button>

                            <div class="id short"><?php echo $row['id'] ?></div>
                            <div class="title"><?php echo $row['title'] ?></div>
                            <div class="users" data-id="<?php echo $row['id'] ?>"><?php echo $row['user'] ?></div>
                            <div class="categories"><?php echo $row['categories'] ?></div>
                            <div class="tags"><?php echo $row['tags'] ?></div>
                            <div class="date long"><?php echo $row['modified'] ?></div>
                            <div class="actions">
                                <span class="hoverIcon seeDetails details-icon" onclick="previewPage('<?php echo $row['id'] ?>')"></span>
                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'developer')): ?>

                                    <span class="hoverIcon seeDetails pencil-icon" onclick="editPage('<?php echo $row['id'] ?>')"></span>

                            <span class="hoverIcon removeLine remove-icon" onclick="removePage('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>
                        <?php endif; ?>
                            </div>

                        </div>



                        <?php


                        $pieces = explode(",", $row['user']);
                        foreach ($pieces as $userID) {
                            if (is_numeric($userID) && !in_array($userID, $userIDs)) {
                                array_push($userIDs, $userID);
                            }
                        }


                    }
                        echo "<script>const userIDs = {";
                        if (count($userIDs)){
                            $sql = "SELECT id,username FROM users WHERE id IN (".implode(", ",$userIDs).")";
                            $result = mysqli_query($connection, $sql);
                            $i = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                if($i != 0){
                                    echo ",";
                                }
                                echo "'".$row['id']."':'".$row['username']."'";
                                $i = $i +1;
                            }
                        }
                        echo "};setUserDetails(userIDs);</script>";

                    endif;
                    ?>
                    <script>
                        //Demiran.applyDragNDrop(".drag-container", ".dragged");

                        const registerButton = document.querySelector(".addP");
                        if(registerButton){
                            registerButton.onclick = function () {
                                const form = document.querySelector(".addPage form");

                                if(form){
                                    const cln = form.cloneNode(true);

                                    const popup = Demiran.openPopUp("Hozzáadás", cln, [
                                        {
                                            value:"Hozzáad",
                                            onclick: (closeDialog, modalID)=>{
                                                const modal = document.querySelector("#"+modalID);
                                                const form = modal.querySelector("form");
                                                //form.submit();
                                                closeDialog();
                                                Demiran.call("add_page",Demiran.convertToFormEncoded(form),function(error,result){
                                                    if(!error && result.trim() === "OK"){
                                                        location.reload();
                                                    } else {
                                                        Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                        console.log(result,error);
                                                    }
                                                });
                                            }
                                        },
                                        {
                                            value:"Vissza",
                                            type:"close"
                                        }
                                    ]);
                                }
                            }
                        }

                        const previewPage = function (id) {
                            navigate("../page.php?id="+id);
                            return false;
                        };

                        const removePage = function (id, title) {
                            Demiran.openPopUp("Jóváhagyás", "Biztonsan törölni szeretnéd ezt az oldalt az adatbázisból? <br> " + id + " - " + title, [
                                {
                                    value:"Igen",
                                    onclick: (closeDialog)=>{
                                        closeDialog();
                                        Demiran.call("delete_page","deletepage=" + id,function(error,result){
                                            if(!error && result.trim() === "OK"){
                                                location.reload();
                                            } else {
                                                Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                console.log(result,error);
                                            }
                                        });
                                    }
                                },
                                {
                                    value:"Vissza",
                                    type:"close"
                                }
                            ]);

                            return false;
                        };

                        const editPage = function (id) {
                            navigate("./edit_page.php?id="+id);
                            return false;
                        };

                    </script>
                </div>
                <div class="addPage" style="display: none">
                    <form style="padding: 10px 15px;" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Cím
                                <input type="text" class="form-control" name="title" id="title" placeholder="Cím" required/>
                            </label>
                            <label for="user">Szerző
                            <input class="form-control" id="user" value="<?php echo $_SESSION['username']; ?>" disabled></label>
                            <input type="hidden" name="user" id="user" value="<?php echo $_SESSION['id']; ?>">

                            <label for="categories">Kategória
                                <input type="text" class="form-control" name="categories" id="categories" placeholder="Kategóriák">
                            </label>

                            <label for="tags">Címkék
                                <input type="text" class="form-control" name="tags" id="tags" placeholder="Címkék">
                            </label>


                            <input type="hidden" class="form-control" name="addpage" style="display:none;" value="1"/>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php footer(); ?>
    </body></html>
