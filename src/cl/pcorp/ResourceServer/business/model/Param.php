<?php
namespace cl\pcorp\ResourceServer\business\model;

class Param {

  const PARAM_NO_CACHING = 'no_caching';

  /**
   * @var string
   */
  private $key;

  /**
   * @var string
   */
  private $value;

  /**
   * Whether the param will stay in the uri
   * that the cache service will hash, as part
   * of the filename of the cached file, or
   * will be extracted
   *
   * @var bool
   */
  private $cacheable = true;

  /**
   * A regular expression that will validate
   * the $value member
   * @var string
   */
  private $validationRegex;

  /**
   * @var boolean
   */
  private $valid;

  /**
   * @return string
   */
  public function getKey(): string {
    return $this->key;
  }

  /**
   * @param string $key
   */
  public function setKey(string $key) {
    $this->key = $key;
  }

  /**
   * @return mixed
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @param mixed $value
   */
  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * @return boolean
   */
  public function isCacheable(): bool {
    return $this->cacheable;
  }

  /**
   * @param boolean $cacheable
   */
  public function setCacheable(bool $cacheable) {
    $this->cacheable = $cacheable;
  }

  /**
   * @return string
   */
  public function getValidationRegex(): string {
    return $this->validationRegex;
  }

  /**
   * @param string $validationRegex
   */
  public function setValidationRegex(string $validationRegex) {
    $this->validationRegex = $validationRegex;
  }

  /**
   * @return boolean
   */
  public function isValid(): bool {
    return $this->valid;
  }

  /**
   * @param boolean $valid
   */
  public function setValid(bool $valid) {
    $this->valid = $valid;
  }

}
