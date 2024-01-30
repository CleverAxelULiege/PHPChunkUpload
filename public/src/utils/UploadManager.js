const CHUNK_SIZE = 1000 * 1000;
const UPLOAD_RECORD_LINK = "/upload_record.php?lng=" + document.documentElement.lang;
const ASK_PERMISSION_TO_UPLOAD_LINK = "/ask_permission_upload_record.php?lng=" + document.documentElement.lang;
const UPLOAD_OVER_LINK = "/upload_over.php?lng=" + document.documentElement.lang;
const ATTEMPTS_BEFORE_ABORTING = 5;

const STATUS = {
    NO_FILE_SENT: 0,
    FILE_TOO_BIG: 1,
    FAILED_TO_MOVE_FILE: 2,
    DIRECTORY_DOESNT_EXIST: 3,
    INVALID_CSRF_TOKEN: 4,
    TOTAL_SIZE_EXCEEDED: 5,
    OK: 6,
}

export class UploadManager {

    /**@type {Blob|null} */
    file = null;

    start = 0;
    end = CHUNK_SIZE;

    CSRFToken = "";

    isOnline = true;

    /**@type {HTMLSpanElement|null} */
    displayProgressInPercent = null;

    /**@type {HTMLDivElement|null} */
    displayProgressBar = null

    /**@type {HTMLSpanElement|null} */
    displayUploadInProgress = null;

    /**@type {HTMLSpanElement|null} */
    displayUploadComplete = null;

    /**@type {HTMLSpanElement|null} */
    displayUploadError = null;

    /**@type {HTMLSpanElement|null} */
    displayConnectionLost = null;

    /**@type {HTMLSpanElement|null} */
    displayVideoProcessing = null;

    /**@type {HTMLDivElement|null} */
    displayUploadErrorMessages = null;

    /**
     * 
     * @param {HTMLDivElement|undefined} progressBar 
     * @param {HTMLDivElement|undefined} messageContainer 
     */
    constructor(progressBar, messageContainer) {

        /**@type {HTMLDivElement} */
        this.progressBar = progressBar

        /**@type {HTMLDivElement} */
        this.messageContainer = messageContainer;

        if (progressBar) {
            this.displayProgressInPercent = progressBar.querySelector(".progress");
            this.displayProgressBar = progressBar.querySelector(".bar");
        }

        if (messageContainer) {
            this.displayUploadComplete = messageContainer.querySelector(".complete");
            this.displayUploadInProgress = messageContainer.querySelector(".in_progress");
            this.displayUploadError = messageContainer.querySelector(".error");
            this.displayUploadErrorMessages = messageContainer.querySelector(".error_messages");
            this.displayConnectionLost = messageContainer.querySelector(".connection");
            this.displayVideoProcessing = messageContainer.querySelector(".processing");
        }

        window.addEventListener("online", () => {
            this.isOnline = true;
        });

        window.addEventListener("offline", () => {
            this.isOnline = false;
        })
    }

    beforeUnloadHandler(event) {
        event.preventDefault();
        event.returnValue = true;
    };

    /**
     * @param {Blob} file 
     */
    setFile(file) {
        this.file = file;
    }

    asyncGetRecordDuration() {
        return new Promise((resolve) => {
            let video = document.createElement("video");
            video.src = URL.createObjectURL(this.file);
            video.onloadedmetadata = () => {
                URL.revokeObjectURL(video.src);
                video.onloadedmetadata = null;
                resolve(video.duration);
            };

        })
    }

