<?php
namespace cl\pcorp\ResourceServer\app\http;

use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;
use League\Uri\Schemes\Http;

class LeagueHttpRequest implements ifHttpRequest {

  /**
   * @var Http
   */
  private $uriObject = null;

  public function __construct($url = null) {
    try {
      if($url == null) {
        $this->uriObject = Http::createFromServer($_SERVER);
      } else {
        $this->uriObject = Http::createFromString($url);
      }
    } catch(\Exception $e) {
      throw new BadURIFormatException($e->getMessage());
    }

  }

  /**
   * @return string
   */
  public function getURI() {
    $uri = $this->getPath();
    $query = $this->uriObject->getQuery();
    if(!empty($query)) {
      $uri .= '?' . $query;
    }

    return $uri;
  }

  /**
   * @return string
   */
  public function getPath() {
    return $this->uriObject->path
      ->withoutEmptySegments()
      ->withoutDotSegments()
      ->withoutTrailingSlash()
      ->withoutLeadingSlash()
      ->__toString();
  }

  /**
   * @return array
   */
  public function getPathArray() {
    return explode('/', $this->getPath());
  }

  /**
   * @return int
   */
  public function getPathCount() {
    return count($this->getPathArray());
  }

  /**
   * @return string
   */
  public function getQuery() {
    return $this->uriObject->query->ksort(SORT_STRING)->__toString();
  }

  /**
   * @return array
   */
  public function getQueryArray() {
    return $this->uriObject->query->toArray();
  }

  /**
   * Returns the ETags of the request
   *
   * @return string
   */
  public function getETag() {
    // TODO: Implement getETags() method.
  }
}