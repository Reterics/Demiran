<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2020. 10. 07.
 * Time: 18:29
 */

require('../config.php');
include("./auth.php");
require_once("./template.php");

?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Projektek - Secured Page</title>
        <?php admin_head(); ?>
        <script src="./js/charts/d3v5.js" ></script>
        <script src="./js/charts/d3color.js" ></script>
        <script src="./js/charts/calendar.js" ></script>

    </head>
    <body>
<?php
require_once('./backend/main.php');
admin_header_menu();
require_once "process.php";
$search = array(" 00:00:00");
$replace = array("");

if (isset($_GET['id'])):

    $sql = "SELECT * FROM project WHERE id='".$_GET['id']."';";

    $project_details = sqlGetAll($sql);
    $userIDs = getUserIDs($project_details);

    $sql = "SELECT id,users,title,`repeat`,image, state, priority, deadline,`order` FROM project_tasks WHERE project=" .$project_details[0]['id'].";";

    $project_tasks = sqlGetAll($sql);
    $userIDList = getUserIDs($project_tasks);
    foreach ($userIDList as $userID) {
        if (!in_array($userID, $userIDs)) {
            array_push($userIDs, $userID);
        }
    }
    $userData = getUsersByIDs($userIDs);

    $result = $project_details[0];
        ?>
        <div class="top_outer_div">

            <div class="row">
                <div class="col-md-4">
                    <div class="lio-modal">

                        <div class="header" style=" ">

                            <h5 class="title"><span class="back-icon" style="margin-right: 10px;height: 1.3em;width: 1.3em;" onclick="navigate('./projects.php')"></span><?php echo $result['title']; ?></h5>
                        </div>
                        <div class="body">
                            <table class='table'>
                                <tr><td>ID</td><td><?php echo $result['id'] ?></td></tr>
                                <tr><td>Felhasználók</td><td class='users'><?php setUserIconSpan(selectUserFromArray($result['users'], $userData)); ?></td></tr>
                                <tr><td>Kategóriák</td><td><?php echo $result['category']  ?></td></tr>
                                <tr><td>Megrendelő</td><td class='client'><?php setUserIconSpan(selectUserFromArray($result['client'], $userData))  ?></td></tr>
                                <tr><td>Státusz</td><td><?php
                                        $translate_from = array(
                                            "open",
                                            "in_progress",
                                            "review",
                                            "closed",
                                            "medium",
                                            "low",
                                            "high",
                                        );
                                        $translate_to = array(
                                            "Nyitott",
                                            "Folyamatban",
                                            "Átnézésre vár",
                                            "Lezárva",
                                            "Normál",
                                            "Alacsony",
                                            "Magas",
                                        );


                                        echo str_replace($translate_from, $translate_to, $result['status']);  ?></td></tr>
                                <tr><td>Számlázás</td><td><?php echo $result['billing']  ?></td></tr>
                                <tr><td>Ár</td><td><?php echo $result['price'] ?></td></tr>

                                <tr><td>Készült</td><td><?php echo $result['created']  ?></td></tr>
                                <tr><td>Kezdés</td><td><?php echo $result['start_time'] ?></td></tr>
                                <tr><td>Határidő</td><td><?php echo $result['deadline'] ?></td></tr>
                            </table>

                            <div class='details-container'><?php echo $result['details'] ?></div>

                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="lio-modal">
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


                            foreach ($project_tasks as $row) :
                                ?>
                                <div class="dragged" data-order="<?php echo $row['order'] ?>">
                                    <button class="dragButton"><span class="toggler-icon"></span></button>

                                    <div class="id short"><?php echo $row['id'] ?></div>
                                    <div class="title"><?php echo $row['title'] ?></div>
                                    <div class="users">

                                        <?php
                                        $pieces = explode(",", $row['users']);
                                        foreach ($pieces as $userID) {
                                            setUserIconSpan(selectUserFromArray($userID, $userData));
                                        }

                                         ?></div>
                                    <div class="repeat"><?php echo $row['repeat'] ?></div>
                                    <div class="state">

                                        <?php

                                        $translate_from = array(
                                            "open",
                                            "in_progress",
                                            "review",
                                            "closed",
                                            "medium",
                                            "low",
                                            "high",
                                        );
                                        $translate_to = array(
                                            "Nyitott",
                                            "Folyamatban",
                                            "Átnézésre vár",
                                            "Lezárva",
                                            "Normál",
                                            "Alacsony",
                                            "Magas",
                                        );


                                        echo str_replace($translate_from, $translate_to,$row['state']); ?>
                                       </div>
                                    <div class="priority"><span class="exclamation-mark-icon-<?php echo $row['priority'] ?>"></span></div>
                                    <div class="date long"><?php echo str_replace($search,$replace,$row['deadline']) ?></div>
                                    <div class="actions">
                                        <span class="hoverIcon seeDetails details-icon" onclick="editTask('<?php echo $row['id'] ?>')"></span>
                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                        <span class="hoverIcon removeLine remove-icon" onclick="removeTask('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>
                                <?php endif; ?>
                                    </div>

                                </div>


                                <?php endforeach; ?>
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
    add_task_form($result['id']);
    edit_task_form();
    ?>
