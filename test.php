<?php
// $pathToScript = "D:\php_projects\upload\src\GarbageCollector.ps1";
// $output = shell_exec("powershell -executionpolicy bypass -file " . $pathToScript . " -force");
// $output = shell_exec("powershell -executionpolicy bypass -file .\GarbageCollector.ps1 -force >/dev/null 2>/dev/null &");
//https://stackoverflow.com/questions/45953/php-execute-a-background-process#45966
//https://www.php.net/manual/fr/function.popen.php
// pclose(popen("start ". "powershell -executionpolicy bypass -file .\GarbageCollector.ps1 -force", "r"));

// $pathToScript = "D:\php_projects\upload\src\GarbageCollector.ps1";
// $handle = popen("start " . "powershell -executionpolicy bypass -file " . $pathToScript, "r");

// if ($handle !== false) {
//     pclose($handle);
//     echo "good";
// } else {
//     echo "not good";
// }

echo rand(0, 2);