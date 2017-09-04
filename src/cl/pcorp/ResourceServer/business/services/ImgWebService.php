<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\agents\ifAgent;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\model\WebImg;
use cl\pcorp\ResourceServer\common\Config;

class ImgWebService extends ImgService implements ifResourceService {

  /**
   * @var WebImg
   */
  private $webImg;

  /**
   * @param ifAgent $agent
   * @return void
   */
  public function setAgent(ifAgent $agent) {
    $this->agent = $agent;
  }

  /**
   * @param ResourceRequest $resourceRequest
   * @return ifResource
   */
  public function getResource(ResourceRequest $resourceRequest) {
    $this->resourceRequest = $resourceRequest;
    $this->webImg = $this->getEmptyResource();
    $this->setResourceProperties();
    $this->setResourceCacheControl($this->webImg);
    $this->loadImage($this->webImg);
    $this->extractImageProperties();
    return $this->webImg;
  }

  private function setResourceProperties() {
    $this->webImg->setBasename($this->resourceRequest->getBasename());
    $this->webImg->setLocation($this->getResourceStoreLocation());
    $this->webImg->setExtension($this->resourceRequest->getExtension());
  }

  /**
   * @return ifResource
   */
  public function getEmptyResource() {
    $webImage = new WebImg();
    $this->setResourceCacheControl($webImage);
    return $webImage;
  }

  private function extractImageProperties() {
    $this->webImg->setWidth($this->getWidth($this->webImg));
    $this->webImg->setHeight($this->getHeight($this->webImg));
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
    // TODO: Implement processParams() method.
  }

  /**
   * The path in the filesystem of the resource the service
   * is in charge
   *
   * @return string
   */
  public function getResourceStoreLocation() {
    return Config::get('WEB_IMG_STORE_LOCATION');
  }
}