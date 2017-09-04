<?php
namespace cl\pcorp\ResourceServer\business\model;

interface ifResource {

  /**
   * @return string
   */
  public function getType();

  /**
   * @param string $type Any ResourceType type
   */
  public function setType($type);

  /**
   * @return String
   */
  public function getBasename();

  /**
   * @param String $basename
   */
  public function setBasename($basename);

  /**
   * @return String
   */
  public function getFilename();

  /**
   * @param String $filename
   */
  public function setFilename(String $filename);

  /**
   * @return String
   */
  public function getLocation();

  /**
   * @param String $location
   */
  public function setLocation($location);

  /**
   * @return String
   */
  public function getExtension();

  /**
   * @param String $extension
   */
  public function setExtension($extension);

  /**
   * @return string
   */
  public function getContent();

  /**
   * @param string $content
   */
  public function setContent($content);

  /**
   * @return string
   */
  public function getPath();

  /**
   * @return boolean
   */
  public function isReusable();

  /**
   * @param boolean $isReusable
   */
  public function setReusable($isReusable);

  /**
   * @return bool
   */
  public function getAlwaysRevalidate();

  /**
   * @param bool $alwaysRevalidate
   */
  public function setAlwaysRevalidate($alwaysRevalidate);

  /**
   * @return boolean
   */
  public function isPublic();

  /**
   * @param boolean $isPublic
   */
  public function setPublic($isPublic);

  /**
   * @return int
   */
  public function getMaxCacheLifetime(): int;

  /**
   * @param int $maxCacheLifetime
   */
  public function setMaxCacheLifetime(int $maxCacheLifetime);
}