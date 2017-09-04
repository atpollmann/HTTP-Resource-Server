<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\agents\AgentFactory;
use cl\pcorp\ResourceServer\app\Dispatcher;
use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpRequest;
use cl\pcorp\ResourceServer\app\RequestParser;
use cl\pcorp\ResourceServer\business\model\Resource;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\services\ResourceServiceFactory;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Utils;

class ResourceServiceTest extends BaseTester {

  /**
   * @dataProvider URIDataSetProvider
   * @param string $URIString
   * @param array $expected
   */
  public function testParsesRawParams(string $URIString, array $expected) {

  }

  /**
   * @dataProvider resourceRequestProvider
   * @param ResourceRequest $resourceRequest
   */
  public function testSetsResourceCacheControlParams(ResourceRequest $resourceRequest) {
    try {
      $resourceFactory = new ResourceServiceFactory();
      $agentFactory = new AgentFactory();
      $service = $resourceFactory->getService($resourceRequest->getResourceType(), $resourceRequest->getEntityType());
      $agent = $agentFactory->getAgent(Config::getAgentName($resourceRequest->getExtension()), $resourceRequest->getResourceType());
      $service->setAgent($agent);

      $resource = $service->getResource($resourceRequest);
      $resourceControl = Config::getCacheControlParams(Utils::getSimpleClassName(get_class($resource)));

      $this->assertEquals($resourceControl["isReusable"], $resource->isReusable());
      $this->assertEquals($resourceControl["alwaysMustRevalidate"], $resource->getAlwaysRevalidate());
      $this->assertEquals($resourceControl["isPublic"], $resource->isPublic());
      $this->assertEquals($resourceControl["maxCacheLifetime"], $resource->getMaxCacheLifetime());
    } catch(\Exception $e) {}

  }

}
