<?php
namespace cl\pcorp\ResourceServer\app\cache;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\model\ifResource;

interface ifResourceCacheServiceBuilder {

  /**
   * @param ResourceRequest $resourceRequest
   * @return ResourceCacheService
   */
  public static function withResourceRequest(ResourceRequest $resourceRequest);

  /**
   * @param ifResource $resource
   * @return ResourceCacheService
   */
  public function withResource(ifResource $resource);

  /**
   * @param string $location
   * @return ResourceCacheService
   */
  public function withCacheStoreLocation(string $location);

  /**
   * @return ResourceCacheService
   */
  public function build();

}