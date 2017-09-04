<?php
namespace cl\pcorp\ResourceServer\app;

use cl\pcorp\ResourceServer\app\http\ifHttpRequest;
use cl\pcorp\ResourceServer\business\model\EntityType;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\model\ResourceType;
use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;

class RequestParser {

  /**
   * @var ResourceRequest The request model
   */
  private $resourceRequest;

  /**
   * @var ifHttpRequest
   */
  private $httpRequest;

  /**
   * RequestParser constructor.
   * @param ifHttpRequest $httpRequest
   */
  public function __construct(ifHttpRequest $httpRequest) {
    $this->httpRequest = $httpRequest;
  }

  /**
   * @return ResourceRequest
   * @throws BadURIFormatException
   */
  public function getResourceRequest() {
    $this->resourceRequest = new ResourceRequest($this->httpRequest->getURI());
    $this->parseRequest();
    return $this->resourceRequest;
  }

  /**
   * @throws BadURIFormatException
   */
  private function parseRequest() {
    try {
      $this->resourceRequest->setPath($this->httpRequest->getPathArray());
      $this->resourceRequest->setPathCount($this->httpRequest->getPathCount());
      $this->resourceRequest->setFilename($this->parseFilename($this->httpRequest));
      $this->resourceRequest->setBasename($this->parseBasename());
      $this->resourceRequest->setExtension($this->parseExtension());
      $this->resourceRequest->setRawParams($this->httpRequest->getQueryArray());
      $this->resourceRequest->setResourceType($this->parseResourceType());
      $this->resourceRequest->setEntityType($this->parseEntityType());
    } catch(\Exception $e) {
      throw new BadURIFormatException($e->getMessage());
    }
  }

  /**
   * @param ifHttpRequest $httpRequest
   * @return string
   * @throws \Exception
   */
  private function parseFilename(ifHttpRequest $httpRequest) {

    $path = $httpRequest->getPathArray();
    $count = $httpRequest->getPathCount();
    $lastSegment = '';
    if($count >= 1) {
      $lastSegment = $path[$count - 1];
      if(!preg_match('/\./', $lastSegment)) {
        throw new \Exception('Unidentified filename');
      }
    }
    return $lastSegment;
  }

  /**
   * We already checked for the correct
   * filename in the parseFilename method,
   * so we suppose that the dot exists or
   * an exception would have ocurred
   *
   * @return string
   */
  private function parseBasename() {
    $filename = $this->resourceRequest->getFilename();
    return substr($filename, 0, strrpos($filename, "."));
  }

  /**
   * @return string
   */
  private function parseExtension() {
    $filename = $this->resourceRequest->getFilename();
    return substr($filename, strrpos($filename, ".") + 1);
  }

  /**
   * @return string
   * @throws \Exception
   */
  private function parseResourceType() {
    $resourceType = null;
    $path = $this->resourceRequest->getPath();
    $pathCount = count($this->resourceRequest->getPath());
    if($pathCount >= 1) {
      $parsedType = $path[0];
      // CSS resources sometimes may be an image
      if(
        $parsedType == ResourceType::RESOURCE_TYPE_CSS
        && $this->isImageExtension($this->resourceRequest->getExtension())
      ) {
        $resourceType = ResourceType::RESOURCE_TYPE_IMAGE;
      } else {
        $resourceType = ResourceType::getType($parsedType);
      }
    }

    if($resourceType == null) {
      throw new \Exception('Unrecognized resource type: ' . $resourceType);
    }

    return $resourceType;
  }

  /**
   * @return null|string
   * @throws \Exception
   */
  private function parseEntityType() {
    $entityType = null;
    $path = $this->resourceRequest->getPath();
    $pathCount = count($this->resourceRequest->getPath());
    if($pathCount >= 2) {
      $entityType = $path[1];
      // CSS resources sometimes may be an image
      if(
        ($entityType == EntityType::ENTITY_TYPE_LOCAL
        || $entityType == EntityType::ENTITY_TYPE_THIRD_PARTY)
        && $this->isImageExtension($this->resourceRequest->getExtension())
      ) {
        $entityType = EntityType::ENTITY_TYPE_WEB_ASSET;
      } else {
        $entityType = EntityType::getType($entityType);
      }
    }

    if($entityType == null) {
      throw new \Exception('Unrecognized entity type');
    }

    return $entityType;
  }

  private function isImageExtension($extension) {
    return preg_match("/jpg|png|gif/", $extension);
  }
}