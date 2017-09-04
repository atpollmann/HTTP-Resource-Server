<?php
namespace cl\pcorp\ResourceServer\common;

interface ifLogger {

  /**
   * Severity DEBUG(100) Detailed debug information
   *
   * @param string $message
   * @param array $context
   */
  public static function debug(string $message, array $context = []);

  /**
   * Severity INFO(200) Interesting events. Examples: User logs in, SQL logs.
   *
   * @param string $message
   * @param array $context
   */
  public static function info(string $message, array $context = []);

  /**
   * Severity NOTICE(250) Normal but significant events.
   *
   * @param string $message
   * @param array $context
   */
  public static function notice(string $message, array $context = []);

  /**
   * Severity WARNING(300) Exceptional occurrences that are not errors.
   * Examples: Use of deprecated APIs, poor use of an API,
   * undesirable things that are not necessarily wrong
   *
   * @param string $message
   * @param array $context
   */
  public static function warning(string $message, array $context = []);

  /**
   * Severity ERROR(400) Runtime errors that do not require immediate
   * action but should typically be logged and monitored
   *
   * @param string $message
   * @param array $context
   */
  public static function error(string $message, array $context = []);

  /**
   * Severity CRITICAL(500) Critical conditions.
   * Example: Application component unavailable, unexpected exception.
   *
   * @param string $message
   * @param array $context
   */
  public static function critical(string $message, array $context = []);

  /**
   * Severity ALERT(550) Action must be taken immediately.
   * Example: Entire website down, database unavailable, etc.
   * This should trigger the SMS alerts and wake you up
   *
   * @param string $message
   * @param array $context
   */
  public static function alert(string $message, array $context = []);

  /**
   * Severity EMERGENCY(600) System is unusable
   *
   * @param string $message
   * @param array $context
   */
  public static function emergency(string $message, array $context = []);


}