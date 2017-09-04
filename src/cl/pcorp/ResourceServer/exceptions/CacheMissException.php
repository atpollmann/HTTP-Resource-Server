<?php
namespace cl\pcorp\ResourceServer\exceptions;

use cl\pcorp\ResourceServer\common\Logger;

class CacheMissException extends \Exception {

  public function __construct($message) {
    parent::__construct($message);
    Logger::notice(__CLASS__ . " " . $message);
  }

}