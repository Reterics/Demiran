<?php
/**
 * Created by PhpStorm.
 * Author: Attila Reterics
 * Date: 2020. 10. 11.
 * Time: 12:05
 */

require('../config.php');
include("./auth.php");
require_once("./template.php");

?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="utf-8">
        <title>Oldal Szerkesztése</title>
        <?php admin_head(); load_tiny_mce();?>
    </head>
    <body>
<?php
require_once('./backend/main.php');
admin_header_menu();
require_once "process.php";

if(isset($_POST['details']) && isset($_POST['id'])){
    // Save attempt
    $text = htmlspecialchars($_POST['details']);
    $title = mysqli_real_escape_string($connection, $_POST['title']);

    $sql = "UPDATE pages SET title='".$title."',details='".$text."'  WHERE ( id=".$_POST['id']." ) ";

    $query = mysqli_query($connection,$sql);
    if($query){
        echo "OK";
    }else{
        echo "ERROR";
    }
}
if(isset($_GET['id'])):
    $sql = "SELECT * FROM pages WHERE id='".$_GET['id']."';";
    $query = mysqli_query($connection,$sql);
    //Error handling is missing
    $row = mysqli_fetch_array($query);

    if(!$row):
        ?>
        <div class="top_outer_div">
            <div class="row" style="justify-content: center;">
                <div class="col-md-3">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">Hiba </h5>
                        </div>
                        <div class="body">
                            Ezzel az ID-vel nem található oldal!
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
        <div class="col-md-3">
            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">
                        <?php echo $row['id']; ?> -  <?php echo $row['title']; ?>
                    </h5>
                </div>

                <div class="body">

                </div>
            </div>
        </div>
        <div class="col-md-9">

            <div class="lio-modal">
                <div class="header">
                    <h5 class="title">Oldal Szerkesztése</h5>
                </div>
                <div class="body">
                    <form method="post">
                        <div class="form-group">
                            <input id="id" name="id" type="hidden" value="<?php if(isset($_GET['id'])) {echo $_GET['id'];} ?>">
                            <label for="title">Cím
                                <input type="text" class="form-control" name="title" id="title" placeholder="Cím" value="<?php echo $row['title']; ?>" required/>
                            </label>
                            <label for="details">Oldal Tartalma</label>
<textarea id="details" name="details">
<?php
if(isset($_POST['details'])){
    echo $_POST['details'];
}
?>
</textarea>

                            <script type="text/javascript">tinymce.init({
                                    selector: 'textarea',
                                    height: 500,
                                    plugins: [
                                        'advlist autolink lists link image charmap print preview anchor',
                                        'searchreplace visualblocks code fullscreen',
                                        'insertdatetime media table contextmenu paste code'
                                    ],
                                    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
                                });</script>
                        </div>
                    </form>
                </div>
                <div class="footer">
        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'owner' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'developer')): ?>

        <input type="button" class="btn btn-outline-black saveButton" value="Mentés">
        <?php endif; ?>
                </div>

                <script>

                    const saveButton = document.querySelector(".saveButton");
                    if(saveButton){
                        saveButton.onclick = function () {
                            const pageContent = tinymce.activeEditor.getContent();

                            document.querySelector("form").submit();
                        }
                    }
                </script>
            </div>
        </div>
    </div>
</div>


    <?php
    endif;
    else:
    ?>
        <div class="top_outer_div">
            <div class="row" style="justify-content: center;">
                <div class="col-md-3">
                    <div class="lio-modal">
                        <div class="header">
                            <h5 class="title">Hiba </h5>
                        </div>
                        <div class="body">
                            Nem található oldal!
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php
endif;
footer()
?>
 </body></html>
