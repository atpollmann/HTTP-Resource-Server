<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class PHPConfigParser implements ifConfigParser {

  /**
   * Extract the configuration from the filepath
   * and returns it as a php array
   *
   * @param string $filePath
   * @return array
   * @throws FileNotFoundException
   */
  public function extractConfig(string $filePath) : array {
    /** @noinspection PhpIncludeInspection */
    return include($filePath);
  }
}