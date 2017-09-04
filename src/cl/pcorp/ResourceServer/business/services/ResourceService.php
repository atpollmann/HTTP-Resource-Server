<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\agents\ifAgent;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Utils;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

abstract class ResourceService implements ifResourceService {

  /**
   * @var ifAgent
   */
  protected $agent;

  /**
   * @var ResourceRequest
   */
  protected $resourceRequest;

  /**
   * File content string when loading the file using
   * file_get_contents
   * @var string
   */
  private $fileContent;

  /**
   * @var resource
   */
  private $filePointer;

  public function parseRawParams(ResourceRequest $resourceRequest) {
    $pps = new ParamsParsingService();
    $params = $pps->parseRequestedParams($resourceRequest);
    return $params;
  }

  /**
   * @param string $filename
   * @return resource
   * @throws FileNotFoundException
   */
  protected function openFile(string $filename) {
    if(!is_readable($filename)) {
      throw new FileNotFoundException("The file $filename is not readable");
    }
    $this->filePointer = fopen($filename, "rb");
    if(!$this->filePointer) {
      throw new FileNotFoundException("The file $filename could not be opened");
    }
    return $this->filePointer;
  }

  protected function closeFile() {
    fclose($this->filePointer);
  }

  /**
   * @param string $filename
   * @throws FileNotFoundException
   */
  protected function loadFile(string $filename) {
    try {
      $this->fileContent = file_get_contents($filename);
    } catch(\Exception $e) {
      $msg = "The content of the file $filename could not be loaded: " .
              $e->getMessage();
      throw new FileNotFoundException($msg);
    }
  }

  /**
   * @return string
   */
  protected function getFileContent() {
    return $this->fileContent;
  }

  /**
   * @param string $filename
   * @return int
   */
  public function getLastModificationTime(string $filename) {
    $time = 0;
    if(file_exists($filename)) {
      $time = filemtime($filename);
    }
    return $time;
  }

  /**
   * Sets the resource cache control parameters
   *
   * @param ifResource $resource
   */
  protected function setResourceCacheControl(ifResource $resource) {
    $resourceName = Utils::getSimpleClassName(get_class($resource));
    $cacheControl = Config::getCacheControlParams($resourceName);

    $resource->setReusable($cacheControl["isReusable"]);
    $resource->setPublic($cacheControl["isPublic"]);

    if($cacheControl["isReusable"]) {
      $resource->setAlwaysRevalidate($cacheControl["alwaysMustRevalidate"]);
      $resource->setMaxCacheLifetime($cacheControl["maxCacheLifetime"]);
    }
  }

}