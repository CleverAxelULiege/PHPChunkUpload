const CHUNK_SIZE = 1000 * 1000;
const UPLOAD_RECORD_LINK = "/upload_record.php";
const ASK_PERMISSION_TO_UPLOAD_LINK = "/ask_permission_upload_record.php";
const UPLOAD_OVER_LINK = "/upload_over.php";
const ATTEMPTS_BEFORE_ABORTING = 5;

const STATUS = {
    NO_FILE_SENT: 0,
    FILE_TOO_BIG: 1,
    FAILED_TO_MOVE_FILE: 2,
    DIRECTORY_DOESNT_EXIST: 3,
    INVALID_CSRF_TOKEN: 4,
    TOTAL_SIZE_EXCEEDED: 5,
    OK: 6,
    CONTENT_LENGTH_TOO_LONG: 7,
    FAILED_TO_CONNECT: 8,
}

export class UploadManager {

    /**@type {Blob|null} */
    file = null;

    start = 0;
    end = CHUNK_SIZE;

    CSRFToken = "";


    constructor(progressBar, message){
        /**@type {HTMLDivElement} */
        this.progressBar = progressBar
        /**@type {HTMLDivElement} */
        this.message = message;
        
        this.message.classList.remove("hidden");
        this.progressBar.classList.remove("hidden");
    }

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

            if((statusUpload == STATUS.OK || statusUpload == STATUS.FAILED_TO_MOVE_FILE) && attempt < ATTEMPTS_BEFORE_ABORTING){
                if(statusUpload == STATUS.OK){
                    attempt = 0;
                    this.start = this.end;
                    this.end = this.start + CHUNK_SIZE;
                    let progress = Math.floor((this.start / this.file.size) * 100);
                    if(progress > 100){
                        progress = 100;
                    }

                    this.progressBar.querySelector(".progress").innerHTML = `${progress}%`;
                    this.progressBar.querySelector(".bar").style.width = `${progress}%`;
                    // console.info((this.start / this.file.size) * 100);
                } else {
                    attempt++;
                }
            } else {
                break;
            }
        }

        if(statusUpload == STATUS.OK){
            this.uploadComplete();
        }

    }

    async uploadComplete(){
        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            CSRFtoken: this.CSRFToken,
        }));
        formData.append("file", this.file.slice(this.start, this.end))

        try {
            let response = await fetch(UPLOAD_OVER_LINK, {
                method: "POST",
                body: formData
            });

            if (response.status == 422) {
                return STATUS.CONTENT_LENGTH_TOO_LONG;
            }

            let json = await response.json();

            this.message.querySelector(".in_progress").classList.add("hidden");
            this.message.querySelector(".complete").classList.remove("hidden");

            if (json.status != STATUS.OK) {
                console.log(json);
            }

            return json.status;
        } catch {
            window.alert("Failed to connect to the server. Please contact the responsible person");
            return STATUS.FAILED_TO_CONNECT;
        }
    }

    /**
     * @returns {Promise<number>}
     */
    async asyncUploadChunk() {
        if (this.file == null) {
            console.error("No file given");
            return;
        }

        if (this.CSRFToken == "") {
            console.error("No CSRF token given");
            return;
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

            if (response.status == 422) {
                return STATUS.CONTENT_LENGTH_TOO_LONG;
            }

            let json = await response.json();
            this.CSRFToken = json.CSRFToken ?? "";

            if (json.status != STATUS.OK) {
                console.log(json);
            }

            return json.status;
        } catch {
            window.alert("Failed to connect to the server. Please contact the responsible person");
            return STATUS.FAILED_TO_CONNECT;
        }


    }

    async asyncAskPermissionToUpload() {
        if (this.file === null) {
            window.alert("No file given to upload");
            return;
        }
        let videoDuration = await this.asyncGetRecordDuration();

        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            fileSize: this.file.size,
            recordDuration: videoDuration,
            fileName: this.file.name ?? Date.now().toString() + "_record.webm",   
            CSRFtoken: this.CSRFToken,
        }));

        try {
            let response = await fetch(ASK_PERMISSION_TO_UPLOAD_LINK, {
                method: "POST",
                body: formData
            });

            let json = await response.json();

            if (response.status == 422) {
                window.alert("Somehow, the content length of your payload was too large and you shouldn't be able to see this message.");
                return;
            }

            if (response.ok) {
                this.CSRFToken = json.CSRFToken;
                this.asyncUploadFile();
            } else {
                //display error messages;
                console.log(json);
            }

        } catch(e) {
            console.log(e);
        }


    }
}