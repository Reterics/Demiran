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
    echo "Base64 data:".$base64_string."\n";
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
    $query = "INSERT into `messages` (source, target, created, message, status, project, attachment)
VALUES ('$source', '$target', '$create', '$message', 'sent', '', '$imgTarget')";
echo $query;
die();
    //$result = mysqli_query($connection, $query);
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
    $sql = "SELECT username FROM users WHERE id=" . $id . ";";

    $result = mysqli_query($connection, $sql);
    if (!$result) {

        return null;
    }

    $userData = mysqli_fetch_array($result);
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
        $sql = "SELECT id,username FROM users WHERE id IN (" . implode(", ", $array) . ")";
        $result = mysqli_query($connection, $sql);
        while ($row = mysqli_fetch_array($result)) {
            $output[$row['id']] = $row['username'];
        }
    }
    return $output;
}

$sources = sqlGetAll($senderSQL);

$sourceCount = count($sources);


?>

<div class="top_outer_div">
    <div class="row">
        <div class="col-md-4">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Üzenetek</h5>
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
                            if ($selectedSource == $otherGuyID) {
                                echo " active";
                            }
                            ?>" data-id="<?php echo $otherGuyID; ?>">
                                <div class="message-user-icon">

                                </div>

                                <div class="message-details" onclick="return openPerson('<?php echo $otherGuyID; ?>')">
                                    <div class="message-source">
                                        <?php

                                        if ($amISource) {
                                            echo $_SESSION['username'] . " - " . $userIDs[$sourceData['target']];
                                        } else {
                                            echo $userIDs[$sourceData['source']] . " - " . $_SESSION['username'];
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


                    $messages = sqlGetAll(getMessagesSQL($selectedSource));
                    $messagesCount = 0;
                    if($messages){
                        $messagesCount = count($messages);
                    }

                    $targetUser = getUserName($selectedSource);
                    ?>


                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title"><?php echo $targetUser; ?></h5>
                </div>
                <div class="body messages-list" style="max-height: 50vh;overflow-y: scroll;">

                    <?php


                    if($messagesCount != 0){
                        foreach (array_reverse($messages) as $message) {
                            if ($userID == $message['source']) {
                                ?>
                                <div class='message-right'>
                                    <span class='message-title'><?php echo $_SESSION['username']; ?></span>
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
                <div class="footer mr-2 btn-group">
                    <form id="message-sender-form" class="form-inline" method="post" action=""
                          style="width: 100%;    justify-content: space-between;">

                        <div class="icons" style="flex: auto;height: 80px;">
                            <div id="drop-area" class="image-icon" style="height: 30px;flex: auto;"></div>
                        </div>

                        <div class="input" style="width: 70%;">
                            <textarea class="form-control" type="text" placeholder="Ide írhatsz" name="message"
                                      style="width: 100%;"></textarea>
                        </div>


                        <input id="image" type="file" style="display: none" accept="image/png, image/jpeg, image/jpg">
                        <input type="hidden" id="attachment" name="attachment"/>
                        <input type="hidden" id="msg_id" name="msg_id"/>


                        <button class="btn btn-outline-black" style="margin-left: 5px">Hozzáad</button>
                        <input<?php

                        if(!$selectedSource){
                            echo " type=\"text\" placeholder=\"Címzett vagy E-mail\"";
                        } else {
                            echo " type=\"hidden\"";
                        }

                        ?> name="target" value="<?php echo $selectedSource; ?>" style="width:100%"> <?php

                        if(!$selectedSource){
                            echo "<input type=\"hidden\" name=\"user-input\" value=\"yes\" />";
                        }
                        ?>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script> function openPerson(a) {
        location.href = location.protocol + "//" + location.host + location.pathname + "?source=" + a
    }

    Demiran.activateFileDrop("form #attachment", 'form #drop-area', "form #image");

    const form = document.getElementById('message-sender-form');
    if(form){
        form.onsubmit = function(e){
            e.preventDefault();
            Demiran.post("",Demiran.convertToFormEncoded(form),function(error,data){
                if(!error){

                }
                console.log(data,error);
            });
        }
    }
    const msg_id = document.getElementById("msg_id");
    if(msg_id){
        msg_id.value = d
    }
</script>
<?php footer(); ?>
</body>
</html>
