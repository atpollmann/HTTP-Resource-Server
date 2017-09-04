<?php
namespace cl\pcorp\ResourceServer\common;

class Utils {

  /**
   * Returns the class name without the namespace
   *
   * @param string $fqcn The fully qualified class name
   * @return bool
   */
  public static function getSimpleClassName(string $fqcn) {
    return (new \ReflectionClass($fqcn))->getShortName();
  }

  public static function getExtension(string $path) {
    return pathinfo($path, PATHINFO_EXTENSION);
  }

}