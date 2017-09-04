<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

class YMLConfigParser implements ifConfigParser {

  /**
   * Extract the configuration from the filepath
   * and returns it as a php array
   *
   * @param string $filePath
   * @return array
   * @throws FileNotFoundException
   */
  public function extractConfig(string $filePath) : array {
    return Yaml::parse(file_get_contents($filePath));
  }
}