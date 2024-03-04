import { AVAILABLE_MIME_TYPES, DEVICE_STATUS } from "./Device.js";
import "./typedefs.js"
export class Page {

    /**@type {IDOMElement} */
    element = {
        MAIN: null,
        SELECTABLE_DEVICES_CONTAINER_DIV: null,
        AUDIO_DEVICE_SELECT: null,
        VIDEO_DEVICE_SELECT: null,
        RECORD_FROM_SITE_DIV: null,
        ERROR_BOX_DEVICE_DIV: null,
        LOADER_CONTAINER_DIV: null,
        ROOT_DIV: null
    }

    retrieveDOMElements() {
        this.element.SELECTABLE_DEVICES_CONTAINER_DIV = document.querySelector(".devices");
        this.element.RECORD_FROM_SITE_DIV = document.querySelector("#permission_to_record_from_site");
        this.element.AUDIO_DEVICE_SELECT = document.querySelector("#audio_device_select");
        this.element.VIDEO_DEVICE_SELECT = document.querySelector("#video_device_select");
        this.element.MAIN = document.querySelector("main");
        this.element.ROOT_DIV = document.querySelector("#root");
        this.element.ERROR_BOX_DEVICE_DIV = document.querySelector(".error_box");
        this.element.LOADER_CONTAINER_DIV = document.querySelector("main>.loader_container");

        // this.element.LOADER_CONTAINER_DIV.querySelector("h2").innerText = this.traduction.loader.start;
    }

    displayVideoDeviceUnavailable() {
        document.querySelector("h3.recorder_video_device_disabled").classList.remove("hidden");
    }

    /**
     * @param {(audioDeviceId:string|null, videoDeviceId:string|null) => void} update 
     */
    updateDeviceToMediaConstraint(update) {
        this.element.AUDIO_DEVICE_SELECT.addEventListener("change", (e) => {
            update(e.target.value, null);
        });

        this.element.VIDEO_DEVICE_SELECT.addEventListener("change", (e) => {
            update(null, e.target.value);
        });
    }

    /**
     * 
     * @param {any} deviceStatus 
     * @param {ITraductionErrorDevice} traduction 
     */
    displayErrors(deviceStatus, traduction) {
        this.element.ERROR_BOX_DEVICE_DIV.classList.remove("hidden");
        switch (deviceStatus) {
            case DEVICE_STATUS.unavailableAudioDeviceVideoDevice:
                this.element.ERROR_BOX_DEVICE_DIV.innerHTML = `<p>${traduction.unavailableAudioDeviceVideoDevice} ${traduction.default}</p>`;
                break;
            case DEVICE_STATUS.unavailablePermissionForDevices:
                this.element.ERROR_BOX_DEVICE_DIV.innerHTML = `<p>${traduction.unavailablePermissionToUseDevices} ${traduction.default}</p>`;
                break;
            case DEVICE_STATUS.unavailablePermissionToUseAudioDeviceWithVideoDevice:
                this.element.ERROR_BOX_DEVICE_DIV.innerHTML = `<p>${traduction.unavailablePermissionToUseAudioDeviceWithVideoDevice} ${traduction.default}</p>`;
                break;
            case DEVICE_STATUS.unavailableMediaRecorderMediaStream:
                this.element.ERROR_BOX_DEVICE_DIV.innerHTML = `<p>${traduction.unavailableMediaRecorderMediaStream} ${traduction.default}</p>`;
                break;
            case DEVICE_STATUS.unavailableMimeType:
                this.element.ERROR_BOX_DEVICE_DIV.innerHTML =
                    `<p>${traduction.unavailableMimeType.replace(":PLACEHOLDER", "<ul style='padding-left: 20px;'>" + AVAILABLE_MIME_TYPES.map((type) => `<li>${type}</li>`).join("") + "</ul><br><br>")} ${traduction.default}</p>`;
                break;
            default:
                if (deviceStatus != DEVICE_STATUS.ok) {
                    this.element.ERROR_BOX_DEVICE_DIV.innerHTML = `<p>${traduction.unknownError ?? "Failed to fetch the correct traduction"}</p>`;
                }
                break;
        }

        return this;
    }

    removePossibilityToRecord() {
        this.element.ROOT_DIV.removeChild(this.element.RECORD_FROM_SITE_DIV);
        this.element.ROOT_DIV.removeChild(document.querySelector(".recorder_container"));
        this.element.ROOT_DIV.removeChild(document.querySelector(".recorded_element_container"));
    }

    displayPossibilityToRecord() {
        this.element.ERROR_BOX_DEVICE_DIV.classList.add("hidden");
        this.element.RECORD_FROM_SITE_DIV.removeChild(this.element.RECORD_FROM_SITE_DIV.querySelector(".loader_container"));
        // this.element.RECORD_FROM_SITE_DIV?.classList.remove("hidden");
    }

    /**
     * 
     * @param {string|undefined} audioDeviceId 
     * @param {string|undefined} videoDeviceId 
     * @param {mediaStreamConstraint} mediaStreamConstraint 
     */
    enumerateDevicesInSelect(audioDeviceId, videoDeviceId, mediaStreamConstraint) {
        navigator.mediaDevices.enumerateDevices()
            .then((devices) => {
                devices.forEach((device) => {
                    switch (device.kind) {
                        case "videoinput":
                            if (mediaStreamConstraint.video) {
                                this.element.VIDEO_DEVICE_SELECT.appendChild(this.createOptionDevice(device, device.deviceId == videoDeviceId));
                            }
                            break;
                        case "audioinput":
                            if (mediaStreamConstraint.audio) {
                                this.element.AUDIO_DEVICE_SELECT.appendChild(this.createOptionDevice(device, device.deviceId == audioDeviceId));
                            }
                            break;
                    }
                });
            });

        return this;
    }

    /**
     * @private
     * @param {MediaDeviceInfo} mediaDeviceInfo 
     * @param {boolean} selected 
     * @returns 
     */
    createOptionDevice(mediaDeviceInfo, selected) {
        let option = document.createElement("option");
        option.textContent = mediaDeviceInfo.label;
        option.value = mediaDeviceInfo.deviceId;
        option.selected = selected;
        return option;
    }

    /**
     * @param {MediaStreamConstraints} deviceConstraint 
     * @returns 
     */
    removeUnavailableDeviceFromSelectableDevice(deviceConstraint) {
        if (!deviceConstraint.audio) {
            this.element.SELECTABLE_DEVICES_CONTAINER_DIV.removeChild(document.querySelector(".device_container.audio_device"));
        }

        if (!deviceConstraint.video) {
            this.element.SELECTABLE_DEVICES_CONTAINER_DIV.removeChild(document.querySelector(".device_container.video_device"));
        }

        return this;
    }

    /**
     * @param {MediaStreamConstraints} deviceConstraint 
     */
    removeVideoDeviceFromSelectableDevice(deviceConstraint) {
        if (deviceConstraint.video) {
            this.element.SELECTABLE_DEVICES_CONTAINER_DIV.removeChild(document.querySelector(".device_container.video_device"));
        }
    }
}