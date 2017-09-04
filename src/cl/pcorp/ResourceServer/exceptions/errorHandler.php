<?php
namespace cl\pcorp\ResourceServer\exceptions;

use cl\pcorp\ResourceServer\common\Logger;

/**
 * Sets all error reporting to exceptions
 *
 * @param $severity
 * @param $message
 * @param $file
 * @param $line
 * @throws \ErrorException
 */
function errorToException($severity, $message, $file, $line) {
  if(!(error_reporting() & $severity)) {
    return;
  }
  throw new \ErrorException($message, 0, $severity, $file, $line);
}

/**
 * The uncaught exceptions sink
 * @param \Exception $exception
 */
function uncaughtExceptionHandler($exception) {
  $message = "Exception (uncaught): ".
    $_SERVER["REQUEST_URI"].
    " ".
    $exception->getMessage().
    " ".
    $exception->getFile().
    " ".
    $exception->getLine();
  Logger::critical($message);
}

set_error_handler("cl\pcorp\\ResourceServer\\exceptions\\errorToException");
set_exception_handler("cl\pcorp\\ResourceServer\\exceptions\\uncaughtExceptionHandler");
