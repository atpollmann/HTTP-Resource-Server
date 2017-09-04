<?php
namespace cl\pcorp\ResourceServer\app\http;

use Symfony\Component\HttpFoundation\Request;

class SymfonyHttpRequest implements ifHttpRequest {

  /**
   * @var Request
   */
  private $request;

  private $pathArray = null;
  private $normalizedURL = null;
  private $normalizedPath = null;
  private $queryArray = array();


  public function __construct($url = null) {
    if($url == null) {
      $this->request = Request::createFromGlobals();
    } else {
      $this->request = Request::create($url);
    }
  }

  /**
   * Returns the request uri AS DEFINED IN THE
   * SPECIFICATION DOCUMENT (it does not include
   * the scheme nor the authority segments)
   *
   * @return string
   */
  public function getURI() {
    if($this->normalizedURL == null) {
      $this->normalizedURL = $this->normalizeURL($this->request->getRequestUri());
    }
    return $this->normalizedURL;
  }

  /**
   * Returns the uri path as a string
   * The path must not contain dot segments, trailing
   * or leading slashes nor empty segments
   * ie.: foo.com/bar/./..//baz/01.jpg returns
   * "bar/baz/01.jpg"
   *
   * @return string
   */
  public function getPath() {
    if($this->normalizedPath == null) {
      $this->normalizedPath = $this->normalizeURL($this->request->getPathInfo());
    }
    return $this->normalizedPath;
  }

  /**
   * Returns an array of the path segments
   * It does not consider dot or empty segments
   * ie.: foo.com/bar/./..//baz/01.jpg
   * returns ['bar', 'baz', '01.jpg']
   *
   * @return array
   */
  public function getPathArray() {
    if($this->pathArray == null) {
      $this->pathArray = explode('/', $this->getPath());
    }
    return $this->pathArray;
  }

  /**
   * Returns the number of segments in the path
   * It does not consider dot or empty segments
   * ie.: foo.com/bar/./..//baz/ returns 2
   *
   * @return int
   */
  public function getPathCount() {
    return count($this->getPathArray());
  }

  /**
   * Returns the query string of the uri
   *
   * ie.: foo.com/bar/baz?param1=hello&param2=from the chan
   * returns "param1=hello&param2=from%20the%20chan"
   *
   * @return string
   */
  public function getQuery() {
    return $this->request->getQueryString();
  }

  /**
   * Returns an associative array representation of the query
   *
   * ie.: foo.com/bar/baz?param1=hello&param2=from the chan
   * returns ["param1" => "hello", "param2" => "from the chan"]
   *
   * @return array
   */
  public function getQueryArray() {
    if($this->queryArray == null) {
      foreach($this->request->query as $key => $value) {
        $this->queryArray[$key] = $value;
      }
    }
    return $this->queryArray;
  }

  public function getETag() {
    $etag = null;
    $etags = $this->request->getETags();
    if(count($etags) > 0) {
      $etag = $etags[0];
    }
    return str_replace("\"", "", $etag);
  }

  /**
   * Cleans url segments like '///', '/../', etc
   *
   * @param $url
   * @return string
   */
  private function normalizeURL($url) {
    return preg_replace('/^\/|\.{1,}\/|\/{2,}/U', "", $url);
  }
}