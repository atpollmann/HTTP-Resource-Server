<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\RequestParser;
use cl\pcorp\ResourceServer\business\model\EntityType;
use cl\pcorp\ResourceServer\business\model\ParamBuilder;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\model\ResourceType;
use cl\pcorp\ResourceServer\business\services\ParamsParsingService;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Logger;

class ParamsParsingServiceTest extends BaseTester {

  /**
   * @dataProvider paramsProvider
   * @param string $entityType
   * @param string $resourceType
   * @param array $expectedParams
   */
  public function testFetchAllowedResourceParams(string $entityType, string $resourceType, array $expectedParams) {
    $service = new ParamsParsingService();
    $params = $service->fetchAllowedResourceParams($entityType, $resourceType);
    $this->assertEquals($expectedParams, $params);
  }

  /**
   * @dataProvider requestsProvider
   * @param ResourceRequest $resourceRequest
   * @param array $expected
   */
  public function testParseRequestedParams(ResourceRequest $resourceRequest, array $expected) {
    $service = new ParamsParsingService();
    if(isset($expected["params"])) {
      $params = $service->parseRequestedParams($resourceRequest);
      foreach($expected["params"] as $i => $param) {
        $paramObject = $params[$param["key"]];
        $this->assertEquals(
          $param["isValid"],
          $paramObject->isValid(),
          "param " . $paramObject->getKey().
          " of resource " . $resourceRequest->getEntityType().$resourceRequest->getResourceType().
          " with value " . $paramObject->getValue().
          " must be" . $param["isValid"]);
      }
      $this->assertCount(count($expected["params"]), $params);
    }
  }

  /**
   * @dataProvider requestsProvider
   * @param ResourceRequest $resourceRequest
   * @param array $expected
   */
  public function testServiceTrowsExceptions(ResourceRequest $resourceRequest, array $expected) {
    if($expected["exception"]) {
      $service = new ParamsParsingService();
      $this->expectException($expected["exception"]);
      $params = $service->parseRequestedParams($resourceRequest);
    }
  }

  public function paramsProvider() {
    Config::init(__DIR__ . "/config/config.json");
    $expected = array();
    $expected = array_merge($expected, $this->resourceParamsObjectProvider("productImg"));
    $expected = array_merge($expected, $this->resourceParamsObjectProvider("img"));
    $expected = array_merge($expected, $this->resourceParamsObjectProvider("resource"));
    return array(
      array(
        "entityType" => EntityType::ENTITY_TYPE_PRODUCT,
        "resourceType" => ResourceType::RESOURCE_TYPE_IMAGE,
        "expectedParams" => $expected
      )
    );
  }

  public function requestsProvider() {
    Config::init(__DIR__ . "/config/config.json");
    Logger::init();
    $dataSet = array();
    foreach($this->URIDataSetProvider() as $entry) {
      $URIString = $entry["URIString"];
      $expected = $entry["expected"];
      try {
        $requestParser = new RequestParser(new LeagueHttpRequest($URIString));
        $request = $requestParser->getResourceRequest();
        $dataSet[] = array($request, $expected);
      } catch(\Exception $e) {}
    }
    return $dataSet;
  }

  private function resourceParamsObjectProvider($type) {
    $resourceParamsFromConfig = Config::getResourceParams($type);
    $resourceParamsObjects = array();
    foreach($resourceParamsFromConfig as $key => $value) {
      $resourceParamsObjects[$key] = ParamBuilder::withKey($key)
        ->cacheable($value["cacheable"])
        ->withValidationRegex($value["validationRegex"])
        ->build();
    }
    return $resourceParamsObjects;
  }


}
