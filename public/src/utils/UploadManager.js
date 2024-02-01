const CHUNK_SIZE = 1000 * 1000;
const UPLOAD_RECORD_LINK = "/upload_record.php?lng=" + document.documentElement.lang;
const ASK_PERMISSION_TO_UPLOAD_LINK = "/ask_permission_upload_record.php?lng=" + document.documentElement.lang;
const UPLOAD_OVER_LINK = "/upload_over.php?lng=" + document.documentElement.lang;
const CONTINUE_UPLOAD_LINK = "/continue_upload.php?lng=" +  document.documentElement.lang;
const ATTEMPTS_BEFORE_ABORTING = 5;

const KEY_LOCALSTORAGE = "upload_in_progress";

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

    /**@type {number|null} */
    videoDuration = null;

    start = 0;
    end = CHUNK_SIZE;

    CSRFToken = "";

    isOnline = true;

    defaultFileName = Date.now().toString() + "_record.webm";

    waitingForResponse = false;

    isUploadComplete = false;

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

    /**@type {HTMLButtonElement} */
    closeProgressButton = null;

    /**
     * 
     * @param {HTMLDivElement|undefined} container 
     * @param {HTMLDivElement|undefined} progressBar 
     * @param {HTMLDivElement|undefined} messageContainer 
     */
    constructor(container, progressBar, messageContainer) {

        /**@type {HTMLDivElement} */
        this.container = container;

        /**@type {HTMLDivElement} */
        this.progressBar = progressBar

        /**@type {HTMLDivElement} */
        this.messageContainer = messageContainer;

        this.displayProgressInPercent = progressBar.querySelector(".progress");
        this.displayProgressBar = progressBar.querySelector(".bar");

        this.displayUploadComplete = messageContainer.querySelector(".complete");
        this.displayUploadInProgress = messageContainer.querySelector(".in_progress");
        this.displayUploadError = messageContainer.querySelector(".error");
        this.displayConnectionLost = messageContainer.querySelector(".no_connection");
        this.displayVideoProcessing = messageContainer.querySelector(".processing");

        this.closeProgressButton = container.querySelector(".close_progress_bar_container");

        this.closeProgressButton.addEventListener("click", () => {
            container.classList.add("hidden");
        });

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

    /** @param {string} fileName  */
    setDefaultFileName(fileName){
        this.defaultFileName = fileName;
    }

    /**
     * @param {HTMLDivElement} messageContainer 
     */
    setUploadErrorMessages(messageContainer) {
        this.displayUploadErrorMessages = messageContainer;
    }

    asyncGetRecordDuration() {
        return new Promise((resolve) => {
            let video = document.createElement("video");
            video.src = URL.createObjectURL(this.file);
            video.addEventListener("loadedmetadata", () => {
                if (video.duration === Infinity) {
                    video.currentTime = 1e101;
                    video.addEventListener("timeupdate", () => {
                        video.currentTime = 0;
                        resolve(video.duration);
                    }, { once: true });
                } else {
                    resolve(video.duration);
                }
            }, { once: true });
        });
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

                    this.progressBar.setAttribute("aria-valuenow", `${progress}%`);

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
        else if (!this.isOnline) {
            this.closeProgressButton.classList.remove("hidden");
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.remove("hidden");
            this.displayVideoProcessing.classList.add("hidden");
        }
        else if (statusUpload == STATUS.FAILED_TO_MOVE_FILE) {
            this.closeProgressButton.classList.remove("hidden");
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.add("hidden");
            this.displayUploadError.classList.remove("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.add("hidden");
        }

    }

    async delay() {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve();
            }, 2000);
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

            this.closeProgressButton.classList.remove("hidden");
            this.messageContainer.classList.remove("hidden");
            this.displayUploadComplete.classList.remove("hidden");
            this.displayUploadError.classList.add("hidden");
            this.displayUploadInProgress.classList.add("hidden");
            this.displayConnectionLost.classList.add("hidden");
            this.displayVideoProcessing.classList.add("hidden");
            this.isUploadComplete = true;
        } catch {
            this.closeProgressButton.classList.remove("hidden");
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
        this.waitingForResponse = false;
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
            this.closeProgressButton.classList.remove("hidden");
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

    async asyncContinueUpload(){
        let unCompleteUpload = JSON.parse(localStorage.getItem(KEY_LOCALSTORAGE));

        if(unCompleteUpload === null){
            return false;
        }
        
        if(unCompleteUpload.fileName != this.file.name && unCompleteUpload.fileDuration != this.videoDuration && unCompleteUpload.fileSize != this.file.size){
            return false;
        }

        let formData = new FormData();
        formData.append("payload",JSON.stringify({empty: true}));
        formData.append("file", this.file.slice(this.start, this.end));

        try {
            let response = await fetch(CONTINUE_UPLOAD_LINK, {
                method: "POST",
                body: formData
            });

            if (response.status != 200 && response.status != 400) {
                throw new Error();
            }

            if(response.status == 400){
                return false;
            }

            let json = await response.json();
            console.log(json);
            this.start = json.nextChunk;
            this.end = this.start + CHUNK_SIZE;

            this.CSRFToken = json.CSRFToken ?? "";
            return true;
        } catch {
            this.closeProgressButton.classList.remove("hidden");
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
            return false;
        }
    }

    async asyncAskPermissionToUpload() {
        if (this.file === null) {
            window.alert("No file given to upload");
            return false;
        }

        this.videoDuration = await this.asyncGetRecordDuration();
        let canContinueUpload = await this.asyncContinueUpload();
        
        if(canContinueUpload){
            this.progressBar.classList.remove("hidden");
            this.displayUploadInProgress.classList.remove("hidden");
            this.container.classList.remove("hidden");

            window.addEventListener("beforeunload", this.beforeUnloadHandler);
            localStorage.setItem(KEY_LOCALSTORAGE, JSON.stringify({fileName : this.file.name ?? this.defaultFileName, fileDuration: this.videoDuration, fileSize: this.file.size}));
            this.asyncUploadFile();
            return true;
        }

        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            fileSize: this.file.size,
            recordDuration: this.videoDuration,
            fileName: this.file.name ?? this.defaultFileName,
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
            this.waitingForResponse = true;
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
                this.container.classList.remove("hidden");

                window.addEventListener("beforeunload", this.beforeUnloadHandler);
                localStorage.setItem(KEY_LOCALSTORAGE, JSON.stringify({fileName : this.file.name ?? this.defaultFileName, fileDuration: this.videoDuration, fileSize: this.file.size}));
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
            this.waitingForResponse = false;
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