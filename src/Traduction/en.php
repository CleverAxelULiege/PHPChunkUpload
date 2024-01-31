<?php

return [
    "file" => [
        "no_file_name" => "No file name received.",
        "unallowed_extension" => "Your file type isn't allowed. The authorized extensions are : :PLACEHOLDER",
        "no_file_size" => "No file size received.",
        "file_too_big" => "File too big. It shouldn't exceed :PLACEHOLDER bytes.",
        "empty_file" => "Empty file.",
        "no_duration" => "No recording duration received.",
        "recording_too_long" => "The length of your recording is too long. It shouldn't exceed :PLACEHOLDER seconds.",
        "recording_too_short" => "The length of your recording can't be less or equals than :PLACEHOLDER second(s).",
    ],
    "loader" => [
        "start" => "Please wait a moment, we're in the process of requesting permission to use your available devices if we do not have them already."
    ],
    "time" => [
        "minute" => " minute",
        "second" => " second",
        "minutePlural" => " minutes",
        "secondPlural" => " seconds",
        "separator" => " and ",
        "placeholder" => ":MINUTE_NUMBER:MINUTE_NAME:SEPARATOR:SECOND_NUMBER:SECOND_NAME"
    ],
    "device" => [
        "audio" => "Audio device",
        "video" => "Video device"
    ],
    "recorder" => [
        "main" => "You can record your audio and/or video message from here by pressing the button below.",
        "leaveWhileRecording" => "You're recording a video/audio track, do you really want to quit? This will stop the recording.",
        "overwritePreviousRecording" => "You're about to record a new recording, this will erase the previous one, continue?",
        "notificationStartRecording" => "Start the recording",
        "notificationTimeoutRecording" => "Time up! Recording cannot last (approximately) longer than",
        "notificationLimitSizeReachedRecording" => "Maximum recording size reached. Recording cannot be (approximately) larger than",
        "downloadRecordingAtEnd" => "Automatically download the recording at the end. (In webm format)",
        "dontRecordOscilloscope" => "Do not record the oscilloscope if possible (<i>this will reduce the recording size if you only record a voice message</i>).",
        "video" => [
            "disable" => "Video device disabled.",
            "unavailable" => "Video device unavailable.<br> You still have the possibility to record an audio message.",
            "button" => [
                "stop" => "Stop the recording",
                "pause" => "Pause the recording",
                "resume" => "Resume the recording",
                "start" => "Start the recording",
                "toggleVideoDevice" => "Enable/disable the camera",
                "requestFullScreen" => "Enable/disable the fullscreen"
            ]
        ]
    ],
    "recorded" => [
        "main" => "Your recorded message",
        "button" => [
            "upload" => "Upload your recorded message",
            "download" => "Download your recorded message"
        ],
        "confirmUpload" => "You're about to upload your recording. We recommend that you have a WI-FI or a wired connection to perform this task. Continue ?"
    ],
    "errorMessages" => [
        "device" => [
            "default" => "It is not possible to record a video from the site.<br><br>However, you always have the option of uploading a personally recorded video.",
            "unavailableAudioDeviceVideoDevice" => "No recording device (audio or video) has been detected.",
            "unavailablePermissionToUseDevices" => "You have not given your consent to use your microphone and/or camera.",
            "unavailablePermissionToUseAudioDeviceWithVideoDevice" => "You can't record a video without sound, so please give your consent to use your microphone.",
            "unavailableMediaRecorderMediaStream" => "Your browser does not support two useful functions for registering on the site.",
            "unknownError" => "An unknown error has occurred. Please contact the person responsible."
        ]
    ],
    "upload" => [
        "inProgress" => "Your recording is being uploaded. Please do not leave this page.",
        "processing" => "Your recording is being processed.",
        "complete" => "Upload complete !",
        "error" => "An error occured while uploading",
        "noConnection" => "No internet connection"
    ]
];
