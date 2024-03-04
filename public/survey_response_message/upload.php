<?php

use Database\DefaultDatabase;
use Surveys\Repositories\UserRepository;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../../vendor/autoload.php");
sessionStart();

$db = new DefaultDatabase();
$userRepository = new UserRepository($db);
$user = $userRepository->getUserBySession();

// needsToBeLogged($user);

$traduction = Traduction::retrieveSurveyResponseMessage();

define("TRADUCTION", $traduction->traductions);
?>
<!doctype html>
<html lang="<?= $traduction->lng ?>">

<head>
    <meta charset="UTF-8" />
    <!-- https://stackoverflow.com/questions/29454982/javascript-buffer-video-from-blobs-source -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../src/styles/style.css">
    <link rel="stylesheet" href="../src/styles/utils/toggle_switch.css">
    <link rel="stylesheet" href="../src/styles/response_message/upload_progress.css">
    <link rel="stylesheet" href="../src/styles/response_message/upload_file.css">
    <title>Js recorder</title>
</head>

<body >
    <main style="display: none;">
        
        <div id="root">

            <!-- upload un message pré enregistré -->
            <div class="upload_file_container">
                <h2><?= TRADUCTION["uploadFile"]["main"] ?> :</h2>
                <div class="prerecorded_input_container">
                    <label for="prerecorded_file">
                        <svg width="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z" />
                        </svg>
                        <h3><?= TRADUCTION["uploadFile"]["selectFile"] ?></h3>
                        <input type="file" id="prerecorded_file" accept="video/mp4, video/webm">
                    </label>
                </div>
                <h4 class="file_name"></h4>
                <div class="button_upload_container">
                    <button class="upload_prerecorded_video_button" disabled>
                        <?= TRADUCTION["recorded"]["button"]["upload"] ?>
                        <div class="loader_container">
                            <div class="loader"></div>
                        </div>
                    </button>
                </div>
                <hr class="separator">
                <div class="prerecorded_message_upload_error"></div>
            </div>

            <!-- la barre de progrès de l'upload -->
            <div class="upload_progress_container hidden">
                <div class="upload_progress">
                    <div class="progress_bar" role="progressbar" aria-valuemin="0%" aria-valuemax="100%" aria-valuenow="0%">
                        <span class="progress">0%</span>
                        <div class="bar"></div>
                    </div>
                    <div class="message_container">
                        <span class="in_progress hidden"><?= TRADUCTION["upload"]["inProgress"] ?></span>
                        <span class="processing hidden"><?= TRADUCTION["upload"]["processing"] ?></span>
                        <span class="complete hidden"><?= TRADUCTION["upload"]["complete"] ?></span>
                        <span class="error hidden"><?= TRADUCTION["upload"]["error"] ?></span>
                        <span class="no_connection hidden"><?= TRADUCTION["upload"]["noConnection"] ?></span>
                    </div>

                    <div class="close_progress_bar_container hidden">
                        <button class="close_progress_bar_button">OK</button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>

<script type="module" src="../src/script/survey_response_message/main_upload_prerecorded.js"></script>

</html>