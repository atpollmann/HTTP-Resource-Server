<?php
namespace cl\pcorp\ResourceServer\app\http;

use Symfony\Component\HttpFoundation\Response;
use cl\pcorp\ResourceServer\common\Config;

class SymfonyHttpResponse implements ifHttpResponse {

  /**
   * @var Response
   */
  private $response;

  public function __construct() {
    $this->response = new Response();
  }

  public function send() {
    $this->response->setProtocolVersion("1.1");
    $this->response->send();
  }

  public function setContent($content) {
    $this->response->setContent($content);
  }

  public function setStatusCode(int $code) {
    $this->response->setStatusCode($code);
  }

  /**
   * @return int
   */
  public function getStatusCode() {
    return $this->response->getStatusCode();
  }

  public function setMaxAge(int $value) {
    $this->response->setMaxAge($value);
  }

  public function setLastModified(\DateTime $value) {
    $this->response->setLastModified($value);
  }

  public function setHeader(string $key, string $value) {
    $this->response->headers->set($key, $value);
  }

  public function setETag(string $tag) {
    $this->response->setEtag($tag);
  }

  /**
   * @return void
   */
  public function setPublic() {
    $this->response->setPublic();
  }

  /**
   * @return void
   */
  public function setPrivate() {
    $this->response->setPrivate();
  }

  /**
   * @param string $extension
   * @return void
   */
  public function setContentType(string $extension) {
    $contentType = Config::get('DEFAULT_CONTENT_TYPE');
    $mimes = Config::getMimes();
    if(array_key_exists($extension, $mimes)) {
      $contentType = $mimes[$extension];
    }

    $this->setHeader("Content-type", $contentType);
  }

  /**
   * Sets the cache-control: no-store
   *
   * @return void
   */
  public function setNoStore() {
    $this->setHeader("Cache-control", "no-store");
  }

  /**
   * Sets the cache-control: no-cache
   *
   * @return void
   */
  public function setNoCache() {
    $this->setHeader("Cache-control", "no-cache");
  }
}