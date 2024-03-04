if (!testForAsyncFetchAndArrowFunctionAndES6Class()) {
    document.body.innerHTML = `
    <div>
        <h1 style="padding:10px;">Your browser does not support some keys elements for it to function properly on this site. Try updating or changing your browser.</h1>
        <h1 style="padding:10px;">Votre navigateur ne supporte pas des éléments clefs pour fonctionner correctement sur ce site. Essayez de mettre à jour ou de changer de navigateur.</h1>
    </div>`;
    throw "unavailable async/await & fetch API or arrow function or ES6 class.";
}

document.querySelector("main").style.display = "";

import { AudioVisualizer } from "./AudioVisualizer.js";
import { Device } from "./Device.js";
import { Page } from "./Page.js";
import { Recorder } from "./Recorder.js";
import { UploadManager } from "./UploadManager.js";
import { VideoPlayer } from "../utils/video_player/VideoPlayer.js";
import "./typedefs.js";


/**@type {MediaStreamConstraints} */
let mediaStreamConstraint;

let page = new Page(document.documentElement.lang);
let device = new Device();

/**@type {Recorder|null} */
export let recorder = null;

/**@type {VideoPlayer|null} */
let videoPlayer = null;

export const IS_MOBILE = device.checkIfMobile();
export const IS_MOBILE_OR_TABLET = device.checkIfMobileOrTablet();
export const IS_TOUCH_SCREEN = device.checkIfTouchScreen();
export const SUPPORT_FULLSCREEN = device.supportFullscreen();
export const UPLOAD_MANAGER = new UploadManager(
    document.querySelector(".upload_progress_container"),
    document.querySelector(".upload_progress_container .progress_bar"),
    document.querySelector(".upload_progress_container .message_container")
);

init();

async function init() {
    page.retrieveDOMElements();
    try {
        
        //va tester si MediaRecorder & MediaStream existe. Si n'existe pas ça nous envoie dans le CATCH
        device.testForMediaRecorderMediaStreamAPI();

        //va tester les différents mimeTypes & codecs disponibles/proposés, si aucun n'a été trouvé, ça nous envoie dans le CATCH sinon ça nous renvoie un des types dispo.
        let mimeType = device.testForMimeTypesAndCodecs();
        
        
        // console.log("audio/webm:"+MediaRecorder.isTypeSupported('audio/webm;codecs=opus'));
        //askPermissions peut rater et nous envoyer dans le CATCH
        let deviceDetails = await device.askPermissions();
        
        mediaStreamConstraint = {
            audio: deviceDetails.audio.hasPermission && deviceDetails.audio.exists,
            video: deviceDetails.video.hasPermission && deviceDetails.video.exists
        }
        
        videoPlayer = new VideoPlayer(document.querySelector(".video_player"));
        
        page
            .removeUnavailableDeviceFromSelectableDevice(mediaStreamConstraint)
            .enumerateDevicesInSelect(deviceDetails.audio.deviceId, deviceDetails.video.deviceId, mediaStreamConstraint)
            .displayPossibilityToRecord();

        if (!mediaStreamConstraint.video) {
            page.displayVideoDeviceUnavailable();
        }

        let audioVisualizer = new AudioVisualizer();

        recorder = new Recorder(TRADUCTION_RECORDER, TRADUCTION_RECORDED, TRADUCTION_TIME, audioVisualizer, videoPlayer);
        recorder
            .setMimeType(mimeType)
            .setDeviceConstraint(mediaStreamConstraint, deviceDetails.audio.deviceId, deviceDetails.video.deviceId)
            .initEventListeners()
            .startStreamingToPreviewVideo()
            .then(() => {
                // recorder.openRecorder();
            });

        page.updateDeviceToMediaConstraint(recorder.updateDevice());
    } catch (status) {
        page
            .displayErrors(status, TRADUCTION_ERROR_DEVICE)
            .removePossibilityToRecord();
    }
}

function testForAsyncFetchAndArrowFunctionAndES6Class() {
    if (!("fetch" in window)) {
        return false;
    }

    try {
        eval("(async function() {})");
    } catch (e) {
        return false;
    }

    try {
        eval("(() => '')()")
    } catch (e) {
        return false;
    }

    try {
        eval('"use strict"; class foo {}');
    }
    catch (e) {
        return false;
    }

    return true;
}