<?php
else:
    ?>

    <div class="top_outer_div">
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

                    $projects = sqlGetAll($sql);
                    $userIDs = getUserIDs($projects);
                    $userData = getUsersByIDs($userIDs);
                    foreach($projects as $row) :
                        ?>
                        <div class="dragged" data-order="<?php echo $row['order'] ?>">
                            <button class="dragButton"><span class="toggler-icon"></span></button>

                            <div class="id short"><?php echo $row['id'] ?></div>
                            <div class="title"><?php echo $row['title'] ?></div>
                            <div class="users" data-id="<?php echo $row['id'] ?>">
                                <?php setUserIconSpan(selectUserFromArray($row['users'], $userData)); ?>
                               </div>
                            <div class="category"><?php echo $row['category'] ?></div>
                            <div class="client"><?php setUserIconSpan(selectUserFromArray($row['client'], $userData)) ?></div>
                            <div class="status"><?php echo $row['status'] ?></div>
                            <div class="billing"><?php echo $row['billing'] ?></div>
                            <div class="price"><?php echo $row['price'] ?></div>
                            <div class="date long"><?php echo str_replace($search,$replace,$row['deadline']) ?></div>
                            <div class="actions">
                                <span class="hoverIcon seeDetails details-icon" onclick="openProject('<?php echo $row['id'] ?>')"></span>
                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                    <span class="hoverIcon removeLine edit-icon" onclick="editProject('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>

                                    <span class="hoverIcon removeLine remove-icon" onclick="removeProject('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>
                                <?php endif; ?>
                            </div>

                        </div>
                        <?php
                    endforeach;
                    ?>





   <!-- <script>
        Demiran.applyDragNDrop(".drag-container", ".dragged");
    </script> -->
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
                                <?php echo geUsersAsOptions(); ?>
                            </select>

                            <label for="category">Kategória
                            <input type="text" class="form-control" name="category" id="category" placeholder="Kategóriák">
                            </label>
                            <label for="client">Megrendelő
                            <select class="form-control"  name="client" id="client" >
                                <?php echo getClientsAsOptions(); ?>
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
                            <input type="hidden" class="form-control" name="addproject" value="1"/>

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
                                    Demiran.call("delete_project",Demiran.convertToFormEncoded(form),function(error,result){
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

                    const openProject = function (id) {
                        navigate("./projects.php?id="+id);
                        return false;
                    };

                    const editProject = function (id) {
                        const form = document.querySelector(".addProject form");

                        if (form) {
                            Demiran.call("get_project", 'projectid=' + id, function (e, result) {

                                let json = null;
                                try {
                                    json = JSON.parse(result);
                                }catch (e) {
                                    console.error(e);
                                }
                                console.log(json);
                                if(json){
                                    const cln = form.cloneNode(true);
                                    const idNode = document.createElement("input");
                                    idNode.setAttribute("type", "hidden");
                                    idNode.setAttribute("name", "id");
                                    idNode.value = id;
                                    cln.appendChild(idNode);

                                    const title = cln.querySelector("#title");
                                    const users = cln.querySelector("#users");
                                    const category = cln.querySelector("#category");
                                    const client = cln.querySelector("#client");
                                    const billing = cln.querySelector("#billing");
                                    const price = cln.querySelector("#price");
                                    const start_time = cln.querySelector("[name=start_time]");
                                    const deadline = cln.querySelector("[name=deadline]");
                                    if(title && users && category && client && billing && price && start_time && deadline) {
                                        title.value = json.title;
                                        category.value = json.category;
                                        client.value = json.client;
                                        billing.value = json.billing;
                                        price.value = json.price;
                                        start_time.value = json.start_time.split(" ")[0];
                                        deadline.value = json.deadline.split(" ")[0];

                                        const options = users.querySelectorAll("option");
                                        (json.users || "").split(",").forEach(function(user){
                                            options.forEach(function (option){
                                                console.log(option.getAttribute("value"), user);
                                                if(option.getAttribute("value") === user && user) {
                                                    option.setAttribute("selected", "true");
                                                }
                                            })
                                        });
                                    } else {
                                        console.log("HTML Elemek hiányoznak az ablakból.");
                                    }
                                    const popup = Demiran.openPopUp(json.title, cln, [
                                        {
                                            value:"Frissítés",
                                            onclick: (closeDialog, modalID)=>{
                                                const modal = document.querySelector("#"+modalID);
                                                const form = modal.querySelector("form");

                                                closeDialog();
                                                Demiran.call("update_project",Demiran.convertToFormEncoded(form),function(error,result){
                                                    if(!error && result.trim() === "OK"){
                                                        Demiran.alert("Adatok mentése sikeres!");
                                                    } else {
                                                        Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                        console.log(result,error);
                                                    }
                                                });
                                                //form.submit();

                                            }
                                        },
                                        {
                                            value:"Bezárás",
                                            type:"close"
                                        }
                                    ]);
                                } else {
                                    Demiran.alert("Hibás adat érkezett a szervertől.");
                                }
                            });
                        }
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
                                            //form.submit();
                                            closeDialog();
                                            Demiran.call("add_project",Demiran.convertToFormEncoded(form),function(error,result){
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
                </script>
            </div>
        </div>
    </div>
    </div>
<?php

endif;



?>
<div class="row" style="padding:0 1em">
    <div class="col-md-12">
        <div class="lio-modal">
            <div class="header">
                <h5 class="title">Naptár</h5>
            </div>
            <div class="body" style="height:30vh">
                <div class="v" style="display: flex;justify-content: center;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Demiran.call("get_project_times", 'get_project_times=true<?php
        if(isset($_GET['id'])) {
            echo "&filter_project=".$_GET['id'];
        }
        ?>', function (e, result) {
        //console.log(result);
        console.log(JSON.parse(result));
        const data = JSON.parse(result);
        const inputData = [];
        data.forEach(function(d){
            if(d){
                if (d.start_time) {
                    inputData.push({
                        date:d.start_time,
                        color:1
                    });
                }
                if (d.deadline) {
                    inputData.push({
                        date:d.deadline,
                        color:2
                    });
                }
            }


        });
        drawCalendar2({
            selector:".v",
            data:inputData

        })
    });
</script>
<?php






footer();
?>
 </body></html>
