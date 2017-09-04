<?php
namespace cl\pcorp\ResourceServer\exceptions;

use cl\pcorp\ResourceServer\common\Logger;

class AgentErrorException extends \Exception {

  public function __construct($message) {
    parent::__construct($message);
    Logger::critical(__CLASS__ . " " . $message);
  }
}