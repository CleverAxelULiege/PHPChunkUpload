<?php

return [
    "file" => [
        "no_file_name" => "Aucun nom de fichier reçu.",
        "unallowed_extension" => "Votre type de fichier n'est pas autorisé. Les extensions autorisées sont : :PLACEHOLDER",
        "no_file_size" => "Aucune taille de fichier reçue.",
        "file_too_big" => "Fichier trop gros. Il ne devrait pas dépasser :PLACEHOLDER bytes.",
        "empty_file" => "Fichier vide.",
        "no_duration" => "Aucune durée de vidéo reçue.",
        "recording_too_long" => "La durée de votre enregistrement est trop longue. Ça ne peut pas dépasser :PLACEHOLDER secondes.",
        "recording_too_short" => "La durée de votre enregistrement ne peut pas être inférieure ou égale à :PLACEHOLDER seconde(s).",
    ],
    "loader" => [
        "start" => "Veuillez patienter un instant, nous sommes en train de vous demander les permissions d'utiliser vos périphériques disponibles si nous ne les avons pas encore"
    ],
    "time" => [
        "minute" => " minute",
        "second" => " seconde",
        "minutePlural" => " minutes",
        "secondPlural" => " secondes",
        "separator" => " et ",
        "placeholder" => ":MINUTE_NUMBER:MINUTE_NAME:SEPARATOR:SECOND_NUMBER:SECOND_NAME"
    ],
    "device" => [
        "audio" => "Périphérique audio",
        "video" => "Périphérique vidéo"
    ],
    "recorder" => [
        "main" => "Vous pouvez enregistrer votre message audio et/ou vidéo depuis ici en appuyant sur le bouton ci-dessous.",
        "leaveWhileRecording" => "Vous êtes en train d'enregistrer une vidéo/piste audio, souhaitez-vous vraiment quitter ? Cela entrainera l'arrêt de l'enregistrement.",
        "overwritePreviousRecording" => "Vous êtes sur le point d'enregistrer un nouvel enregistrement, ceci effacera l'enregistrement précédent, continuer ?",
        "notificationStartRecording" => "Commencer l'enregistrement",
        "notificationTimeoutRecording" => "Temps écoulé ! L'enregistrement ne peut pas durer (approximativement) plus de",
        "notificationLimitSizeReachedRecording" => "Taille maximale de l'enregistrement atteinte. L'enregistrement ne peut pas faire (approximativement) plus de",
        "downloadRecordingAtEnd" => "Télécharger automatiquement l'enregistrement à sa fin. <i>(Au format WEBM ou MP4, cela dépend de ce que votre navigateur supporte)</i>",
        "dontRecordOscilloscope" => "Ne pas enregistrer l'oscilloscope si possible (<i>cela réduira la taille de l'enregistrement si vous n'enregistrez qu'un message vocal</i>)",
        "video" => [
            "disable" => "Périphérique vidéo désactivé.",
            "disabledByInterviewer" => "Périphérique vidéo désactivé pour cette enquête.",
            "unavailable" => "Périphérique vidéo indisponible.<br>Vous pouvez toujours enregistrer un message audio.",
            "button" => [
                "stop" => "Arrêter l'enregistrement",
                "pause" => "Mettre en pause l'enregistrement",
                "resume" => "Reprendre l'enregistrement",
                "start" => "Commencer l'enregistrement",
                "toggleVideoDevice" => "Activer/désactiver la caméra",
                "requestFullScreen" => "Activer/désactiver le plein écran"
            ]
        ]
    ],
    "recorded" => [
        "main" => "Votre message enregistré",
        "confirmUpload" => "Vous êtes sur le point de mettre en ligne votre enregistrement, nous vous conseillons d'être connecté en WI-FI ou câblé pour effectuer cette tâche. Continuer ?",
        "button" => [
            "download" => "Télécharger votre message enregistré",
            "upload" => "Mettre en ligne votre message enregistré"
        ]
    ],
    "errorMessages" => [
        "device" => [
            "default" => "Il vous est impossible d'enregistrer une vidéo depuis le site.<br><br>Néanmoins, vous avez toujours la possibilité d'upload(ou télécharger en amont) une vidéo enregistrée personnellement.",
            "unavailableAudioDeviceVideoDevice" => "Aucun périphérique (audio ou vidéo) d'enregistrement n'a été détécté.",
            "unavailablePermissionToUseDevices" => "Vous n'avez pas donné votre accord pour utiliser votre micro et/ou votre caméra.",
            "unavailablePermissionToUseAudioDeviceWithVideoDevice" => "Vous ne pouvez pas enregistrer une vidéo sans son, veuillez donner votre accord pour utiliser votre micro.",
            "unavailableMediaRecorderMediaStream" => "Votre navigateur ne supporte pas deux fonctionnalités utiles pour pouvoir enregistrer sur le site.",
            "unavailableMimeType" => "Votre navigateur n'a pas un de ces types MIME/codecs proposés par le site : :PLACEHOLDER",
            "unknownError" => "Une erreur inconnue est survenue. Contactez le responsable."
        ]
    ],
    "upload" => [
        "inProgress" => "Votre enregistrement est en cours de transfert merci de ne pas quitter cette page.",
        "processing" => "Votre enregistrement est en cours de traitement.",
        "complete" => "Transfert terminé !",
        "error" => "Une erreur est survenue au moment du transfert",
        "noConnection" => "Aucune connection internet"
    ],
    "uploadFile" => [
        "main" => "Mettre en ligne un message pré-enregistré",
        "selectFile" => "Sélectionner un fichier à mettre en ligne"
    ]
];
