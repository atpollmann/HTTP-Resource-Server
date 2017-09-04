<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\agents\ifAgent;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ProductImg;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Logger;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\exceptions\WatermarkNotFoundException;

class ImgProductService extends ImgService implements ifResourceService {

  const PARAM_IMAGE_WIDTH = "width";
  const PARAM_IMAGE_HEIGHT = "height";

  /**
   * @var ProductImg
   */
  private $productImg;

  public function setAgent(ifAgent $agent) {
    $this->agent = $agent;
  }

  /**
   * @return ifResource
   */
  public function getEmptyResource() {
    $productImg = new ProductImg();
    $this->setResourceCacheControl($productImg);
    return $productImg;
  }

  /**
   * Bootstrap method. Triggers the creation
   * of the ProductImg object and processing
   * of the request
   *
   * @param ResourceRequest $resourceRequest
   * @return ProductImg
   * @throws WatermarkNotFoundException
   */
  public function getResource(ResourceRequest $resourceRequest) {
    $this->resourceRequest = $resourceRequest;
    $this->productImg = $this->getEmptyResource();
    $this->extractPartView();
    $this->setResourceProperties();
    $this->setResourceCacheControl($this->productImg);
    $this->loadImage($this->productImg);
    $this->extractImageProperties();
    $this->processParams();
    try {
      $this->watermark();
    } catch(\Exception $e) {
      throw new WatermarkNotFoundException($e->getMessage());
    } finally {
      return $this->productImg;
    }
  }

  /**
   * Extracts the partnumber and view
   * from the basename
   */
  private function extractPartView() {
    $basename = $this->resourceRequest->getBasename();
    $partNumber = $this->extractPartNumber($basename);
    $view = $this->extractView($basename);
    $this->productImg->setPartNumber($partNumber);
    $this->productImg->setView($view);
  }

  /**
   * @param string $basename
   * @return string
   */
  private function extractPartNumber(string $basename) {
    $pieces = explode(ProductImg::IMG_VIEW_DELIMITER, $basename);
    return (count($pieces) > 1) ? $pieces[0] : $basename;
  }

  /**
   * @param string $basename
   * @return int
   */
  private function extractView(string $basename) {
    $pieces = explode(ProductImg::IMG_VIEW_DELIMITER, $basename);
    return (count($pieces) > 1) ? intval($pieces[1]) : ProductImg::IMG_NO_VIEW;
  }

  private function setResourceProperties() {
    $this->productImg->setBasename($this->resourceRequest->getBasename());
    $this->productImg->setLocation($this->getResourceStoreLocation());
    $this->productImg->setExtension($this->resourceRequest->getExtension());
  }

  private function extractImageProperties() {
    $this->productImg->setWidth($this->getWidth($this->productImg));
    $this->productImg->setHeight($this->getHeight($this->productImg));
  }

  private function resizeImage() {
    $oWidth = $nWidth = $this->productImg->getWidth();
    $oHeight = $nHeight = $this->productImg->getHeight();

    $wParam = $this->resourceRequest->getParam(self::PARAM_IMAGE_WIDTH);
    $hParam = $this->resourceRequest->getParam(self::PARAM_IMAGE_HEIGHT);

    if($wParam != null) {
      $nWidth = $wParam->getValue();
    }

    if($hParam != null) {
      $nHeight = $hParam->getValue();
    }

    // Only if dimensions are different spend the
    // time calling resize procedure
    if(($oWidth != $nWidth) || ($oHeight != $nHeight)) {
      $this->resize($this->productImg, $nWidth, $nHeight, true, true);
    }
  }

  private function watermark() {
    $watermark = new ProductImg();
    $watermark->setLocation(Config::get('PRODUCT_WATERMARK_LOCATION'));
    $watermark->setBasename(Config::get('PRODUCT_WATERMARK_BASENAME'));
    $watermark->setExtension(Config::get('PRODUCT_WATERMARK_EXTENSION'));
    $this->loadFile($watermark->getPath());
    $watermark->setContent($this->getFileContent());
    $this->insertWatermark($this->productImg, $watermark);

  }

  /**
   * Processes all the valid params.
   * Applies all the modifications
   * that the resource is allowed to do to the
   * resource
   *
   * @return void
   */
  public function processParams() {
    $wParam = $this->resourceRequest->getParam(self::PARAM_IMAGE_WIDTH);
    $hParam = $this->resourceRequest->getParam(self::PARAM_IMAGE_HEIGHT);
    if($wParam != null || $hParam != null) {
      $this->resizeImage();
    }
  }

  /**
   * The path in the filesystem of the resource the service
   * is in charge
   *
   * @return string
   */
  public function getResourceStoreLocation() {
    return Config::get('PRODUCT_IMG_STORE_LOCATION');
  }
}