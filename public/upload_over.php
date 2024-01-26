<?php

use Upload\FileManager;
require(__DIR__ . "/../vendor/autoload.php");
$fileParts = glob(FileManager::PATH_TO_UPLOAD_TEMP . "/" . "5e45ba784f11991a2a8792b357bb0d6c/" . "*.bin");

sort($fileParts, SORT_NATURAL);

$finalFile = fopen(__DIR__ . "/test.mp4", "w");
foreach($fileParts as $part){
    $chunk = file_get_contents($part);
    fwrite($finalFile, $chunk);
}
fclose($finalFile);