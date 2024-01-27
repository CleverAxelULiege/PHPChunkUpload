const CHUNK_PART = 1000 * 1000;
const UPLOAD_RECORD_LINK = "/upload_record.php";
const ASK_PERMISSION_TO_UPLOAD_LINK = "/ask_permission_upload_record.php";

export class UploadManager {

    /**@type {Blob|null} */
    file = null;

    recordDuration = null;

    start = 0;
    end = CHUNK_PART;

    CSRFToken = "";

    abortEventListener = new AbortController();

    /**
     * @param {Blob} file 
     */
    setFile(file){
        this.file = file;
        this.setRecordDuration();
    }

    setRecordDuration() {
        if(this.recordDuration == null){
            this.abortEventListener.abort();
        }

        this.abortEventListener = new AbortController();
        this.recordDuration = null;

        let video = document.createElement("video");
        video.src = URL.createObjectURL(this.file);

        video.addEventListener("loadedmetadata", () => {
            this.recordDuration = video.duration;
            console.log(this.recordDuration);
            URL.revokeObjectURL(video.src);
        }, {once: true, signal: this.abortEventListener.signal});
    }

    async askPermissionToUpload() {
        if (this.recordDuration == null) {
            window.alert("File duration not yet retrieved");
            return;
        }

        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            fileSize: this.file.size,
            recordDuration: this.recordDuration,
            fileName: this.file.name,
            CSRFtoken: CSRFToken,
        }));

        let response = await fetch(ASK_PERMISSION_TO_UPLOAD_LINK, {
            method: "POST",
            body: formData
        });

        
    }
}