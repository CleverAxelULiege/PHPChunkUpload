if (!testForAsyncFetchAndArrowFunctionAndES6Class()) {
    document.body.innerHTML = `
    <div>
        <h1 style="padding:10px;">Your browser does not support some keys elements for it to function properly on this site. Try updating or changing your browser.</h1>
        <h1 style="padding:10px;">Votre navigateur ne supporte pas des éléments clefs pour fonctionner correctement sur ce site. Essayez de mettre à jour ou de changer de navigateur.</h1>
    </div>`;
    throw "unavailable async/await & fetch API or arrow function or ES6 class.";
}

document.querySelector("main").style.display = "";
import { UploadManager } from "./UploadManager.js";
import "./typedefs.js";

export const UPLOAD_MANAGER = new UploadManager(
    document.querySelector(".upload_progress_container"),
    document.querySelector(".upload_progress_container .progress_bar"),
    document.querySelector(".upload_progress_container .message_container")
);

/** @type {HTMLInputElement} */
const INPUT_FILE_PRERECORDED_MSG = document.querySelector("#prerecorded_file");
/**@type {HTMLButtonElement} */
const BUTTON_UPLOAD_PRERECORDED_MSG = document.querySelector(".upload_prerecorded_video_button");

window.addEventListener("DOMContentLoaded", () => {
    INPUT_FILE_PRERECORDED_MSG.value = "";
});

INPUT_FILE_PRERECORDED_MSG.addEventListener("change", () => {
    BUTTON_UPLOAD_PRERECORDED_MSG.disabled = false;
    document.querySelector(".file_name").textContent = INPUT_FILE_PRERECORDED_MSG.files[0].name
});

BUTTON_UPLOAD_PRERECORDED_MSG.addEventListener("click", () => {
    if (UPLOAD_MANAGER.isUploadComplete || UPLOAD_MANAGER.waitingForResponse || INPUT_FILE_PRERECORDED_MSG.value == "") {
        return;
    }

    UPLOAD_MANAGER.setFile(INPUT_FILE_PRERECORDED_MSG.files[0]);
    UPLOAD_MANAGER.setUploadErrorMessages(document.querySelector(".prerecorded_message_upload_error"));

    BUTTON_UPLOAD_PRERECORDED_MSG.disabled = true;
    BUTTON_UPLOAD_PRERECORDED_MSG.classList.add("waiting_for_response");
    UPLOAD_MANAGER.asyncAskPermissionToUpload().then((hasStarted) => {
        if (hasStarted) {
            BUTTON_UPLOAD_PRERECORDED_MSG.parentElement.removeChild(BUTTON_UPLOAD_PRERECORDED_MSG);
        } else {
            BUTTON_UPLOAD_PRERECORDED_MSG.disabled = false;
            BUTTON_UPLOAD_PRERECORDED_MSG.classList.remove("waiting_for_response");
        }
    });
});

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