<?php
namespace cl\pcorp\ResourceServer\business\model;

class ParamBuilder {

  private $key = "";
  private $value = "";
  private $isCacheable = true;
  private $validationRegex = "";
  private $valid = false;

  public static function withKey($key) {
    $builder = new self;
    $builder->key = $key;
    return $builder;
  }

  public function withValue(string $value) {
    $this->value = $value;
    return $this;
  }

  public function cacheable(bool $cacheable) {
    $this->isCacheable = $cacheable;
    return $this;
  }

  public function withValidationRegex(string $regex) {
    $this->validationRegex = $regex;
    return $this;
  }

  public function isValid(bool $valid) {
    $this->valid = $valid;
    return $this;
  }

  /**
   * @return Param
   */
  public function build() {
    $param = new Param();
    $param->setKey($this->key);
    $param->setValue($this->value);
    $param->setCacheable($this->isCacheable);
    $param->setValidationRegex($this->validationRegex);
    $param->setValid($this->valid);
    return $param;
  }


}