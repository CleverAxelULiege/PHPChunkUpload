<?php

$output = shell_exec("powershell -executionpolicy bypass -file .\GarbageCollector.ps1");
echo $output;