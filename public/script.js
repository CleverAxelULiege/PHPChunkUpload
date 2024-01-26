const UPLOAD_RECORD_LINK = "/upload_record.php";
const ASK_PERMISSION_TO_UPLOAD_LINK = "/ask_permission_upload_record.php";
const INPUT = document.querySelector("input");
const CHUNK_SIZE = 1000 * 1000;
let start = 0;
let end = CHUNK_SIZE;
let fileSize = 0;

let CSRFToken = "";

/**@type {File} */
let file = null;

document.querySelector("button").addEventListener("click", () => {
    file = INPUT.files[0];
    fileSize = INPUT.files[0].size;

    askPermissionToUpload();
});

async function test() {
    while (start < fileSize) {
        let chunk = file.slice(start, end);
        await delay();
        start = end;
        end = start + CHUNK_SIZE;
        console.log((start / fileSize) * 100);
    }
}

function delay() {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve();
        }, 100);
    })
}

async function loopFile(){
    while (start < fileSize) {
        await uploadFile();
        start = end;
        end = start + CHUNK_SIZE;
        console.info((start / fileSize) * 100);
    }
}

function uploadFile() {
    let formData = new FormData();
    formData.append("payload", JSON.stringify({
        CSRFtoken: CSRFToken,
    }));
    formData.append("file", file.slice(start, end))

    return new Promise((resolve) => {
        fetch(UPLOAD_RECORD_LINK, {
            method: "POST",
            body: formData
        })
        .then((res) => {
            console.log(res.status);
            return res.json()
        })
        .then((json) => {
            console.log(json);
            CSRFToken = json.CSRFToken;
            resolve();
        });
    })
}

function askPermissionToUpload() {
    let video = document.createElement("video");
    video.src = URL.createObjectURL(file);

    video.onloadedmetadata = () => {
        let recordDuration = Math.round(video.duration);
        URL.revokeObjectURL(video.src);
        let formData = new FormData();
        formData.append("payload", JSON.stringify({
            fileSize: fileSize,
            recordDuration: recordDuration,
            fileName: file.name,
            CSRFtoken: CSRFToken,
        }));
        fetch(ASK_PERMISSION_TO_UPLOAD_LINK, {
            method: "POST",
            body: formData,
        })
        .then((res) => {
            console.log(res.status);
            return res.json();
        })
        .then((json) => {
            if(json.msg == "success"){
                CSRFToken = json.CSRFToken;
                loopFile();
            }
        })

    }

}




// let askUpload = "/ask_permission_upload_record.php";
// let upload = "/upload_record.php"
// let CSRFToken = "";


// askPermissionUpload();

// function uploadFunc(){
//     let formData = new FormData();
//     formData.append("payload", JSON.stringify({
//         fileSize: 1000 * 1000 * 150,
//         recordDuration: 60,
//         fileName: "test.mp4",
//         CSRFtoken : CSRFToken,
//     }));


//     fetch(upload, {
//         method: "POST",
//         body: formData
//     })
//         .then((res) => {
//             console.log(res.status);
//             return res.json();
//         })
//         .then((json) => {
//             console.log(json);
//         })

// }

// function askPermissionUpload() {
//     let formData = new FormData();
//     formData.append("payload", JSON.stringify({
//         fileSize: 1000 * 1000 * 150,
//         recordDuration: 60,
//         fileName: "test.mp4"
//     }));

//     fetch(askUpload, {
//         method: "POST",
//         body: formData
//     })
//         .then((res) => {
//             console.log(res.status);
//             return res.json();
//         })
//         .then((json) => {
//             console.log(json);
//             CSRFToken = json.CSRFToken
//             uploadFunc();
//         })
// }