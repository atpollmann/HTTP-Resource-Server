<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\ConfigParserFactory;
use cl\pcorp\ResourceServer\common\Logger;
use cl\pcorp\ResourceServer\common\YMLConfigParser;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class ConfigTest extends \PHPUnit_Framework_TestCase {

  private static $configParserFactory = null;

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    Config::init(__DIR__ . "/config/config.json");
    Logger::init();
  }

  public function testConfigParserFactoryThrowsFileNotFoundException() {
    $this->expectException(FileNotFoundException::class);
    self::getParserFactory()->getParser("NONEXISTINGTYPE");
  }

  public function testConfigParserFactoryGetsYAMLParser() {
    $this->assertThat(
      self::getParserFactory()->getParser("yml"),
      $this->isInstanceOf(YMLConfigParser::class));
  }

  public function testConfigReadsConfigurationFromYAML() {
    $parser = self::getParserFactory()->getParser("yml");
    $config = $parser->extractConfig(__DIR__ . "/config/config.yml");
    $this->checkConfig($config);
  }

  public function testConfigReadsConfigurationFromJSON() {
    $parser = self::getParserFactory()->getParser("json");
    $config = $parser->extractConfig(__DIR__ . "/config/config.json");
    $this->checkConfig($config);
  }

  public function testConfigReadsConfigurationFromPHP() {
    $parser = self::getParserFactory()->getParser("php");
    $config = $parser->extractConfig(__DIR__ . "/config/config.php");
    $this->checkConfig($config);
  }

  public function testConfigurationsFromDifferentSourcesAreEquals() {
    $yamlParser = self::getParserFactory()->getParser("yml");
    $yamlConfig = $yamlParser->extractConfig(__DIR__ . "/config/config.yml");

    $phpParser = self::getParserFactory()->getParser("php");
    $phpConfig = $phpParser->extractConfig(__DIR__ . "/config/config.php");

    $jsonParser = self::getParserFactory()->getParser("json");
    $jsonConfig = $jsonParser->extractConfig(__DIR__ . "/config/config.json");

    $this->assertEquals($yamlConfig, $phpConfig);
    $this->assertEquals($phpConfig, $jsonConfig);
  }

  private static function getParserFactory() {
    if(self::$configParserFactory == null) {
      self::$configParserFactory = new ConfigParserFactory();
    }

    return self::$configParserFactory;
  }

  private function checkConfig($config) {
    // Check the core elements
    $this->assertArrayHasKey("config", $config);
    $this->assertArrayHasKey("resourceParams", $config);
    $this->assertArrayHasKey("resourceCacheControl", $config);

  }

}
