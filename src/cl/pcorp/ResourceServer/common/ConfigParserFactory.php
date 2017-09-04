<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class ConfigParserFactory extends Factory {

  /**
   * The config file extension
   *
   * @var string
   */
  private $type = "";

  /**
   * @param string $type JSON, YAML, PHP, etc.
   * @return ifConfigParser
   * @throws FileNotFoundException
   */
  public function getParser(string $type) {
    $this->type = $type;
    $this->setBasename($this->getParserBasename());
    $this->setNamespace(__NAMESPACE__);
    $this->setBasePath(__DIR__ . '/');
    return $this->getInstance();
  }

  private function getParserBasename() {
    return strtoupper($this->type) . "ConfigParser";
  }

}