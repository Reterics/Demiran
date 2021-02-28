<?php
require_once('../config.php');
require_once("auth.php");
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
admin_header_menu();
require_once "process.php";

if (isset($_GET['id'])):

    $sql = "SELECT * FROM users WHERE id='" . $_GET['id'] . "';";

    if (isset($connection) && $connection):
        $result = mysqli_fetch_array(mysqli_query($connection, $sql))
        ?>
        <div style="padding: 1em;">
            <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
            <div class="row" style="margin-bottom: 10px">
                <div class="col-md-12">
                    <a href="./users.php" class="btn btn-outline-primary"> << Vissza</a>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title"><?php echo $result['username']; ?></h5>
                        </div>
                        <div class="body">
                            <img src="./uploads/<?php echo $result['image']; ?>" alt="profile-picture"
                                 style="    width: 100%;">
                            <?php
                            echo "<table class='table'>";

                            echo "<tr>
                            <td>ID</td><td>" . $result['id'] . "</td></tr>
                            <tr><td>Felhasználónév</td><td>" . $result['username'] . "</td></tr>
                            <tr><td>E-mail</td><td>" . $result['email'] . "</td></tr>
                            <tr><td>Szerep</td><td>" . $result['role'] . "</td></tr>
                            <tr><td>Beosztás</td><td>" . $result['job'] . "</td></tr>
                            <tr><td>Exp</td><td>" . $result['exp'] . "</td></tr>
                            <tr><td>Szint</td><td>" . $result['level'] . "</td></tr>
                            <tr><td>Dátum</td><td>" . $result['trn_date'] . "</td></tr>";

                            echo "</table>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                </div>
            </div>
        </div>


    <?php


    endif;

else:

    ?>

    <div style="padding: 1em;">
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
                        <div class="exp" style="display: none">Tapasztalat</div>
                        <div class="work_time">Munkaidő</div>
                        <div class="date long">Dátum</div>
                        <div class="actions">Műveletek</div>
                    </div>



                    <?php
                    $sql = "SELECT id,username,email,role,job,exp,level,work_time,trn_date FROM users;";

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
                            <div class="role"><?php echo $row['role']; ?></div>
                            <div class="job"><?php echo $row['job']; ?></div>
                            <div class="exp" style="display: none">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $row['exp']; ?>" style="width: <?php echo $row['exp'] ?>%" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $row['exp']; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="level short" style="display: none"><?php echo $row['level']; ?></div>
                            <div class="static-column work_time" style="width: auto"><?php echo $row['work_time']; ?></div>

                            <div class="editable-column" style="width: auto">
                                <input name="from" class="from" data-id="<?php echo $row['id']; ?>" value="<?php echo $work_time_from; ?>" type="time">
                                <input name="to" class="to" data-id="<?php echo $row['id']; ?>" value="<?php echo $work_time_to; ?>" type="time">
                                <span class="pencil-icon"></span>
                            </div>

                            <div class="date long"><?php echo $row['trn_date']; ?></div>
                            <div class="actions">
                                <span class="hoverIcon seeDetails details-icon" onclick="editUser('<?php echo $row['id'] ?>')"></span>

                                <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
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
                                        Demiran.post("process.php", 'updateworktime=1&from=' + fromTime.value + '&to=' + toTime.value + '&id=' + id, function (e, result) {
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

                        Demiran.applyDragNDrop(".drag-container", ".dragged");
                    </script>
                </div>
                <div class="footer btn-group mr-2">

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

                            <input type="password" class="form-control" name="password" id="password"
                                   placeholder="Jelszó"></label>


                            <label for="image">Kép
                            <input type="file" class="form-control" id="file-selector" name="image"
                                   accept=".jpg, .jpeg, .png"></label>

                            <div id="drop-area" style="width: 100%; height: 100px; "></div>
                            <input type="text" style="display:none;" id="image" name="adduser">


                            <label for="role">Szerep
                            <select class="form-control" name="role" id="role">
                                <option value="member">Tag</option>
                                <option value="client">Megrendelő</option>
                                <option value="developer">Fejlesztő</option>
                                <option value="tester">Tesztelő</option>
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
                                Demiran.post("process.php", 'deleteuser=' + id, function (e, result) {
                                    console.log(result);
                                    if (result.trim() === "OK") {
                                        location.reload();
                                    }
                                });
                            }
                        });

                        return false;
                    };

                    const editUser = function (id) {
                        navigate("./users.php?id=" + id);
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
                                            form.submit();
                                            closeDialog();
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

</body></html>
