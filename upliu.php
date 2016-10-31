<?php

$GLOBALS['UPLIU_LOGS'] = [];

register_shutdown_function(function(){
    foreach ($GLOBALS['UPLIU_LOGS'] as $log) {
        upliu_store_log($log);
    }
});

function upliu()
{
    static $only;
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $file = $trace[0]['file'];
    $line = $trace[0]['line'];

    $caller = "$file:$line";

    $args = func_get_args();

    if ($only === null && in_array('only', $args, true)) {
        $only = $caller;
        $GLOBALS['UPLIU_LOGS'] = [];
    } else if ($only !== null && $caller !== $only) {
        return;
    }

    $created_at = time();
    $time_float = date('H:i:s') . '.' . substr(microtime(true), 11, 3);

    ob_start();
    echo str_repeat('=', 80), "\n";
    echo date('H:i:s'), '.', substr(microtime(true), 11, 3), "\t", $file, ':', $line, "\n";
    echo str_repeat('=', 80), "\n";
    foreach ($args as $arg) {
        if ($arg === 'trace') {
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        } else if ($arg === 'shutdown') {
            $shutdown = true;
        } else {
            print_r($arg);
        }
        echo "\n";
    }
    $log = ob_get_clean();
    $log = compact('caller', 'created_at', 'time_float', 'log');
    if (isset($shutdown)) {
        upliu_store_log($log);
    } else {
        $GLOBALS['UPLIU_LOGS'][] = $log;
    }
}

function upliu_level($error_number)
{
    $error_description = array( );
    $error_codes = array(
        E_ERROR              => "E_ERROR",
        E_WARNING            => "E_WARNING",
        E_PARSE              => "E_PARSE",
        E_NOTICE             => "E_NOTICE",
        E_CORE_ERROR         => "E_CORE_ERROR",
        E_CORE_WARNING       => "E_CORE_WARNING",
        E_COMPILE_ERROR      => "E_COMPILE_ERROR",
        E_COMPILE_WARNING    => "E_COMPILE_WARNING",
        E_USER_ERROR         => "E_USER_ERROR",
        E_USER_WARNING       => "E_USER_WARNING",
        E_USER_NOTICE        => "E_USER_NOTICE",
        E_STRICT             => "E_STRICT",
        E_RECOVERABLE_ERROR  => "E_RECOVERABLE_ERROR",
        E_DEPRECATED         => "E_DEPRECATED",
        E_USER_DEPRECATED    => "E_USER_DEPRECATED",
        E_ALL                => "E_ALL"
    );
    foreach( $error_codes as $number => $description )
    {
        if ( ( $number & $error_number ) == $number )
        {
            $error_description[ ] = $description;
        }
    }
    return
        implode( " | ", $error_description );
}

function upliu_store_log($log)
{
	static $stmt;
	if (!$stmt) {
		$UPLIU_pdo = new PDO('mysql:dbname=upliu', 'liu');
		$UPLIU_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $UPLIU_stmt = $UPLIU_pdo->prepare('insert into upliu set caller=:c,created_at=:ca,time_float=:tf,log=:l');
	}

    $stmt->execute([
        ':c' => $log['caller'],
        ':ca' => $log['created_at'],
        ':tf' => $log['time_float'],
        ':l' => $log['log'],
    ]);
    if (php_sapi_name() === 'cli') {
        echo $log['log'];
    }
}
