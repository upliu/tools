<?php

function upliu()
{
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$file = $trace[0]['file'];
	$line = $trace[0]['line'];

	$args = func_get_args();
	ob_start();
	echo str_repeat('=', 80), "\n";
	echo "\t", date('H:i:s'), '.', substr(microtime(true), 11, 3), "\t", $file, ':', $line, "\n";
	echo str_repeat('=', 80), "\n";
	print_r($args);
	echo "\n\n";
	$buff = ob_get_clean();
	file_put_contents('/Users/liu/Library/Logs/UPLIU/debug.log', $buff, FILE_APPEND);
}
