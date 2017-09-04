<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class JSONConfigParser implements ifConfigParser {

  /**
   * Extract the configuration from the filepath
   * and returns it as a php array
   *
   * @param string $filePath
   * @return array
   * @throws FileNotFoundException
   */
  public function extractConfig(string $filePath) : array {
    $json = file_get_contents($filePath);
    return json_decode($json, true);
  }
}