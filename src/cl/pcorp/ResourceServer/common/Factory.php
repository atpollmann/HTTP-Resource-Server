<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class Factory {

  private $namespace;

  /**
   * @var string The name of the file, excluding extension
   */
  private $basename;

  private $classname;

  /**
   * @var string The location of the fabricated class
   */
  private $basePath;

  /**
   * @return string
   */
  public function getBasename() {
    return $this->basename;
  }

  /**
   * @param string $basename
   */
  public function setBasename($basename) {
    $this->basename = $basename;
  }

  public function setNamespace(string $namespace) {
    $this->namespace = $namespace;
  }

  /**
   * @return string
   */
  public function getBasePath() {
    return $this->basePath;
  }

  /**
   * @param string $basePath
   */
  public function setBasePath($basePath) {
    $this->basePath = $basePath;
  }

  protected function getInstance() {
    $this->classname = $this->namespace . '\\' . $this->getBasename();

    if(!$this->fileExists()) {
      throw new FileNotFoundException('File \'' . $this->getBasePath() . $this->getBasename() . '\' not found or it\'s not readable');
    }
    if(!$this->isInstantiable($this->classname)) {
      throw new FileNotFoundException('The class is not instatiable');
    }

    return new $this->classname;
  }

  protected function isInstantiable(string $classname) {
    $reflection = new \ReflectionClass($classname);
    return $reflection->isInstantiable();
  }

  /**
   * @return bool
   */
  private function fileExists() {
    $fullPath = $this->getBasePath() . $this->getBasename() . '.php';
    return is_readable($fullPath);
  }
}