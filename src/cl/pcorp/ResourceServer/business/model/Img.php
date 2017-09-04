<?php
namespace cl\pcorp\ResourceServer\business\model;

abstract class Img extends Resource {

  /**
   * @var int In pixels
   */
  private $width;

  /**
   * @var int In pixels
   */
  private $height;

  public function __construct() {
    $this->setType(ResourceType::RESOURCE_TYPE_IMAGE);
  }

  /**
   * @return int
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * @param int $width
   */
  public function setWidth($width) {
    $this->width = intval($width);
  }

  /**
   * @return int
   */
  public function getHeight() {
    return $this->height;
  }

  /**
   * @param int $height
   */
  public function setHeight($height) {
    $this->height = intval($height);
  }

}