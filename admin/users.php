<?php
require_once('../config.php');
require_once("./auth.php");
require_once("./template.php");

?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Users - Secured Page</title>
        <?php admin_head(); ?>
    </head>
<body>
<?php
require_once('./backend/main.php');
admin_header_menu();
require_once "process.php";

if (isset($_GET['id'])):

    $sql = "SELECT * FROM users WHERE id='" . $_GET['id'] . "';";

    if (isset($connection) && $connection):
        $result = mysqli_fetch_array(mysqli_query($connection, $sql))
        ?>
        <div class="top_outer_div">

            <div class="row">
                <div class="col-md-4">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">
                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                <span class="back-icon" style="margin-right: 10px;height: 1.3em;width: 1.3em;" onclick="navigate('./users.php')"></span>
                                <?php endif; ?>
                                <?php echo $result['username']; ?></h5>
                        </div>
                        <div class="body">
                            <?php if(isset($result['image']) && $result['image'] != ""): ?>
                            <img <?php

                            if(file_exists('./uploads/'.$result['image'])){
                                echo 'src="./uploads/'.$result['image'].'"';
                            } else {
                                echo 'src=""';
                            }

                            ?> alt="profile-picture"
                                 style=" width: 100%;">
                            <?php
                            endif;
                            echo "<table class='table'>";

                            echo "<tr>
                            <td>ID</td><td>" . $result['id'] . "</td></tr>
                            <tr><td>Felhasználónév</td><td>" . $result['username'] . "</td></tr>
                            <tr><td>E-mail</td><td>" . $result['email'] . "</td></tr>
                            <tr><td>Szerep</td><td>" . $result['role'] . "</td></tr>
                            <tr><td>Beosztás</td><td>" . $result['job'] . "</td></tr>
                            <tr><td>Dátum</td><td>" . $result['trn_date'] . "</td></tr>";

                            echo "</table>";
                            ?>
                        </div>
                    </div>
                </div>
                <?php if($_SESSION["username"] == $result['username']) : ?>
                <div class="col-md-8">

                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">Műveletek</h5>
                        </div>
                        <div class="body">

                            <div class="form-group inline">
                                <div class="col-sm-7">
                                    <h4>GDPR - Adatkezelés</h4>
                                </div>
                            </div>
                            <div class="form-group inline" >
                                <div class="col-sm-3 col-sm-offset-1 input-lg">
                                    Saját adatok letöltése

                                </div>
                                <div class="col-sm-7">
                                    <input id="gdpr_data_download" type="button" class="btn btn-outline-dark" value="Adatok letöltése">
                                </div>
                            </div>
                            <div class="form-group inline">
                                <div class="col-sm-7">
                                    <h4>Jelszó Módosítás</h4>
                                </div>
                            </div>

                            <form>
                                <div class="form-group inline">
                                    <div class="col-sm-7">
                                        <label>Régi Jelszó
                                            <input id="old_pass" type="password" class="form-control" value="" autocomplete="on">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group inline">
                                    <div class="col-sm-7">
                                        <label>Új Jelszó
                                            <input id="new_pass" type="password" class="form-control" value="" autocomplete="off">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group inline">
                                    <div class="col-sm-7">
                                        <label>Új Jelszó Megerősítése
                                            <input id="new_pass_again" type="password" class="form-control" value="" autocomplete="off">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group inline">
                                    <div class="col-sm-7">
                                        <input type="button" class="btn btn-outline-dark password_change_button" value="Mentés">
                                    </div>
                                </div>
                            </form>
                            <script>
                                const password_change_button = document.querySelector(".password_change_button");
                                if (password_change_button) {
                                    password_change_button.onclick = function (e){
                                        e.preventDefault();

                                        const old_pass = document.getElementById("old_pass");
                                        const new_pass = document.getElementById("new_pass");
                                        const new_pass_again = document.getElementById("new_pass_again");
                                        if(old_pass && new_pass && new_pass_again) {
                                            // Validate
                                            const old_pass_value = old_pass.value;
                                            const new_pass_value = new_pass.value;
                                            const new_pass_again_value = new_pass_again.value;

                                            if (!old_pass_value || !new_pass_value || ! new_pass_again_value) {
                                                Demiran.alert("Valamelyik mező hiányzik, kérlek pótold!");
                                                return;
                                            }
                                            Demiran.openPopUp("Jóváhagyás", "Biztonsan meg szeretnéd változtatni a jelszavad?", [
                                                {
                                                    value:"Igen",
                                                    onclick: (closeDialog)=>{
                                                        closeDialog();
                                                        Demiran.call("change_user_pass", 'change_user_pass=' + old_pass_value + '&new_pass=' + new_pass_value + '&new_pass_again=' + new_pass_again_value, function (e, result) {
                                                            if (!e && result.trim() === "OK") {
                                                                Demiran.alert("Jelszó változtatás sikeres!");
                                                            } else {
                                                                if(typeof result === "string"){
                                                                    Demiran.alert(result);
                                                                } else {
                                                                    Demiran.alert("Jelszó változtatás sikertelen, nézd meg a konzolt a részletekért!");
                                                                }

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

                                const gdpr_data_download = document.getElementById("gdpr_data_download");
                                if(gdpr_data_download) {
                                    gdpr_data_download.onclick = function (e) {
                                        e.preventDefault();
                                        Demiran.openPopUp("Jóváhagyás", "Biztonsan le szeretnéd tölteni az összes hozzád kapcsolódó anyagot?", [
                                            {
                                                value:"Igen",
                                                onclick: (closeDialog)=>{
                                                    closeDialog();
                                                    Demiran.call("get_gdpr_data", "", function (e, result) {
                                                        if(result && !e) {
                                                            const timeId = Math.floor(new Date().getTime()/360000);
                                                            Demiran.downloadData("adatok-"+timeId+"-.demiran.json", result, "text/plain");
                                                        } else {
                                                            Demiran.alert("Az adatok lekérése sikertelen! Kérlek vedd fel a kapcsolatot a rendszergazdával!");
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

                            </script>

                        </div>
                    </div>

                </div>
                <?php endif; ?>
            </div>
        </div>


    <?php


    endif;

else:

    ?>

    <div class="top_outer_div">
    <div class="row">
        <div class="col-md-12">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Felhasználók</h5>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                    <span class="plus-icon register"></span>
                    <?php endif; ?>
                </div>
                <style>

                </style>
                <div class="body users drag-container" style="overflow-x: hidden;overflow-y: scroll;max-height: 70vh;">

                    <div class="dragTitle">
                        <button class="dragButton"><span class="toggler-icon"></span></button>
                        <div class="id short">ID</div>
                        <div class="name">Név</div>
                        <div class="email long">E-mail</div>
                        <div class="role">Szerep</div>
                        <div class="job">Beosztás</div>
                        <div class="work_time">Munkaidő</div>
                        <div class="date long">Dátum</div>
                        <div class="actions">Műveletek</div>
                    </div>



                    <?php
                    $translate_from = array(
                        "admin",
                        "client",
                        "member",
                        "owner"
                    );
                    $translate_to = array(
                        "Admin",
                        "Megrendelő",
                        "Tag",
                        "Tulajdonos"
                    );
                    $sql = "SELECT id,username,email,role,job,work_time,trn_date FROM users;";

                    if (isset($connection)) :
                        $result = mysqli_query($connection, $sql);
                        while ($row = mysqli_fetch_array($result)) {
                            $work_time_from = "";
                            $work_time_to = "";
                            if (strpos($row['work_time'], '-') !== false) {
                                $parts = explode("-", $row['work_time']);
                                if(count($parts) > 1){
                                    $work_time_from = $parts[0];
                                    $work_time_to = $parts[1];
                                }

                            }
                        ?>
                        <div class="dragged">
                            <button class="dragButton"><span class="toggler-icon"></span></button>
                            <div class="id short"><?php echo $row['id']; ?></div>
                            <div class="name"><?php echo $row['username']; ?></div>
                            <div class="email long"><?php echo $row['email']; ?></div>
                            <div class="role"><?php echo str_replace($translate_from, $translate_to,$row['role']); ?></div>
                            <div class="job"><?php echo $row['job']; ?></div>
                            <div class="static-column work_time" style="width: auto"><?php echo $row['work_time']; ?></div>

                            <div class="editable-column" style="width: auto">
                                <input name="from" class="from" data-id="<?php echo $row['id']; ?>" value="<?php echo $work_time_from; ?>" type="time">
                                <input name="to" class="to" data-id="<?php echo $row['id']; ?>" value="<?php echo $work_time_to; ?>" type="time">
                                <span class="pencil-icon"></span>
                            </div>

                            <div class="date long"><?php echo $row['trn_date']; ?></div>
                            <div class="actions">
                                <span class="hoverIcon seeDetails details-icon" onclick="openUser('<?php echo $row['id'] ?>')"></span>

                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                    <span class="hoverIcon removeLine edit-icon" onclick="editUser('<?php echo $row['id'] ?>','<?php echo $row['username'] ?>')"></span>

                                    <span class="hoverIcon removeLine remove-icon" onclick="removeUser('<?php echo $row['id'] ?>','<?php echo $row['username'] ?>')"></span>

                                <?php endif; ?>
                            </div>
                        </div>

                            <?php

                    }
                    endif;
                    ?>

                    <script>
                        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                        const staticColumns = document.querySelectorAll(".static-column");
                        const editableColumns = document.querySelectorAll(".editable-column > span");


                        staticColumns.forEach(function (column) {
                            column.onclick = function () {
                                const editable = column.nextElementSibling;
                                if (editable && editable.classList.contains("editable-column")) {
                                    editable.style.display = "flex";
                                    column.style.display = "none";
                                }
                            }

                        });

                        editableColumns.forEach(function (span){

                            span.onclick = function () {
                                const column = span.parentNode;
                                const staticColumn = column.previousElementSibling;
                                if (staticColumn && staticColumn.classList.contains("static-column")) {
                                    staticColumn.style.display = "block";
                                    column.style.display = "none";

                                    const fromTime = column.querySelector(".from");
                                    const toTime = column.querySelector(".to");

                                    if (fromTime && toTime && fromTime.value && toTime.value) {
                                        const id = fromTime.getAttribute("data-id") || toTime.getAttribute("data-id");

                                        Demiran.call("update_worktime", 'updateworktime=1&from=' + fromTime.value + '&to=' + toTime.value + '&id=' + id, function (e, result) {
                                            console.log(result);
                                            if (result.trim() === "ok") {
                                                staticColumn.innerHTML = fromTime.value + "-" + toTime.value;
                                            }
                                        });

                                    }
                                }
                            }
                        });
                        <?php endif; ?>

                        //Demiran.applyDragNDrop(".drag-container", ".dragged");
                    </script>
                </div>



                <div class="registerForm" style="display: none">
                    <form style="padding: 10px 15px;" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Felhasználó
                            <input type="text" class="form-control" name="username" id="username"
                                   placeholder="Felhasználói név" required/></label>

                            <label for="email">E-mail
                            <input type="text" class="form-control" name="email" id="email" placeholder="Email"></label>

                            <label for="password">Jelszó

                            <input type="password" class="form-control" name="password" id="password" autocomplete="on"
                                   placeholder="Jelszó"></label>

                            <label for="password_confirmation">Jelszó Megerősítése
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" autocomplete="on"
                                   placeholder="Jelszó Megerősítése"></label>


                            <label for="image">Kép
                            <input type="file" class="form-control" id="file-selector" name="image"
                                   accept=".jpg, .jpeg, .png"></label>

                            <div id="drop-area" style="width: 100%; height: 100px; "></div>
                            <input type="text" style="display:none;" id="image" name="adduser">


                            <label for="role">Szerep
                            <select class="form-control" name="role" id="role">
                                <option value="member">Tag</option>
                                <option value="client">Megrendelő</option>
                                <option value="owner">Tulajdonos</option>
                                <option value="admin">Adminisztrátor</option>
                                <option value="other">Egyéb</option>
                            </select></label>

                            <input type="hidden" name="work_time" value="" id="work_time">
                            <label for="from">Kezdés<input type="time" id="from"></label>
                            <label for="to">Befejezés<input type="time" id="to"></label>

                            <label for="job">Beosztás
                            <input type="text" class="form-control" name="job" id="job"
                                   placeholder="Backend Developer"/></label>

                        </div>

                    </form>
                </div>
                <script>


                    const removeUser = function (id, username) {
                        Demiran.confirm("Jóváhagyás","Biztonsan törölni szeretnéd ezt a felhasználót az adatbázisból? <br> " + id + " - " + username,  result => {
                            if (result) {
                                Demiran.call("delete_user", 'deleteuser=' + id, function (e, result) {
                                    console.log(result);
                                    if (result.trim() === "OK") {
                                        location.reload();
                                    }
                                });
                            }
                        });

                        return false;
                    };

                    const openUser = function (id) {
                        navigate("./users.php?id=" + id);
                        return false;
                    };

                    const editUser = function (id) {
                        const form = document.querySelector(".registerForm form");
                        if (form) {

                            Demiran.call("get_user", 'userid=' + id, function (e, result) {
                                let json = null;
                                try {
                                    json = JSON.parse(result);
                                }catch (e) {
                                    console.error(e);
                                }

                                if(json){
                                    const cln = form.cloneNode(true);
                                    const idNode = document.createElement("input");
                                    idNode.setAttribute("type", "hidden");
                                    idNode.setAttribute("name", "id");
                                    idNode.value = id;
                                    cln.appendChild(idNode);

                                    const imageInput = cln.querySelector("#file-selector");
                                    const imageDropArea = cln.querySelector("#drop-area");
                                    const username = cln.querySelector("#username");
                                    const email = cln.querySelector("#email");
                                    const role = cln.querySelector("#role");
                                    const fromNode = cln.querySelector("#from");
                                    const to = cln.querySelector("#to");
                                    const job = cln.querySelector("#job");


                                    if(imageInput) {
                                        imageInput.parentElement.outerHTML = "";
                                    }
                                    if(imageDropArea){
                                        imageDropArea.outerHTML = "";
                                    }
                                    if(username && email && role && fromNode && to && job) {
                                        username.value = json.username;
                                        email.value = json.email;
                                        role.value = json.role;
                                        fromNode.value = json.from;
                                        to.value = json.to;
                                        job.value = json.job;
                                    }
                                    const popup = Demiran.openPopUp(json.username, cln, [
                                        {
                                            value:"Frissítés",
                                            onclick: (closeDialog, modalID)=>{
                                                const modal = document.querySelector("#"+modalID);
                                                const form = modal.querySelector("form");

                                                if(form){
                                                    const fromTime = form.querySelector("#from");
                                                    const toTime = form.querySelector("#to");
                                                    const work_time = form.querySelector("#work_time");
                                                    if (fromTime && toTime && work_time && fromTime.value && toTime.value) {
                                                        work_time.setAttribute("value", fromTime.value + "-" + toTime.value);
                                                        console.log(work_time.value);
                                                    }
                                                }
                                                closeDialog();
                                                Demiran.call("update_user",Demiran.convertToFormEncoded(form),function(error,result){
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
                        return false;
                    };


                    const registerButton = document.querySelector(".register");
                    let jobCache = "";
                    if (registerButton) {
                        registerButton.onclick = function () {
                            const form = document.querySelector(".registerForm form");

                            if (form) {


                                const cln = form.cloneNode(true);
                                const popup = Demiran.openPopUp("Hozzáadás", cln, [
                                    {
                                        value:"Hozzáad",
                                        onclick: (closeDialog, modalID)=>{
                                            const modal = document.querySelector("#"+modalID);
                                            const form = modal.querySelector("form");

                                            if(form){
                                                const fromTime = form.querySelector("#from");
                                                const toTime = form.querySelector("#to");
                                                const work_time = form.querySelector("#work_time");
                                                if (fromTime && toTime && work_time && fromTime.value && toTime.value) {
                                                    work_time.setAttribute("value", fromTime.value + "-" + toTime.value);
                                                    console.log(work_time.value);
                                                }
                                            }
                                            closeDialog();
                                            Demiran.call("add_user",Demiran.convertToFormEncoded(form),function(error,result){
                                                if(!error && result.trim() === "OK"){
                                                    location.reload();
                                                } else {
                                                    Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                    console.log(result,error);
                                                }
                                            });
                                            //form.submit();

                                        }
                                    },
                                    {
                                        value:"Vissza",
                                        type:"close"
                                    }
                                ]);

                                const role = popup.node.querySelector("#role");
                                const job = popup.node.querySelector("#job");
                                if (role && job) {
                                    role.onchange = function () {
                                        const value = role.getAttribute("value");
                                        const list = [];
                                        role.querySelectorAll("option").forEach(node => list.push(node.value));
                                        if (list[role.selectedIndex] === "client") {
                                            job.disabled = true;
                                            jobCache = job.getAttribute("value");
                                            job.setAttribute("value", "Megrendelő");
                                        } else {
                                            job.disabled = false;
                                            job.setAttribute("value", jobCache || "");
                                        }
                                        console.log(value, role.selected, role.selectedIndex, list[role.selectedIndex]);

                                    }

                                }
                                console.log(role);
                                Demiran.activateFileDrop("#"+popup.id+" #image", '#'+popup.id+' #drop-area', "#"+popup.id+" #file-selector");


                            }
                        }
                    }
                </script>
            </div>

        </div>


    </div>
<?php

endif;
?>
 <?php footer(); ?>
</body></html>
