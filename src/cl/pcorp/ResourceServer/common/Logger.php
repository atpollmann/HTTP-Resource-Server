<?php
namespace cl\pcorp\ResourceServer\common;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monologger;

class Logger implements ifLogger {

  // All ERROR(400) and upper error levels
  // are always logged. One can choose to
  // log lower levels in the configuration
  const LOG_LEVEL_DEBUG = 1;
  const LOG_LEVEL_INFO = 2;
  const LOG_LEVEL_NOTICE = 4;
  const LOG_LEVEL_WARNING = 8;

  /**
   * @var Monologger
   */
  private static $monologger = null;

  /**
   * Default log level is debug
   *
   * @var int
   */
  private static $logLevel = 1;

  private function __construct() {}

  public static function init() {
    if(self::$monologger === null) {
      $logLocation = __DIR__ . "/../../../../var/";
      self::$logLevel = Config::get("LOG_LEVEL");
      self::$monologger = new Monologger('log');

      // Write to file
      $streamHandler = new StreamHandler($logLocation . "resource_server.log");
      $streamHandler->setFormatter(new LineFormatter(null, null, true, true));
      self::$monologger->pushHandler($streamHandler);

      // Send email
//      self::$monologger->pushHandler(new NativeMailerHandler(
//        "x@company.com",
//        "error",
//        "x@company.com"
//      ));
    }
  }

  /**
   * Severity DEBUG(100) Detailed debug information
   *
   * @param string $message
   * @param array $context
   */
  public static function debug(string $message, array $context = []) {
    if(self::$logLevel & self::LOG_LEVEL_DEBUG) {
      self::$monologger->debug($message, $context);
    }
  }

  /**
   * Severity INFO(200) Interesting events. Examples: User logs in, SQL logs.
   *
   * @param string $message
   * @param array $context
   */
  public static function info(string $message, array $context = []) {
    if(self::$logLevel & self::LOG_LEVEL_INFO) {
      self::$monologger->info($message, $context);
    }
  }

  /**
   * Severity NOTICE(250) Normal but significant events.
   *
   * @param string $message
   * @param array $context
   */
  public static function notice(string $message, array $context = []) {
    if(self::$logLevel & self::LOG_LEVEL_NOTICE) {
      self::$monologger->notice($message, $context);
    }
  }

  /**
   * Severity WARNING(300) Exceptional occurrences that are not errors.
   * Examples: Use of deprecated APIs, poor use of an API,
   * undesirable things that are not necessarily wrong
   *
   * @param string $message
   * @param array $context
   */
  public static function warning(string $message, array $context = []) {
    if(self::$logLevel & self::LOG_LEVEL_WARNING) {
      self::$monologger->warning($message, $context);
    }
  }

  /**
   * Severity ERROR(400) Runtime errors that do not require immediate
   * action but should typically be logged and monitored
   *
   * @param string $message
   * @param array $context
   */
  public static function error(string $message, array $context = []) {
    self::$monologger->error($message, $context);
  }

  /**
   * Severity CRITICAL(500) Critical conditions.
   * Example: Application component unavailable, unexpected exception.
   *
   * @param string $message
   * @param array $context
   */
  public static function critical(string $message, array $context = []) {
    self::$monologger->critical($message, $context);
  }

  /**
   * Severity ALERT(550) Action must be taken immediately.
   * Example: Entire website down, database unavailable, etc.
   * This should trigger the SMS alerts and wake you up
   *
   * @param string $message
   * @param array $context
   */
  public static function alert(string $message, array $context = []) {
    self::$monologger->alert($message, $context);
  }

  /**
   * Severity EMERGENCY(600) System is unusable
   *
   * @param string $message
   * @param array $context
   */
  public static function emergency(string $message, array $context = []) {
    self::$monologger->emergency($message, $context);
  }
}