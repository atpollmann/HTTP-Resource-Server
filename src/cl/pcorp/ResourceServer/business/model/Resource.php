<?php
namespace cl\pcorp\ResourceServer\business\model;

abstract class Resource implements ifResource {

  /**
   * @var string
   */
  private $type;

  /**
   * Just the name of the file, without extension
   * @var String
   */
  private $basename;

  /**
   * The path of the filesystem directory that
   * contains the resource
   * @var String
   */
  private $location;

  /**
   * @var String
   */
  private $extension;

  /**
   * Basename + extension
   *
   * @var string
   */
  private $filename;

  /**
   * Whether the resource can be cached by the client
   * or must be fetched everytime
   * ("Cache-control: no-store")
   *
   * @var bool
   */
  private $reusable = true;

  /**
   * If the resource is cached by the client, it must
   * incur in a roundtrip for the server to check
   * the ETag of the resource. If the ETags are the
   * same, a 304 will be issued. If not, the resource
   * will be downloaded along with the new ETag
   *
   * @var bool
   */
  private $alwaysRevalidate = true;

  /**
   * Public or private resource. (cacheable by CDN's)
   * If a resource has max-age specified distinct
   * of zero, it is implied that is public
   *
   * @var bool
   */
  private $public = true;

  /**
   * Time in seconds that the resource can be
   * cached by the client
   *
   * @var int
   */
  private $maxCacheLifetime;

  /**
   * @var string
   */
  private $content;

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType($type) {
    $this->type = $type;
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
  public function getFilename() {
    if($this->filename == null) {
      $filename = $this->basename . '.' . $this->extension;
    } else {
      $filename = $this->filename;
    }
    return $filename;
  }

  /**
   * @param String $filename
   */
  public function setFilename(String $filename) {
    $this->filename = $filename;
  }

  /**
   * @return String
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * @param String $location
   */
  public function setLocation($location) {
    $this->location = $location;
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
   * @return string
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * @param string $content
   */
  public function setContent($content) {
    $this->content = $content;
  }

  public function getPath() {
    return $this->getLocation() . $this->getFilename();
  }

  /**
   * @return boolean
   */
  public function isReusable() {
    return $this->reusable;
  }

  /**
   * @param boolean $reusable
   */
  public function setReusable($reusable) {
    $this->reusable = $reusable;
  }

  /**
   * @return bool
   */
  public function getAlwaysRevalidate() {
    return $this->alwaysRevalidate;
  }

  /**
   * @param mixed $alwaysRevalidate
   */
  public function setAlwaysRevalidate($alwaysRevalidate) {
    $this->alwaysRevalidate = $alwaysRevalidate;
  }

  /**
   * @return boolean
   */
  public function isPublic() {
    return $this->public;
  }

  /**
   * @param boolean $public
   */
  public function setPublic($public) {
    $this->public = $public;
  }

  /**
   * @return int
   */
  public function getMaxCacheLifetime(): int {
    return $this->maxCacheLifetime | null;
  }

  /**
   * @param int $maxCacheLifetime
   */
  public function setMaxCacheLifetime(int $maxCacheLifetime) {
    $this->maxCacheLifetime = $maxCacheLifetime;
  }

}