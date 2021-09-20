<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2021-08-12
 * Time: 20:57
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
require_once('./backend/main.php');
admin_header_menu();
require_once "process.php";

$search = array(" 00:00:00");
$replace = array("");

$sql = "SELECT id,users,title,category,client,status,billing,price,created,start_time,deadline,`order` FROM project LIMIT 100;";

$projects = sqlGetAll($sql);
$userIDs = getUserIDs($projects);

//$userData = getUsersByIDs($userIDs);


$sql = "SELECT id,users,title,`repeat`,image, state, priority, deadline,`order`,project FROM project_tasks LIMIT 100;";

$project_tasks = sqlGetAll($sql);
$userIDList = getUserIDs($project_tasks);
foreach ($userIDList as $userID) {
    if (!in_array($userID, $userIDs)) {
        array_push($userIDs, $userID);
    }
}
$userData = getUsersByIDs($userIDs);

$availableTypes = array("listed_tasks", "kanban_tasks");
$availableType_labels = array("Lista Mód", "KanBan");
$availableGroups = array("state", "priority");
$availableGroup_labels = array("Státusz", "Fontosság");
$availableAssign = array("all", "me");
$availableAssign_labels = array("Bárki", "ÉN");
$currentType = "listed_tasks";
$groupBy = "state";
$assignedTo = "all";
$selectedProject = null;

if (isset($_GET['type']) && in_array($_GET['type'], $availableTypes)) {
    $currentType = $_GET['type'];
}
if (isset($_GET['group']) && in_array($_GET['group'], $availableGroups)) {
    $groupBy = $_GET['group'];
}
if (isset($_GET['assign']) && in_array($_GET['assign'], $availableAssign)) {
    $assignedTo = $_GET['assign'];
}
if (isset($_GET['project']) && $_GET['project'] !== "") {
    $selectedProject = $_GET['project'];
}

$groupList = array();
foreach ($project_tasks as $task) {
    if($selectedProject === null || ($selectedProject !== null && $task['project'] === $selectedProject)) {
        if (!isset($groupList[$task['project']])) {
            $groupList[$task['project']] = [];
        }
        if (isset($task[$groupBy]) && !in_array($task[$groupBy], $groupList[$task['project']])) {
            array_push($groupList[$task['project']], $task[$groupBy]);
        }
    }

}

?>


<aside id="main_tasks_menu">
    <div class="menu_icon_outer">
        <span class="list-icon" onclick="document.getElementById('hiddenTypeInput').value='listed_tasks';document.querySelector('form.mini_top_filter_bar').submit()"></span>

    </div>
    <div class="menu_icon_outer">
        <span class="kan-ban-icon" onclick="document.getElementById('hiddenTypeInput').value='kanban_tasks';document.querySelector('form.mini_top_filter_bar').submit()"></span>
    </div>
    <div class="menu_icon_outer" style="display: none">
        <a href="#">Naptar</a>
    </div>
