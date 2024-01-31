import { AudioVisualizer } from "./utils/AudioVisualizer.js";
import { DEVICE_STATUS, Device } from "./utils/Device.js";
import { Page } from "./utils/Page.js";
import { Recorder } from "./utils/Recorder.js";
import { UploadManager } from "./utils/UploadManager.js";
import "./utils/typedefs.js"
import { VideoPlayer } from "./utils/video_player/VideoPlayer.js";

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

        if (!testForAPI()) {
            throw DEVICE_STATUS.unavailableMediaRecorderMediaStream;
        }

        videoPlayer = new VideoPlayer(document.querySelector(".video_player"));
        
        // console.log("audio/webm:"+MediaRecorder.isTypeSupported('audio/webm;codecs=opus'));
        //askPermissions peut rater et nous envoyer dans le CATCH
        let deviceDetails = await device.askPermissions();

        mediaStreamConstraint = {
            audio: deviceDetails.audio.hasPermission && deviceDetails.audio.exists,
            video: deviceDetails.video.hasPermission && deviceDetails.video.exists
        }

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

function testForAPI() {
    if (!("MediaRecorder" in window)) {
        return false;
    }
    if (!("MediaStream" in window)) {
        return false;
    }

    return true;
}