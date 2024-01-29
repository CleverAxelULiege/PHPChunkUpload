<?php

use Upload\FileManager;
require(__DIR__ . "/../vendor/autoload.php");
$fileParts = glob(FileManager::PATH_TO_UPLOAD_TEMP . "/" . "3a1704ad673e01cf5831f29dd7b7bda7/" . "*.bin");

sort($fileParts, SORT_NATURAL);

$finalFile = fopen(__DIR__ . "/test.mp4", "w");
foreach($fileParts as $part){
    $chunk = file_get_contents($part);
    fwrite($finalFile, $chunk);
}
fclose($finalFile);