<?php

use Surveys\Http\Redirect;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../vendor/autoload.php");
sessionStart();

if(isset($_SESSION["user_id"])){
    Redirect::to("/index.php");
}

?>
<!DOCTYPE html>
<html lang="<?= Traduction::getLng() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/styles/style.css">
    <title>Se connecter</title>
</head>

<body>
    <form action="./post/login.php" method="POST">
        <div>
            <label for="username">Nom utilisateur</label>
            <input type="text" name="username" id="username" autocomplete="off">
        </div>
        <div>
            <label for="passowrd">Mot de passe</label>
            <input type="password" name="password" id="password" autocomplete="off">
        </div>
        <div>
            <input type="submit" value="Envoyer">
        </div>
    </form>
</body>

</html>