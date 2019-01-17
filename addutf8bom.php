#!/usr/local/opt/php@7.1/bin/php
<?php
$file = $argv[1];
if (!file_exists($file)) {
    echo "$file not exists\n";
    return 1;
}
$path = realpath($file);
chdir(dirname($path));
$bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) );
$i = 0;
while (file_exists($bom_file = pathinfo($file, PATHINFO_FILENAME) . ".bom" . ($i ? ".{$i}" : '') . '.' . pathinfo($file, PATHINFO_EXTENSION))) {
	$i++;
};
file_put_contents($bom_file, $bom);
file_put_contents($bom_file, file_get_contents($file), FILE_APPEND);
unlink($path);
