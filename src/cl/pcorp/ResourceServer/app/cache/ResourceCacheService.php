<?php
namespace cl\pcorp\ResourceServer\app\cache;

use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\Param;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\FileService;
use cl\pcorp\ResourceServer\common\ifFileService;
use cl\pcorp\ResourceServer\exceptions\CacheMissException;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class ResourceCacheService implements ifResourceCacheService, ifResourceCacheServiceBuilder {

  /**
   * @var ifFileService
   */
  private $fileService = null;

  /**
   * The location where the cached files will reside
   *
   * @var string
   */
  private $cacheStoreLocation = null;

  /**
   * @var ResourceRequest
   */
  private $resourceRequest = null;

  /**
   * @var ifResource
   */
  private $resource = null;

  /**
   * @param ResourceRequest $resourceRequest
   * @return ResourceCacheService
   */
  public static function withResourceRequest(ResourceRequest $resourceRequest) {
    $builder = new self;
    $builder->resourceRequest = $resourceRequest;
    return $builder;
  }

  /**
   * @param ifResource $resource
   * @return ResourceCacheService
   */
  public function withResource(ifResource $resource) {
    $this->resource = $resource;
    return $this;
  }

  /**
   * @param string $location
   * @return ResourceCacheService
   */
  public function withCacheStoreLocation(string $location) {
    $this->cacheStoreLocation = $location;
    return $this;
  }

  public function build() {
    return $this;
  }

  /**
   * Gets a resource from cache
   *
   * @return Resource
   * @throws CacheMissException
   */
  public function getContent() {
    // Build the filename
    $filename = $this->generateCacheablefilename();

    // Check for the location of the cache
    $cacheDirectory = $this->getCacheDirectory();

    // Get the file
    $fileService = $this->getFileService();

    try {
      $content = $fileService->getFileContent($cacheDirectory, $filename);
      $this->resource->setLocation($cacheDirectory);
      $this->resource->setFilename($filename);
    } catch(FileNotFoundException $e) {
      throw new CacheMissException($e->getMessage());
    }

    if($content == null) {
      throw new CacheMissException("File " . $cacheDirectory . $filename . " not found");
    }
    return $content;
  }


  /**
   * Stores a resource in cache
   *
   * @return mixed
   */
  public function store() {
    $fileService = $this->getFileService();
    $filename = $this->getCacheDirectory() . $this->generateCacheablefilename();
    $fileService->saveFile($filename, $this->resource->getContent());
  }

  /**
   * Constructs the cacheable uri by concatenating the
   * elements in path and adding, as the query string,
   * the cacheable elements in $params.
   *
   * @param string[] $path
   * @param Param[] $params
   * @return string
   */
  public function getCacheableUri(array $path, array $params) {
    $pathStr = implode("/", $path);
    $queryStr = "";
    foreach($params as $param) {
      if($param->isCacheable()) {
        $queryStr .= $param->getKey() . "=" . $param->getValue() . "&";
      }
    }
    return empty($queryStr) ? $pathStr : $pathStr . "?" . rtrim($queryStr, "&");
  }

  /**
   * @return string
   */
  public function generateCacheablefilename() {
    $hash = $this->getHash($this->resourceRequest->getCacheableUri());
    $basename = $this->resourceRequest->getBasename();
    $extension = $this->resourceRequest->getExtension();
    return $basename . "_" . $hash . "." . $extension;
  }

  private function getHash(string $cacheableUri) {
    return md5($cacheableUri);
  }

  /**
   * @return FileService|ifFileService
   */
  private function getFileService() {
    if($this->fileService == null) {
      $this->fileService = new FileService();
    }
    return $this->fileService;
  }

  /**
   * @return mixed|null|string
   */
  private function getCacheDirectory() {
    $cacheDirectory = null;
    if($this->cacheStoreLocation) {
      $cacheDirectory = $this->cacheStoreLocation;
    } else {
      $cacheDirectory = Config::get("RESOURCE_CACHE_LOCATION");
    }
    return $cacheDirectory;
  }
}