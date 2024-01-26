import { UploadManager } from "./UploadManager.js";

const INPUT_FILE = document.querySelector("input");
let uploadManager = new UploadManager();

window.addEventListener("DOMContentLoaded", () => {
    INPUT_FILE.value = "";
});

INPUT_FILE.addEventListener("change", () => {
    uploadManager.setFile(INPUT_FILE.files[0]);
});
