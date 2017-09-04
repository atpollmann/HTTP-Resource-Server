<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\agents\ifAgent;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

interface ifResourceService {

  /**
   * @param ifAgent $agent
   * @return void
   */
  public function setAgent(ifAgent $agent);

  /**
   * @param ResourceRequest $resourceRequest
   * @return ifResource
   */
  public function getResource(ResourceRequest $resourceRequest);

  /**
   * @return ifResource
   */
  public function getEmptyResource();

  /**
   * Ask the params parsing service to validate
   * the raw params comming in the request.
   * The valid params are kept in the $params property
   *
   * @param ResourceRequest $resourceRequest
   * @return Param[]
   */
  public function parseRawParams(ResourceRequest $resourceRequest);

  /**
   * Processes all the valid params.
   * Applies all the modifications
   * that the resource is allowed to do to the
   * resource
   *
   * @return void
   */
  public function processParams();

  /**
   * Gets the unix timestamp of the last modification
   * time of the file
   *
   * @param string $filename
   * @return int|null
   */
  public function getLastModificationTime(string $filename);

  /**
   * The path in the filesystem of the resource the service
   * is in charge
   *
   * @return string
   */
  public function getResourceStoreLocation();
}