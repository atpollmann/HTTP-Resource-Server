<?php
namespace cl\pcorp\ResourceServer\business\model;

class ResourceRequest {

  /**
   * @var String
   */
  private $uri;

  /**
   * The URI without the non-cacheable params in
   * the query string, with the cacheable ones
   * ordered alphabetically asc
   *
   * @var String
   */
  private $cacheableUri;

  /**
   * @var array
   */
  private $path = array();

  /**
   * @var int
   */
  private $pathCount = 0;

  /**
   * A valid ResourceType or null
   *
   * @var String
   */
  private $resourceType;

  /**
   * A valid EntityType or null
   *
   * @var String
   */
  private $entityType;

  /**
   * @var String
   */
  private $filename;

  /**
   * @var String
   */
  private $basename;

  /**
   * @var String
   */
  private $extension;

  /**
   * Array of all the params that comes with the
   * request, valid or not
   *
   * @var array
   */
  private $rawParams = array();

  /**
   * Array of all the parsed (allowed) params
   * that comes with the request
   *
   * @var array
   */
  private $params = array();

  /**
   * @param String $uri
   */
  public function __construct($uri) {
    $this->uri = $uri;
  }

  /**
   * @return String
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * @param String $uri
   */
  public function setUri($uri) {
    $this->uri = $uri;
  }

  /**
   * @return String
   */
  public function getCacheableUri(): String {
    return $this->cacheableUri;
  }

  /**
   * @param String $cacheableUri
   */
  public function setCacheableUri(String $cacheableUri) {
    $this->cacheableUri = $cacheableUri;
  }

  /**
   * @return array
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * @param array $path
   */
  public function setPath($path) {
    $this->path = $path;
  }

  /**
   * @return int
   */
  public function getPathCount() {
    return $this->pathCount;
  }

  /**
   * @param int $pathCount
   */
  public function setPathCount($pathCount) {
    $this->pathCount = $pathCount;
  }

  /**
   * @return String
   */
  public function getResourceType() {
    return $this->resourceType;
  }

  /**
   * @param String $resourceType
   */
  public function setResourceType($resourceType) {
    $this->resourceType = $resourceType;
  }

  /**
   * @return String
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * @param String $entityType
   */
  public function setEntityType($entityType) {
    $this->entityType = $entityType;
  }

  /**
   * @return String
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * @param String $filename
   */
  public function setFilename($filename) {
    $this->filename = $filename;
  }

  /**
   * @return String
   */
  public function getBasename() {
    return $this->basename;
  }

  /**
   * @param String $basename
   */
  public function setBasename($basename) {
    $this->basename = $basename;
  }

  /**
   * @return String
   */
  public function getExtension() {
    return $this->extension;
  }

  /**
   * @param String $extension
   */
  public function setExtension($extension) {
    $this->extension = $extension;
  }

  /**
   * @return array
   */
  public function getParams(): array {
    return $this->params;
  }

  /**
   * @param array $params
   */
  public function setParams(array $params) {
    $this->params = $params;
  }

  public function getParam($paramName) {
    return ($this->paramExists($paramName))
      ? $this->params[$paramName]
      : null;
  }

  public function paramExists($paramName) {
    return array_key_exists($paramName, $this->params);
  }

  /**
   * @return array
   */
  public function getRawParams() {
    return $this->rawParams;
  }

  /**
   * @param array $rawParams
   */
  public function setRawParams($rawParams) {
    $this->rawParams = $rawParams;
  }

  public function rawParamExists($key) {
     return array_key_exists($key, $this->rawParams);
  }

}