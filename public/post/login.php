<?php

use Surveys\Http\Redirect;
use Surveys\Repositories\UserRepository;

require(__DIR__ . "/../../vendor/autoload.php");
requestMethodExpected("POST");

sessionStart();

if (isset($_SESSION["user_id"])) {
    Redirect::to("/index.php");
}

$userRepository = new UserRepository();
$user = $userRepository->getUserByName($_POST["username"]);

if ($user === null || $user->password !== $_POST["password"]) {
    Redirect::to("/login.php");
}

session_regenerate_id();
$_SESSION["user_id"] = $user->id;
Redirect::to("/index.php");
