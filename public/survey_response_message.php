<?php

use Database\DefaultDatabase;
use Surveys\Repositories\UserRepository;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../vendor/autoload.php");
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
    <link rel="stylesheet" href="./src/styles/style.css">
    <link rel="stylesheet" href="./src/styles/response_message/recorder.css">
    <link rel="stylesheet" href="./src/styles/response_message/recorded.css">
    <link rel="stylesheet" href="./src/script/utils/video_player/video_player.css">
    <link rel="stylesheet" href="./src/styles/utils/toggle_switch.css">
    <link rel="stylesheet" href="./src/styles/response_message/upload_progress.css">
    <link rel="stylesheet" href="./src/styles/response_message/upload_file.css">
    <title>Js recorder</title>
</head>

<body >
    <main style="display: none;">
        
        <div id="root">

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

            <div class="recorder_container hidden">
                <div class="recorder animation_enter_recorder">
                    <div class="close_button_container">
                        <button class="close_recorder_button">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16" width="12" viewBox="0 0 384 512">
                                <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />
                            </svg>
                        </button>
                    </div>
                    <div class="video_container">
                        <div class="notification_limit_reached" aria-hidden="true" role="button">
                            <span> : <i class="timeout_duration"></i></span>
                        </div>
                        <h3 class="recorder_video_device_disabled hidden"><?= TRADUCTION["recorder"]["video"]["unavailable"] ?></h3>
                        <video id="preview_video" autoplay muted></video>
                        <canvas class="audio_visualizer" hidden=""></canvas>

                        <div class="recorder_buttons_container">

                            <button title="<?= TRADUCTION["recorder"]["video"]["button"]["start"] ?>" class="recorder_rec_button" id="start_recording_button">
                                <div class="popup_start_recording"><span><?= TRADUCTION["recorder"]["notificationStartRecording"] ?></span></div>
                                <span class="title">REC</span>
                                <span class="circle"></span>
                                <span class="time_elapsed"></span>
                            </button>

                            <div class="recorder_action_fs_tv_buttons_container">
                                <button title="<?= TRADUCTION["recorder"]["video"]["button"]["toggleVideoDevice"] ?>" class="recorder_toggle_video_button" id="toggle_video_device_button">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512">
                                        <path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128zM559.1 99.8c10.4 5.6 16.9 16.4 16.9 28.2V384c0 11.8-6.5 22.6-16.9 28.2s-23 5-32.9-1.6l-96-64L416 337.1V320 192 174.9l14.2-9.5 96-64c9.8-6.5 22.4-7.2 32.9-1.6z" />
                                    </svg>
                                </button>

                                <button title="<?= TRADUCTION["recorder"]["video"]["button"]["requestFullScreen"] ?>" class="recorder_request_fullscreen" id="request_fullscreen_button">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512">
                                        <path d="M32 32C14.3 32 0 46.3 0 64v96c0 17.7 14.3 32 32 32s32-14.3 32-32V96h64c17.7 0 32-14.3 32-32s-14.3-32-32-32H32zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7 14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H64V352zM320 32c-17.7 0-32 14.3-32 32s14.3 32 32 32h64v64c0 17.7 14.3 32 32 32s32-14.3 32-32V64c0-17.7-14.3-32-32-32H320zM448 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32V352z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="recorder_action_buttons_container off_screen" aria-hidden="true">
                                <button title="<?= TRADUCTION["recorder"]["video"]["button"]["stop"] ?>" class="recorder_stop_rec_button" id="stop_recording_button">
                                    <span class="square"></span>
                                </button>
                                <button title="<?= TRADUCTION["recorder"]["video"]["button"]["pause"] ?>" class="recorder_pause_resume_rec_button" id="pause_resume_recording_button">
                                    <svg class="pause_icon" xmlns="http://www.w3.org/2000/svg" height="16" width="10" viewBox="0 0 320 512">
                                        <path d="M48 64C21.5 64 0 85.5 0 112V400c0 26.5 21.5 48 48 48H80c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48H48zm192 0c-26.5 0-48 21.5-48 48V400c0 26.5 21.5 48 48 48h32c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48H240z" />
                                    </svg>
                                    <svg class="resume_icon hidden" xmlns="http://www.w3.org/2000/svg" height="16" width="12" viewBox="0 0 384 512">
                                        <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80V432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="error_box hidden"></div>
            <div id="permission_to_record_from_site" class="permission_to_record_from_site">
                <div class="loader_container">
                    <div class="loader"></div>
                    <h2><?= TRADUCTION["loader"]["start"] ?></h2>
                </div>
                <h2><?= TRADUCTION["recorder"]["main"] ?></h2>

                <div class="button_container">
                    <button class="display_recorder_button" id="display_recorder_button">
                        <div class="icon_video">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                <path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128zM559.1 99.8c10.4 5.6 16.9 16.4 16.9 28.2V384c0 11.8-6.5 22.6-16.9 28.2s-23 5-32.9-1.6l-96-64L416 337.1V320 192 174.9l14.2-9.5 96-64c9.8-6.5 22.4-7.2 32.9-1.6z" />
                            </svg>
                        </div>
                        <div class="icon_mic">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                <path d="M192 0C139 0 96 43 96 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM64 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H216V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z" />
                            </svg>
                        </div>
                        <span class="circle"></span>
                    </button>
                </div>

                <hr class="device_separator">

                <div class="devices">
                    <div class="device_container video_device">
                        <div>
                            <label for="video_device_select">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 576 512">
                                    <path d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128zM559.1 99.8c10.4 5.6 16.9 16.4 16.9 28.2V384c0 11.8-6.5 22.6-16.9 28.2s-23 5-32.9-1.6l-96-64L416 337.1V320 192 174.9l14.2-9.5 96-64c9.8-6.5 22.4-7.2 32.9-1.6z" />
                                </svg>
                                <?= TRADUCTION["device"]["video"] ?> :
                            </label>
                            <select name="video_device_select" id="video_device_select"></select>
                        </div>
                    </div>
                    <div class="device_container audio_device">
                        <div>
                            <label for="audio_device_select">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" viewBox="0 0 384 512">
                                    <path d="M192 0C139 0 96 43 96 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM64 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H216V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z" />
                                </svg>
                                <?= TRADUCTION["device"]["audio"] ?> :
                            </label>
                            <select name="audio_device_select" id="audio_device_select"></select>
                        </div>
                    </div>
                </div>

                <div class="options_recorder">

                    <div class="toggle_switch_container">
                        <label for="download_on_stop_recording" class="toggle_switch">
                            <input id="download_on_stop_recording" type="checkbox" />
                            <span class="slider"></span>
                        </label>
                        <label for="download_on_stop_recording"><?= TRADUCTION["recorder"]["downloadRecordingAtEnd"] ?></label>
                    </div>

                    <div class="toggle_switch_container">
                        <label for="dont_record_oscilloscope" class="toggle_switch">
                            <input id="dont_record_oscilloscope" type="checkbox" />
                            <span class="slider"></span>
                        </label>
                        <label for="dont_record_oscilloscope"><?= TRADUCTION["recorder"]["dontRecordOscilloscope"] ?></label>
                    </div>
                </div>
            </div>

            <div class="recorded_element_container hidden">
                <h2><?= TRADUCTION["recorded"]["main"] ?> :</h2>

                <div class="video_player">
                    <div class="process_infinity_video hidden">
                        <div class="loader_container">
                            <div class="loader"></div>
                            <h2>Video en cours de traitement</h2>
                        </div>
                    </div>
                    <video id="recorded_video"></video>
                </div>
                <div class="download_recorded_video_container">
                    <a role="button" class="download_recorded_video_button"><?= TRADUCTION["recorded"]["button"]["download"] ?></a>
                    <button class="upload_recorded_video_button">
                        <?= TRADUCTION["recorded"]["button"]["upload"] ?>
                        <div class="loader_container">
                            <div class="loader"></div>
                        </div>
                    </button>
                </div>

                <hr class="separator">

                <div class="recorded_message_upload_error hidden"></div>

            </div>
        </div>
    </main>
</body>
<script>
    const TRADUCTION_RECORDED = <?php echo json_encode(TRADUCTION["recorded"], JSON_UNESCAPED_UNICODE) ?>;
    const TRADUCTION_RECORDER = <?php echo json_encode(TRADUCTION["recorder"], JSON_UNESCAPED_UNICODE) ?>;
    const TRADUCTION_TIME = <?php echo json_encode(TRADUCTION["time"], JSON_UNESCAPED_UNICODE) ?>;
    const TRADUCTION_ERROR_DEVICE = <?php echo json_encode(TRADUCTION["errorMessages"]["device"], JSON_UNESCAPED_UNICODE) ?>;
</script>
<script type="module" src="./src/script/response_message/main.js"></script>

</html>