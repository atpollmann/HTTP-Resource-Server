<?php
namespace cl\pcorp\ResourceServer\exceptions;

use cl\pcorp\ResourceServer\common\Logger;

class FileNotFoundException extends \Exception{

  public function __construct($message) {
    parent::__construct($message);
    Logger::error(__CLASS__ . " " . $message . " in file: " . $this->getFile() . " in line: " . $this->getLine());
  }
}