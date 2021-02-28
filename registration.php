<?php
exit();

require_once('config.php');
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztrációs űrlap</title>
    <link rel="stylesheet" href="style.css"/>
    <?php head(); ?>
</head>
<body>
<?php
headerHTML();
if (isset($_REQUEST['username'])) {
    $username = stripslashes($_REQUEST['username']);
    $username = mysqli_real_escape_string($connection, $username);
    $email = stripslashes($_REQUEST['email']);
    $email = mysqli_real_escape_string($connection, $email);
    $password = stripslashes($_REQUEST['password']);
    $password = mysqli_real_escape_string($connection, $password);
    $trn_date = date("Y-m-d H:i:s");
    $query = "INSERT into `users` (username, password, email, trn_date, role, image, job, details, exp, level)
VALUES ('$username', '" . md5($password) . "', '$email', '$trn_date', 'member', '', '', '', '0', '1')";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "<div class='form'>
<h3>Sikeresen Regisztráltál</h3>
<br/>Kattints  <a href='login.php'>IDE a bejelentkezéshez</a></div>";
    }
} else {
    ?>
    <div class="row" style="justify-content: center; margin-top: 20vh">
        <div class="col-md-6">
            <div class="logo">
                <img src="./admin/img/logo_black.svg" style="height: 100%; width: 100%;" alt="demiran Logo">
            </div>

            <form class="login" action="" method="post"
                  style="    text-align: center;    max-width: 440px;margin-left: auto;margin-right: auto">


                <h1>Regisztráció</h1>
                <label>Felhasználói név:
                    <input type="text" class="form-control" name="username" id="username" placeholder="Felhasználói név"
                           required/></label>
                <label>E-mail
                    <input type="text" class="form-control" name="email" placeholder="Email"></label>
                <label>Jelszó:
                    <input type="password" class="form-control" name="password" placeholder="Jelszó"></label>
                <label>Jelszó megerősítése:
                    <input type="password" class="form-control" name="confirmation"
                           placeholder="Jelszó megerősítése"></label>
                <input type="submit" name="submit" value="Regisztráció" class="btn btn-outline-black">
                <p class="login-lost">Már regisztráltál? <a href="login.php">Bejelentkezés</a></p>
            </form>

        </div>
    </div>
<?php } ?>
<?php footer(); ?>
</body>
</html>