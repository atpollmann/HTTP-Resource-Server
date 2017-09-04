<?php
namespace cl\pcorp\ResourceServer\business\model;

class ProductImg extends Img {

  const IMG_VIEW_DELIMITER = '_';

  /**
   * A product image with the view set
   * to IMG_NO_VIEW means the image
   * has no extra views, only one
   */
  const IMG_NO_VIEW = 0;

  /**
   * @var string The product INTERNAL part number
   *              to whom it belongs the image
   */
  private $partNumber;

  /**
   * @var int If a product has 3 pictures, each
   *          one will be a view
   */
  private $view;

  public function __construct() {
    parent::__construct();
  }

  /**
   * @return string
   */
  public function getPartNumber() {
    return $this->partNumber;
  }

  /**
   * @param string $partNumber
   */
  public function setPartNumber($partNumber) {
    $this->partNumber = $partNumber;
  }

  /**
   * @return int
   */
  public function getView() {
    return $this->view;
  }

  /**
   * @param int $view
   */
  public function setView($view) {
    $this->view = (is_int($view)) ? $view : self::IMG_NO_VIEW;
  }



}