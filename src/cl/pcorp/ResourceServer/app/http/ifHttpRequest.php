<?php
namespace cl\pcorp\ResourceServer\app\http;

use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;

interface ifHttpRequest {

  /**
   * If a url is given, the service must pretend
   * that the request is to that url.
   * It is expected that if the url is not present
   * as a parameter, the constructor will deduce it
   * from the globals given by the language
   *
   * @param null $url
   * @throws BadURIFormatException
   */
  public function __construct($url = null);

  /**
   * Returns the request uri AS DEFINED IN THE
   * SPECIFICATION DOCUMENT (it does not include
   * the scheme nor the authority segments)
   *
   * @return string
   */
  public function getURI();

  /**
   * Returns the uri path as a string
   * The path must not contain dot segments, trailing
   * or leading slashes nor empty segments
   * ie.: foo.com/bar/./..//baz/01.jpg returns
   * "bar/baz/01.jpg"
   *
   * @return string
   */
  public function getPath();

  /**
   * Returns an array of the path segments
   * It does not consider dot or empty segments
   * ie.: foo.com/bar/./..//baz/01.jpg
   * returns ['bar', 'baz', '01.jpg']
   *
   * @return array
   */
  public function getPathArray();

  /**
   * Returns the number of segments in the path
   * It does not consider dot or empty segments
   * ie.: foo.com/bar/./..//baz/ returns 2
   *
   * @return int
   */
  public function getPathCount();

  /**
   * Returns the query string of the uri
   *
   * ie.: foo.com/bar/baz?param1=hello&param2=from the chan
   * returns "param1=hello&param2=from%20the%20chan"
   *
   * @return string
   */
  public function getQuery();

  /**
   * Returns an associative array representation of the query
   *
   * ie.: foo.com/bar/baz?param1=hello&param2=from the chan
   * returns ["param1" => "hello", "param2" => "from the chan"]
   *
   * @return array
   */
  public function getQueryArray();

  /**
   * Returns the ETags of the request
   *
   * @return string
   */
  public function getETag();

}