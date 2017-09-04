<?php
namespace cl\pcorp\ResourceServer\app;

use cl\pcorp\ResourceServer\agents\AgentFactory;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\services\ifResourceService;
use cl\pcorp\ResourceServer\common\Config;

class Dispatcher {

  /**
   * @var Resource
   */
  private $resource;

  /**
   * @var string
   */
  private $uriString;

  /**
   * @param ResourceRequest $resourceRequest
   * @param ifResourceService $resourceService
   * @return ifResource
   */
  public function getResource(ResourceRequest $resourceRequest, ifResourceService $resourceService) {
    $agentFactory = new AgentFactory();
    $resourceType = $resourceRequest->getResourceType();
    $agentName = Config::getAgentName($resourceRequest->getExtension());
    $agent = $agentFactory->getAgent($agentName, $resourceType);
    $resourceService->setAgent($agent);
    $resource = $resourceService->getResource($resourceRequest);

    return $resource;
  }
}