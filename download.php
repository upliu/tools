#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: vagrant
 * Date: 16-3-15
 * Time: 上午11:13
 */

$conf = $argv[1];
if (!is_file($conf)) {
    $urls = [$conf];
} else {
    $urls = array_filter(array_map('trim', file($conf)));
}

$outputDir = $argv[2] ?: '.';

foreach ($urls as $url) {
    download($url);
}

echo "\nDone\n";

function download($url)
{
    global $outputDir;
    $file = preg_replace('#http(s)?://#', '', $url);
    $filename = $outputDir . '/' . $file;
    $dir = dirname($filename);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    system("curl $url >> $filename");
//    print("curl $url >> $filename\n\n");
}