    async asyncUploadFile() {
        if (this.file == null) {
            console.error("No file given");
            return;
        }

        if (this.CSRFToken == "") {
            console.error("No CSRF token given");
            return;
        }

        let statusUpload = null
        let attempt = 0;
        while (this.start < this.file.size) {
            statusUpload = await this.asyncUploadChunk();
            await this.delay();
            if ((statusUpload == STATUS.OK || statusUpload == STATUS.FAILED_TO_MOVE_FILE) && attempt < ATTEMPTS_BEFORE_ABORTING) {
                if (statusUpload == STATUS.OK) {
                    attempt = 0;
                    this.start = this.end;
                    this.end = this.start + CHUNK_SIZE;
                    let progress = Math.ceil((this.start / this.file.size) * 100);
                    if (progress > 100) {
                        progress = 100;
                    }

                    this.displayProgressInPercent.innerHTML = `${progress}%`;
                    this.displayProgressBar.style.width = `${progress}%`;
                } else {
                    attempt++;
                }
            } else {
                break;
            }
        }

        if (statusUpload == STATUS.OK) {
            this.uploadComplete();
        }
        else if (statusUpload == STATUS.FAILED_TO_MOVE_FILE) {
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.remove("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.add("hidden");
        }
        else if (!this.isOnline) {
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.remove("hidden");
            this.displayVideoProcessing.classList.add("hidden");
        }

    }

    async delay() {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve();
            }, 1000);
        })
    }

    async uploadComplete() {
        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            CSRFtoken: this.CSRFToken,
        }));
        formData.append("file", this.file.slice(this.start, this.end))

        try {
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.remove("hidden");
            await this.delay();
            let response = await fetch(UPLOAD_OVER_LINK, {
                method: "POST",
                body: formData
            });

            let json = await response.json();
            if (response.status != 200) {
                console.log(json);
                throw new Error();
            }


            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.remove("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.add("hidden");
        } catch {
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.add("hidden");

            if (!this.isOnline) {
                this.displayConnectionLost.classList.remove("hidden");
            } else {
                this.displayUploadError.classList.remove("hidden");
            }
        }
        window.removeEventListener("beforeunload", this.beforeUnloadHandler);
    }

    /**
     * @returns {Promise<number|null>}
     */
    async asyncUploadChunk() {
        if (this.file == null) {
            console.error("No file given");
            return null;
        }

        if (this.CSRFToken == "") {
            console.error("No CSRF token given");
            return null;
        }

        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            CSRFtoken: this.CSRFToken,
        }));
        formData.append("file", this.file.slice(this.start, this.end))

        try {
            let response = await fetch(UPLOAD_RECORD_LINK, {
                method: "POST",
                body: formData
            });

            if (response.status != 200 && response.status != 503) {
                throw new Error();
            }

            let json = await response.json();
            this.CSRFToken = json.CSRFToken ?? "";

            if (json.status != STATUS.OK) {
                console.log(json);
            }

            return json.status;
        } catch {
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.add("hidden");

            if (!this.isOnline) {
                this.displayConnectionLost.classList.remove("hidden");
            } else {
                this.displayUploadError.classList.remove("hidden");
            }
            window.removeEventListener("beforeunload", this.beforeUnloadHandler);
            return null;
        }


    }

    async asyncAskPermissionToUpload() {
        if (this.file === null) {
            window.alert("No file given to upload");
            return false;
        }
        console.log("asking_");
        let videoDuration = await this.asyncGetRecordDuration();

        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            fileSize: this.file.size,
            recordDuration: videoDuration,
            fileName: this.file.name ?? Date.now().toString() + "_record.webm",
            CSRFtoken: this.CSRFToken,
        }));
        let responseStatus = 200;
        let msg = null;

        this.displayUploadErrorMessages.innerHTML = "";

        this.messageContainer.classList.remove("hidden");
        this.displayUploadComplete.classList.add("hidden");
        this.displayUploadError.classList.add("hidden");
        this.displayUploadInProgress.classList.add("hidden");
        this.displayConnectionLost.classList.add("hidden");
        this.displayVideoProcessing.classList.add("hidden");

        try {
            console.log("asking");
            let response = await fetch(ASK_PERMISSION_TO_UPLOAD_LINK, {
                method: "POST",
                body: formData
            });
            let json = await response.json();

            if (response.status == 422) {
                window.alert("Somehow, the content length of your payload was too large and you shouldn't be able to see this message.");
                return false;
            }

            responseStatus = response.status;

            if (response.ok) {
                this.CSRFToken = json.CSRFToken;
                this.progressBar.classList.remove("hidden");
                this.displayUploadInProgress.classList.remove("hidden");
                window.addEventListener("beforeunload", this.beforeUnloadHandler);
                this.asyncUploadFile();
                return true;
            }

            msg = json.msg;
            throw new Error();

        } catch (e) {
            if (responseStatus == 400) {
                this.displayMessage(msg);
            } else {
                this.displayUploadError.classList.remove("hidden");
            }
            return false;
        }
    }

    displayMessage(msg) {
        this.displayUploadErrorMessages.classList.remove("hidden");

        if (typeof msg == "string") {
            this.displayUploadErrorMessages.innerHTML = `<p>${msg}</p>`;
        } else if (Array.isArray(msg)) {
            this.displayUploadErrorMessages.innerHTML = `<ul>${msg.map((m) => `<li>${m}</li>`).join("")}</ul>`;
        } else {
            console.log(msg);
        }
    }
}