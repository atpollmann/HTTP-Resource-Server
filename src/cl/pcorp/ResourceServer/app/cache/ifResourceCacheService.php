<?php
namespace cl\pcorp\ResourceServer\app\cache;

use cl\pcorp\ResourceServer\business\model\Param;
use cl\pcorp\ResourceServer\exceptions\CacheMissException;

interface ifResourceCacheService {

  /**
   * Gets a resource from cache
   *
   * @return Resource
   * @throws CacheMissException
   */
  public function getContent();

  /**
   * Stores a resource in cache
   *
   * @return mixed
   */
  public function store();

  /**
   * Constructs the cacheable uri by concatenating the
   * elements in path and adding, as the query string,
   * the cacheable elements in $params.
   *
   * @param string[] $path
   * @param Param[] $params
   * @return string
   */
  public function getCacheableUri(array $path, array $params);

  /**
   * @return string
   */
  public function generateCacheablefilename();

}