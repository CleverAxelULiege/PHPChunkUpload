<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
<script>
    let formData = new FormData();
    formData.append("payload", JSON.stringify({
        fileSize: 1000 * 1000 * 150,
        recordDuration: 60,
        fileName : "test"
    }));
    fetch("/ask_permission_upload_record.php", {
        method: "POST",
        body:formData
    })
    .then((res) => {
        console.log(res.status);
        return res.json();
    })
    .then((json) => {
        console.log(json);
    })
</script>
</html>