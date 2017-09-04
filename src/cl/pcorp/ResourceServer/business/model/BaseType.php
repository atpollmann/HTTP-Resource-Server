<?php
namespace cl\pcorp\ResourceServer\business\model;

abstract class BaseType {

  public static function isValidType($type) {
    $constants = self::getConstants();
    foreach($constants as $constant) {
      if($type == $constant) {
        return true;
      }
    }
    return false;
  }

  public static function getType($typeString) {
    if(self::isValidType($typeString)) {
      return $typeString;
    }

    return null;
  }

  private static function getConstants() {
    $reflectionClass = new \ReflectionClass(get_called_class());
    return $reflectionClass->getConstants();
  }

}