</aside>
<section id="main_tasks_content">
    <form class="mini_top_filter_bar navbar" method="get">
        <input id="hiddenTypeInput" type="hidden" name="type" value="<?php echo $currentType; ?>" onchange="document.querySelector('form.mini_top_filter_bar').submit()">

        <ul class="navbar-nav ml-auto">
            <li class="nav-item inline">
                <label for="project_selector">Projekt </label><select id="project_selector" class="project_selector" name="project" onchange="document.querySelector('form.mini_top_filter_bar').submit()">
                    <option value="">Minden Projekt</option>
                    <?php

                    foreach ($projects as $project) {
                        if($selectedProject === null || ($selectedProject !== null && $project['id'] === $selectedProject)) {
                            if(($selectedProject !== null && $project['id'] === $selectedProject)){
                                echo "<option value='" . $project['id'] . "' selected>" . $project['title'] . "</option>";
                            } else {
                                echo "<option value='" . $project['id'] . "'>" . $project['title'] . "</option>";
                            }

                        } else {
                            echo "<option value='" . $project['id'] . "'>" . $project['title'] . "</option>";
                        }


                    }
                    ?>
                </select>
            </li>
            <li class="nav-item inline">
                <label for="groupBy">Csoportosítás </label><select name="group" id="groupBy" class="group" onchange="document.querySelector('form.mini_top_filter_bar').submit()">
                    <?php
                    for ($i = 0; $i < count($availableGroups); $i++) {
                        $group_name = $availableGroups[$i];
                        $group_label = $availableGroup_labels[$i];
                        if ($group_name == $groupBy) {
                            echo "<option value='" . $group_name . "' selected>" . $group_label . "</option>";
                        } else {
                            echo "<option value='" . $group_name . "'>" . $group_label . "</option>";
                        }
                    }
                    ?>
                </select>
            </li>
            <li class="nav-item inline">
                <label for="assignedTo">Hozzárendelt ember </label><select name="assign" id="assignedTo" class="" onchange="document.querySelector('form.mini_top_filter_bar').submit()">
                    <?php
                    for ($i = 0; $i < count($availableAssign); $i++) {
                        $person_name = $availableAssign[$i];
                        $person_label = $availableAssign_labels[$i];
                        if ($person_name == $assignedTo) {
                            echo "<option value='" . $person_name . "' selected>" . $person_label . "</option>";
                        } else {
                            echo "<option value='" . $person_name . "'>" . $person_label . "</option>";
                        }
                    }
                    ?>
                </select>
            </li>
        </ul>

    </form>

    <div class="main_content <?php echo $currentType; ?>">

        <?php

        foreach ($projects as $project) :
            if($selectedProject === null || ($selectedProject !== null && $project['id'] === $selectedProject)):

            ?>
            <div class="project_container lio-modal">
                <div class="project_head header">
                    <h5 class="title"><?php echo $project['title']; ?></h5>
                </div>
                <div class="project_body body">
                    <?php
                    foreach ($groupList as $projectID => $groupKeys) :
                        //sort($groupKeys, SORT_STRING );

                        $filteredGroupKeys = array();
                        $order = array(
                            "high",
                            "medium",
                            "low",
                            "in_progress",
                            "open",
                            "review",
                            "closed",
                        );
                        foreach($order as $o){
                            if(in_array($o,$groupKeys)) {
                                array_push($filteredGroupKeys, $o);
                            }
                        }

                        foreach ($filteredGroupKeys as $group):
                            if ($projectID == $project['id']):
                                ?>
                                <div class="project_group">
                                    <div class="project_group_head<?php

                                    if(in_array($group,array("open", "closed", "in_progress", "review", "high", "medium", "low"))) {
                                        echo " ".$group;
                                    }
                                    ?>">
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


                                        echo str_replace($translate_from, $translate_to,$group); ?>
                                    </div>

                                    <div class="project_body tasks">
                                        <?php


                                        foreach ($project_tasks as $row) :
                                            if ($project['id'] == $row['project'] && $row[$groupBy] == $group):
                                                ?>
                                                <div class="project_task <?php echo $row['state'] ?>" data-order="<?php echo $row['order'] ?>">
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
                                                    <div class="state"><?php echo $row['state'] ?></div>
                                                    <div class="priority">

                                                        <?php /*echo $row['priority'] */ ?>

                                                        <span class="exclamation-mark-icon-<?php echo $row['priority'] ?>"></span>

                                                    </div>
                                                    <div class="date"><?php echo str_replace($search, $replace, $row['deadline']) ?></div>
                                                    <div class="actions">
                                                        <span class="hoverIcon seeDetails details-icon"
                                                              onclick="editTask('<?php echo $row['id'] ?>')"></span>
                                                        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin')): ?>
                                                            <span class="hoverIcon removeLine remove-icon"
                                                                  onclick="removeTask('<?php echo $row['id'] ?>','<?php echo $row['title'] ?>')"></span>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>


                                            <?php endif;endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; endforeach; endforeach; ?>
                </div>
            </div>
        <?php

        endif;
        endforeach;

        ?>
    </div>

    <div class="footer-function-buttons">
        <span class="plus-icon big-icon addTask"></span>
    </div>
    <?php add_task_form();edit_task_form(); ?>
</section>
<?php footer(); ?>
</body></html>