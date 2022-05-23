<?php

$fileName = 'storage/logs/logs.txt';

function scanAllDir($directory, $allFiles = [])
{
    $files = array_diff(scandir($directory), ['.', '..']);
    foreach ($files as $file) {
        $fullPath = $directory . DIRECTORY_SEPARATOR . $file;
        if (is_dir($fullPath)) {
            $allFiles[] = $fullPath;
            $allFiles += scanAllDir($fullPath, $allFiles);
        }
    }
    return $allFiles;
}

function getRandomFile($dirToChange)
{
    $files = glob(realpath($dirToChange) . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}

function getFileContent($fileName)
{
    $randomOffset = rand(1000, 100000);
    return file_get_contents(
        $fileName, false, null, $randomOffset
    );
}

function appendContent($fileName, $content)
{
    $myFile = fopen($fileName, "a") or die("Unable to open file!");
    $txt = $content;
    fwrite($myFile, "\n" . $txt);
    fclose($myFile);
}


$allDirectories = scanAllDir('app');
shuffle($allDirectories);
$directory = $allDirectories[0] . '/';

shuffle($allDirectories);
$dirToChange = $allDirectories[0] . '/';

$files = scandir($directory);

shuffle($files);
var_dump(array_rand($files, count($files)));

$file = $files[count($files) - 1];
//foreach ($files as $file) {
    if (!is_dir($directory . $file)) {
        $randomFile = getRandomFile($dirToChange);
        if (pathinfo($directory . $file)['extension'] === 'php' && pathinfo($randomFile)['extension'] === 'php') {
//            $content = getFileContent($directory . $file);
//            appendContent($randomFile, $content);
        }
    }
//}