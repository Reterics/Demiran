<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2020. 10. 19.
 * Time: 17:41
 */
require('../config.php');
include("./auth.php");
require_once("./template.php");

function base64_to_image($base64_string)
{
    //echo "Base64 data:".$base64_string."\n";
    $output_file = "./uploads/" . round(microtime(true) * 1000);
    $extension = "";


    $data = explode(',', $base64_string);

    if (count($data)) {
        $new = str_replace(array("data:", ";base64"), array("", ""), $data[0]);
        $parts = explode("/", $new);
        if (count($parts) > 1) {
            $extension = "." . $parts[1];
        }

        $ifp = fopen($output_file . $extension, 'wb');
        fwrite($ifp, base64_decode($data[1]));

        fclose($ifp);
        return $output_file . $extension;
    } else {
        return null;
    }
}

if (isset($_POST['target']) && isset($_POST['message']) && isset($_POST['attachment']) && ($_POST['message'] != "" || $_POST['attachment'] != "")) {
    $source = $_SESSION['id'];
    $target = $_POST['target'];


    if(isset($_POST['user-input']) && $_POST['user-input'] == "yes") {
        //In this case the target is a username or email
        $sql = "SELECT id FROM users WHERE 'email'='".$_POST['user-input']."' OR 'username'='".$_POST['user-input']."';";
    }
    $msg_id = "";
    if(isset($_POST['msg_id'])){
        $msg_id = $_POST['msg_id'];
    }

    $message = mysqli_real_escape_string($connection, stripslashes($_POST['message']));
    //$attachment = $_POST['attachment'];
    /*if (isset($_FILES['attachment'])) {
        $time = date("Ymdhis");
        $attachment = $time . "-" .basename($_FILES['attachment']['name']);

        $target = "./uploads/" . $attachment;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
            $msg = "Image uploaded successfully";
        } else {
            $msg = "Failed to upload image";
        }
    }*/
    $imgTarget = "";
    if($_POST['attachment'] != ""){
        $imgTarget = base64_to_image($_POST['attachment']);
    }

    $create = date("Y-m-d H:i:s");
    $query = "INSERT into `messages` (source, target, created, message, status, project, attachment, msg_id)
VALUES ('$source', '$target', '$create', '$message', 'sent', '', '$imgTarget', '$msg_id')";

    $result = mysqli_query($connection, $query);
    if($result) {
        echo "OK";
    } else {
        echo mysqli_connect_error();
        echo $query;
        //header('HTTP/1.1 500 Internal Server Error', true, 500);
    }
    die();
    //echo $result;
    //echo var_dump(mysqli_fetch_array($result));
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Üzenetek</title>
    <?php admin_head(); ?>
</head>
<body>
<?php
require_once('./backend/main.php');
admin_header_menu();
$userID = $_SESSION['id'];
$userName = $_SESSION['username'];

$myFullName = $userName;
if(isset($_SESSION['full_name']) && $_SESSION['full_name'] != "") {
    $myFullName = $_SESSION['full_name'] . " (".$_SESSION['username'].")";
}

//$senderSQL = "SELECT DISTINCT `source` FROM `messages` WHERE target=".$userID." OR `source`=".$userID.";";

$senderSQL = "SELECT id, `source`, target, message, created, `status`
  FROM messages 
 WHERE id IN (
               SELECT MAX(id)
                 FROM messages 
                GROUP BY `source`, target
             )";


//$senderSQL = "SELECT DISTINCT `source` FROM `messages` WHERE target=".$userID." UNION ALL SELECT DISTINCT `target` FROM `messages` WHERE `source`=".$userID.";";

function getUserName($id)
{
    global $connection;
    $sql = "SELECT username, full_name FROM users WHERE id=" . $id . ";";

    $result = mysqli_query($connection, $sql);
    if (!$result) {

        return null;
    }

    $userData = mysqli_fetch_array($result);
    if(isset($userData['username']) && isset($userData['full_name']) && $userData['full_name'] != ""){
        return $userData['full_name'] . " (".$userData['username'].")";
    }
    return $userData['username'];

}

function getMessagesSQL($source)
{
    global $userID;
    //return "SELECT messages.id, users.username, messages.message,messages.source, messages.target, messages.created, messages.status FROM messages INNER JOIN users ON messages.target=users.id OR messages.source=users.id";
    return "SELECT * FROM `messages` WHERE (`source`=" . $source . " and `target`=" . $userID . ") OR (`source`=" . $userID . " and `target`=" . $source . ") ORDER BY id DESC LIMIT 5;";
}

function getUsers($array)
{
    global $connection;
    $output = [];
    if (count($array)) {
        $sql = "SELECT id,username,full_name FROM users WHERE id IN (" . implode(", ", $array) . ")";
        $result = mysqli_query($connection, $sql);
        while ($row = mysqli_fetch_array($result)) {
            if(isset($row['username']) && isset($row['full_name']) && $row['full_name'] != ""){
                $output[$row['id']] = $row['full_name'] . " (".$row['username'].")";
            } else {
                $output[$row['id']] = $row['username'];
            }

        }
    }
    return $output;
}

$sources = sqlGetAll($senderSQL);

$sourceCount = count($sources);
$new_message = false;
if(isset($_GET['new_message'])){
    $new_message = true;
}


?>

<div class="top_outer_div">
    <div class="row">
        <div class="col-md-4">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Üzenetek</h5>
                    <?php
                    if(!$new_message):
                    ?>
                        <input type="button" class="btn btn-success" value="Új Üzenet" onclick="location.href=location.href.split('?')[0]+'?new_message'">
                    <?php
                    endif;
                    ?>
                </div>
                <div class="body">

                    <?php
                    $selectedSource = null;
                    if ($sourceCount === 0 || ($sourceCount === 1 && $sources[0] === null)):
                        echo "Nincs megjeleníthető tétel.";
                    else:
                        if ($sources[0] && isset($sources[0]['source']) && $sources[0]['source'] != $userID) {
                            $selectedSource = $sources[0]['source'];
                        } else if ($sources[0] && isset($sources[0]['target']) && $sources[0]['target'] != $userID) {
                            $selectedSource = $sources[0]['target'];
                        }
                        if (isset($_GET['source'])) {
                            $selectedSource = $_GET['source'];
                        }else if($new_message){
                            $selectedSource = null;
                        }

                        $userIDs = array();
                        $loadedIDs = array();
                        foreach ($sources as $sourceData) {
                            $selectedID = $sourceData['source'];
                            if ($sourceData['source'] == $userID) {
                                $selectedID = $sourceData['target'];
                            }

                            if (is_numeric($selectedID) && !in_array($selectedID, $userIDs)) {
                                array_push($userIDs, $selectedID);
                            }

                        }
                        $userIDs = getUsers($userIDs);
                        foreach ($sources as $sourceData) {
                            $amISource = $sourceData['source'] == $userID;
                            $otherGuyID = $sourceData['source'];
                            if ($amISource) {
                                $otherGuyID = $sourceData['target'];
                                if (!in_array($sourceData['target'], $loadedIDs)) {
                                    array_push($loadedIDs, $sourceData['target']);
                                } else {
                                    continue;
                                }
                            } else {
                                if (!in_array($sourceData['source'], $loadedIDs)) {
                                    array_push($loadedIDs, $sourceData['source']);
                                } else {
                                    continue;
                                }
                            }
                            ?>

                            <div class="message-menu<?php
                            if ($selectedSource == $otherGuyID && !$new_message) {
                                echo " active";
                            }
                            ?>" data-id="<?php echo $otherGuyID; ?>">
                                <div class="message-user-icon">

                                </div>

                                <div class="message-details" onclick="return openPerson('<?php echo $otherGuyID; ?>')">
                                    <div class="message-source">
                                        <?php

                                        if ($amISource) {
                                            echo $myFullName . " - " . $userIDs[$sourceData['target']];
                                        } else {
                                            echo $userIDs[$sourceData['source']] . " - " . $myFullName;
                                        }
                                        ?>
                                    </div>
                                    <div class="message-excerpt">
                                        <div class="content">
                                            <?php echo substr($sourceData['message'], 0, 30) ?>
                                        </div>
                                        <div class="date">

                                            <?php echo $sourceData['created']; ?>
                                        </div>
                                    </div>
                                    <div class="message-status">
                                        <?php echo $sourceData['status']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php

                        }
                    endif;


                    $messages = $new_message ? null : sqlGetAll(getMessagesSQL($selectedSource));
                    $messagesCount = 0;
                    if($messages){
                        $messagesCount = count($messages);
                    }

                    $targetUser = $new_message ? null : getUserName($selectedSource);
                    ?>


                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title"><?php

                        if(isset($targetUser) && $targetUser != "") {
                            echo $targetUser;
                        } else {
                            echo  "új üzenet küldése";
                        }
                         ?></h5>

                </div>
                <div class="body messages-list" style="max-height: 50vh;overflow-y: scroll;    min-height: 75px;padding:5px">

                    <?php


                    if($messagesCount != 0){
                        foreach (array_reverse($messages) as $message) {
                            if ($userID == $message['source']) {
                                ?>
                                <div class='message-right'>
                                    <span class='message-title'><?php echo $myFullName; ?></span>
                                    <div class='message-box'><?php echo $message['message']; ?></div>
                                    <?php if(isset($message['attachment']) && $message['attachment'] != ""){
                                        echo "<div class='message-box'><img alt='' src='".$message['attachment']."' height='300' /></div>";
                                    } ?>

                                </div>
                                <?php
                            } else {
                                ?>
                                <div class='message-left'>
                                    <span class='message-title'><?php echo $targetUser; ?></span>
                                    <div class='message-box'><?php echo $message['message']; ?></div>
                                    <?php if(isset($message['attachment']) && $message['attachment'] != ""){
                                        echo "<div class='message-box'><img alt='' src='".$message['attachment']."' height='300' /></div>";
                                    } ?>
                                </div>
                                <?php

                            }
                        }
                    } else {
                        echo "Nincs új üzenet!";
                    }

                    ?>
                </div>
                <script>
                    const messagesList = document.querySelector('.messages-list');
                    if(messagesList){
                        messagesList.scrollTop = messagesList.scrollHeight;
                    }

                    Demiran.imageViewer(".message-box > img");
                </script>
                <div class="footer mr-2 btn-group" style="min-height: 110px;">
                    <form id="message-sender-form" class="form-inline" method="post" action=""
                          style="width: 100%;    justify-content: space-between;">

                        <div class="form-group autocomplete" style="width: 100%;margin-bottom:5px;display: inline-block;position: relative;">
                            <input<?php

                            if(!$selectedSource){
                                echo " type=\"text\" placeholder=\"Címzett\"";
                            } else {
                                echo " type=\"hidden\"";
                            }

                            ?> name="target-name" value="<?php echo $selectedSource; ?>" style="width:100%" class="form-control" autocomplete="off">
                            <input type="hidden" name="target" value="<?php echo $selectedSource; ?>">
                            <?php


                            ?>
                        </div>
                        <?php
                        if(!$selectedSource){
                            echo "<input type=\"hidden\" name=\"user-input\" value=\"yes\" />";
                        }
                        ?>

                        <style>
                            .autocomplete-items {
                                position: absolute;
                                border: 1px solid #d4d4d4;
                                border-bottom: none;
                                border-top: none;
                                z-index: 99;
                                /*position the autocomplete items to be the same width as the container:*/
                                top: 100%;
                                left: 0;
                                right: 0;
                            }
                            .autocomplete-items div {
                                padding: 10px;
                                cursor: pointer;
                                background-color: #fff;
                                border-bottom: 1px solid #d4d4d4;
                            }
                            .autocomplete-items div:hover {
                                /*when hovering an item:*/
                                background-color: #e9e9e9;
                            }
                            .autocomplete-active {
                                /*when navigating through the items using the arrow keys:*/
                                background-color: DodgerBlue !important;
                                color: #ffffff;
                            }
                        </style>

                        <div class="form-group" style="width: 100%">
                            <div class="icons form-inline" style="height: 30px;width:40px">
                                <div id="drop-area" class="image-icon" style="height: 30px;  width: 32px; margin-left: auto;margin-right: auto;"></div>
                            </div>
                            <div class="input form-inline" style="flex: 1">
                            <textarea class="form-control" type="text" placeholder="Ide írhatsz" name="message"
                                      style="width: 100%;"></textarea>
                            </div>
                            <input class="btn btn-outline-black" style="margin-left: 5px" value="Hozzáad" type="submit">

                        </div>



                        <input id="image" type="file" style="display: none" accept="image/png, image/jpeg, image/jpg">
                        <input type="hidden" id="attachment" name="attachment"/>
                        <input type="hidden" id="msg_id" name="msg_id"/>




                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const loadedUserList = {};

    function autocomplete(inp, arr) {
        let currentFocus;
        const textList = [];
        for (let i = 0; i < arr.length; i++) {
            let text = "";
            if(typeof(arr[i]) === "string" || typeof(arr[i]) === "number" || typeof(arr[i]) === "boolean") {
                text = arr[i];
                textList.push(text);
            } else if(typeof(arr[i]) === "object") {
                if(arr[i].full_name) {
                    text = arr[i].full_name + " (" + arr[i].username + ")";
                    if(!loadedUserList[text]){
                        loadedUserList[text] = arr[i].id;
                    }
                    if(!loadedUserList[arr[i].username]){
                        loadedUserList[arr[i].username] = arr[i].id;
                    }
                } else {
                    text = arr[i].username;
                    if(!loadedUserList[text]){
                        loadedUserList[text] = arr[i].id;
                    }
                }
                textList.push(text);
            }
        }

        inp.oninput = function(e){
            let a, b, i, val = this.value;
            closeAllLists();
            if (!val) { return false;}
            currentFocus = -1;
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            this.parentNode.appendChild(a);
            for (i = 0; i < textList.length; i++) {
                const text = textList[i];
                if (text.substr(0, val.length).toUpperCase() === val.toUpperCase()) {
                    b = document.createElement("DIV");
                    b.innerHTML = "<strong>" + text.substr(0, val.length) + "</strong>";
                    b.innerHTML += text.substr(val.length);
                    b.innerHTML += "<input type='hidden' value='" + text + "'>";
                    b.addEventListener("click", function(e) {
                        inp.value = this.getElementsByTagName("input")[0].value;
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        };
        inp.onkeydown = function (e){
            const list = document.getElementById(this.id + "autocomplete-list");
            let x;
            if (list) {
                x = list.getElementsByTagName("div");
            }
            if (e.keyCode === 40) {
                currentFocus++;
                addActive(x);
            } else if (e.keyCode === 38) {
                currentFocus--;
                addActive(x);
            } else if (e.keyCode === 13) {
                e.preventDefault();
                if (currentFocus > -1) {
                    if (x) x[currentFocus].click();
                }
            }
        };
        function addActive(x) {
            if (!x) return false;
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }
        function closeAllLists(elmnt) {
            const x = document.getElementsByClassName("autocomplete-items");
            for (let i = 0; i < x.length; i++) {
                if (elmnt !== x[i] && elmnt !== inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }
        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });

    }

    function openPerson(a) {
        location.href = location.protocol + "//" + location.host + location.pathname + "?source=" + a
    }

    Demiran.activateFileDrop("form #attachment", 'form #drop-area', "form #image");

    const form = document.getElementById('message-sender-form');
    if(form){
        form.onsubmit = function(e){
            e.preventDefault();

            const targetNameNode = form.querySelector("[name=target-name]");
            const targetNode = form.querySelector("[name=target]");
            if(targetNameNode && targetNode) {
                const targetName = targetNameNode.value;
                if(targetName && loadedUserList[targetName]) {
                    targetNode.value = loadedUserList[targetName];
                } else if(!Number.isNaN(Number.parseInt(targetName))) {
                    targetNode.value = targetName;
                }
            }


            Demiran.post("",Demiran.convertToFormEncoded(form),function(error,result){
                if(!error && result.trim() === "OK"){
                    location.reload();
                }
                console.log(result,error);
            });
        }
    }
    Demiran.call("get_user_list", "", function(error,result){
        if(!error && result){
            let json = null;
            try {
                json = JSON.parse(result);
            }catch (e){
                console.error(e);
            }
            if(Array.isArray(json)){
                autocomplete(document.querySelector(".autocomplete input"),json);
            }

        }
    });
    const msg_id = document.getElementById("msg_id");
    if(msg_id){
       msg_id.value = new Date().getTime();
    }
</script>
<?php footer(); ?>
</body>
</html>
