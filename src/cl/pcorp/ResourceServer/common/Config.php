<?php
namespace cl\pcorp\ResourceServer\common;

class Config {

  private static $config;
  private static $agents;
  private static $resourceParams;
  private static $mimes;
  private static $resourceCacheControl;

  private function __construct() {}

  public static function init(string $configFile) {
    try {
      $configParserFactory = new ConfigParserFactory();
      $configParser = $configParserFactory->getParser(Utils::getExtension($configFile));
      $config = $configParser->extractConfig($configFile);

      self::$config = $config["config"];
      self::$agents = $config["agents"];
      self::$resourceParams = $config["resourceParams"];
      self::$mimes = $config["mimes"];
      self::$resourceCacheControl = $config["resourceCacheControl"];
    } catch(\Exception $e) {
      // The config class must have the least ammount of dependencies
      // since it is used by all components in the system
      // If the init sequence fails, it must behave in a
      // stand-alone way
      header("Content-type: text/plain", true, 500);
      exit("Configuration initialization sequence failed.");
    }
  }

  public static function get($config) {
    return array_key_exists($config, self::$config) ? self::$config[$config] : null;
  }

  public static function getResourceParams(string $resourceName) {
    return array_key_exists($resourceName, self::$resourceParams) ? self::$resourceParams[$resourceName] : null;
  }

  public static function getAgentName(string $extension) {
    return array_key_exists($extension, self::$agents) ? self::$agents[$extension] : null;
  }

  public static function getMimes() {
    return self::$mimes;
  }

  public static function set($config, $value) {
    self::$config[$config] = $value;
  }

  public static function getCacheControlParams($resourceClass) {
    return array_key_exists($resourceClass, self::$resourceCacheControl) ? self::$resourceCacheControl[$resourceClass] : null;
  }
}