<?php
/**
 * Created by PhpStorm.
 * User: RedAty
 * Date: 2020. 10. 07.
 * Time: 18:29
 */

require('../config.php');
include("auth.php");
require_once("./template.php");

?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Projektek - Secured Page</title>
        <?php admin_head(); ?>
    </head>
    <body>
<?php
admin_header_menu();
require_once "process.php";
$search = array(" 00:00:00");
$replace = array("");


if (isset($_GET['id'])):

    $sql = "SELECT * FROM project WHERE id='".$_GET['id']."';";

    if($connection):
        $result = mysqli_fetch_array(mysqli_query($connection, $sql));
        $userIDs = array();
        ?>
        <div style="padding: 1em;">

            <div class="row">
                <div class="col-md-4">
                    <div class="lio-modal">

                        <div class="header" style=" ">

                            <h5 class="title"><span class="back-icon" style="margin-right: 10px;height: 1.3em;width: 1.3em;" onclick="navigate('./projects.php')"></span><?php echo $result['title']; ?></h5>
                        </div>
                        <div class="body">

                            <?php
                            echo "<table class='table'>";

                            echo "<tr>
                            <td>ID</td><td>" . $result['id'] . "</td></tr>
                            <tr><td>Felhasználók</td><td class='users'>" . $result['users'] . "</td></tr>
                            <tr><td>Kategóriák</td><td>" . $result['category'] . "</td></tr>
                            <tr><td>Megrendelő</td><td class='client'>" . $result['client'] . "</td></tr>
                            <tr><td>Státusz</td><td>" . $result['status'] . "</td></tr>
                            <tr><td>Számlázás</td><td>" . $result['billing'] . "</td></tr>
                            <tr><td>Ár</td><td>" . $result['price'] . "</td></tr>
                       
                            <tr><td>Készült</td><td>" . $result['created'] . "</td></tr>
                            <tr><td>Kezdés</td><td>" . $result['start_time'] . "</td></tr>
                            <tr><td>Határidő</td><td>" . $result['deadline'] . "</td></tr>";

                            echo "</table>";


                            echo "<div class='details-container'>".$result['details']."</div>"
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="lio-modal" style="opacity: 0.1">
                        <div class="header">
                            <h5 class="title">Project Tasks</h5>
                            <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'developer')): ?>
                                <span class="plus-icon addTask"></span>
                            <?php endif; ?>

                        </div>
                        <div class="body tasks drag-container">
                            <div class="dragTitle">
                                <button class="dragButton"><span class="toggler-icon"></span></button>
                                <div class="id short">ID</div>
                                <div class="title">Cím</div>
                                <div class="users">Felhasználók</div>
                                <div class="category">Ismétlés</div>
                                <div class="client">Haladás</div>
                                <div class="status">Prioritás</div>
                                <div class="date long">Határidő</div>
                                <div class="actions">Műveletek</div>
                            </div>

                            <?php

                            $sql = "SELECT id,users,title,`repeat`,image, elapsed, priority, deadline,`order` FROM project_tasks WHERE project=".$result['id'].";";

                            $project_tasks = mysqli_query($connection, $sql);
                            while ($row =mysqli_fetch_array($project_tasks)){
                                ?>
                                <div class="dragged" data-order="<?php echo $row['order'] ?>">
                                    <button class="dragButton"><span class="toggler-icon"></span></button>

                                    <div class="id short"><?php echo $row['id'] ?></div>
                                    <div class="title"><?php echo $row['title'] ?></div>
                                    <div class="users"><?php echo $row['users'] ?></div>
                                    <div class="repeat"><?php echo $row['repeat'] ?></div>
                                    <div class="elapsed"><?php echo $row['elapsed'] ?></div>
                                    <div class="priority"><?php echo $row['priority'] ?></div>
                                    <div class="date long"><?php echo str_replace($search,$replace,$row['deadline']) ?></div>
                                    <div class="actions">
                                        <span class="hoverIcon seeDetails details-icon" onclick="editProject('<?php echo $row['id'] ?>')"></span>
                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                        <span class="hoverIcon removeLine remove-icon" onclick="removeProject('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>
                                <?php endif; ?>
                                    </div>

                                </div>


                                <?php
                                $pieces = explode(",", $row['users']);
                                foreach ($pieces as $userID) {
                                    if (is_numeric($userID) && !in_array($userID, $userIDs)) {
                                        array_push($userIDs, $userID);
                                    }
                                }
                                if($row['client'] && !in_array($row['client'],$userIDs)) {
                                    array_push($userIDs, $row['client']);
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 10px">
                <div class="col-md-4">
                    <div class="lio-modal" style="opacity: 0.1">
                        <div class="header">
                            <h5 class="title">Fájlok</h5>
                        </div>
                        <div class="body">
                            <form class="form-control" method="post" enctype="multipart/form-data">
                                <label> Tallózás
                                    <input type="file" class="form-control-file">
                                </label>
                                <input type="submit" class="btn btn-outline-black" value="Feltöltés">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <?php

        $pieces = explode(",", $result['users']);
        foreach ($pieces as $userID) {
            if (is_numeric($userID) && !in_array($userID, $userIDs)) {
                array_push($userIDs, $userID);
            }
        }
        if($result['client'] && !in_array($result['client'],$userIDs)) {
            array_push($userIDs, $result['client']);
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

else:
    require_once "methods.php";


    ?>

    <div style="padding: 1em;">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Projektek</h5>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                    <span class="plus-icon addP"></span>
                    <?php endif; ?>
                </div>
                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">ID</div>
                        <div class="title">Név</div>
                        <div class="users">Felhasználók</div>
                        <div class="category">Kategória</div>
                        <div class="client">Megrendelő</div>
                        <div class="status">Állapot</div>
                        <div class="billing">Számlázás</div>
                        <div class="price">Ár</div>
                        <div class="date long">Határidő</div>
                        <div class="actions">Műveletek</div>
                    </div>

                    <?php
                    $sql = "SELECT id,users,title,category,client,status,billing,price,created,start_time,deadline,`order` FROM project;";

                    if ($connection) :
                        $userIDs = array();
                        $result = mysqli_query($connection, $sql);



                        while ($row = mysqli_fetch_array($result)) {

                            ?>
                            <div class="dragged" data-order="<?php echo $row['order'] ?>">
                                <button class="dragButton"><span class="toggler-icon"></span></button>

                                <div class="id short"><?php echo $row['id'] ?></div>
                                <div class="title"><?php echo $row['title'] ?></div>
                                <div class="users" data-id="<?php echo $row['id'] ?>"><?php echo $row['users'] ?></div>
                                <div class="category"><?php echo $row['category'] ?></div>
                                <div class="client"><?php echo $row['client'] ?></div>
                                <div class="status"><?php echo $row['status'] ?></div>
                                <div class="billing"><?php echo $row['billing'] ?></div>
                                <div class="price"><?php echo $row['price'] ?></div>
                                <div class="date long"><?php echo str_replace($search,$replace,$row['deadline']) ?></div>
                                <div class="actions">
                                    <span class="hoverIcon seeDetails details-icon" onclick="editProject('<?php echo $row['id'] ?>')"></span>
                                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                    <span class="hoverIcon removeLine remove-icon" onclick="removeProject('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>
                                    <?php endif; ?>
                                </div>

                            </div>


                            <?php


                            $pieces = explode(",", $row['users']);
                            foreach ($pieces as $userID) {
                                if (is_numeric($userID) && !in_array($userID, $userIDs)) {
                                    array_push($userIDs, $userID);
                                }
                            }
                            if($row['client'] && !in_array($row['client'],$userIDs)) {
                                array_push($userIDs, $row['client']);
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
    Demiran.applyDragNDrop(".drag-container", ".dragged");

</script>
                </div>
                <div class="footer btn-group mr-2" style="display:none;">
                    <button class="btn btn-outline-dark mb-2 mr-sm-2 addP" type="button">Új Projekt Felvétele</button>
                </div>


                <div class="addProject" style="display: none">
                    <form style="padding: 10px 15px;" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Cím
                            <input type="text" class="form-control" name="title" id="title" placeholder="Cím" required/>
                            </label>
                            <label for="users">Hozzárendelt felhasználók</label>
                            <select class="form-control" name="users[]" id="users" multiple>
                                <?php echo geUsersAsOptions($connection); ?>
                            </select>

                            <label for="category">Kategória
                            <input type="text" class="form-control" name="category" id="category" placeholder="Kategóriák">
                            </label>
                            <label for="client">Megrendelő
                            <select class="form-control"  name="client" id="client" >
                                <?php echo getClientsAsOptions($connection); ?>
                            </select>
                            </label>

                            <label for="billing">Számlázás
                            <select class="form-control" name="billing" id="billing" >
                                <option value="fixed">Fix</option>
                                <option value="time-based">Időarányos</option>
                            </select></label>

                            <label for="price">Ár/Ársáv
                            <input type="text" class="form-control" name="price" id="price" placeholder="100,000Ft">
                            </label>


                            <label>Kezdés
                                <input type="date" class="form-control" name="start_time" value="" min="2020-10-01" max="2030-12-31">
                            </label>
                            <label>Határidő
                                <input type="date" class="form-control" name="deadline" value="" min="2020-10-01" max="2030-12-31">
                            </label>
                            <input type="text" class="form-control" name="addproject" style="display:none;" value="1"/>

                        </div>

                    </form>
                </div>
                <script>

                    const removeProject = function (id, title) {
                       Demiran.openPopUp("Jóváhagyás", "Biztonsan törölni szeretnéd ezt a projektet az adatbázisból? <br> " + id + " - " + title, [
                            {
                                value:"Igen",
                                onclick: (closeDialog)=>{
                                    closeDialog();
                                    Demiran.post("process.php", 'deleteproject=' + id, function (e, result) {
                                        console.log(result);
                                        if (result.trim() === "OK") {
                                            location.reload();
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

                    const editProject = function (id) {
                        navigate("./projects.php?id="+id);
                        return false;
                    };


                    const registerButton = document.querySelector(".addP");
                    if(registerButton){
                        registerButton.onclick = function () {
                            const form = document.querySelector(".addProject form");

                            if(form){
                                const cln = form.cloneNode(true);

                                const popup = Demiran.openPopUp("Hozzáadás", cln, [
                                    {
                                        value:"Hozzáad",
                                        onclick: (closeDialog, modalID)=>{
                                            const modal = document.querySelector("#"+modalID);
                                            const form = modal.querySelector("form");
                                            form.submit();
                                            closeDialog();
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
                </script>
            </div>
        </div>
    </div>
    </div>
<?php

endif;
footer(); ?>
 </body></html>
