<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\agents\AgentFactory;
use cl\pcorp\ResourceServer\agents\img\InterventionImgAgent;
use cl\pcorp\ResourceServer\app\cache\ResourceCacheService;
use cl\pcorp\ResourceServer\app\Dispatcher;
use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpRequest;
use cl\pcorp\ResourceServer\app\RequestParser;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\services\ResourceServiceFactory;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Logger;
use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;
use cl\pcorp\ResourceServer\exceptions\CacheMissException;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class ResourceCacheServiceTest extends BaseTester {

  private $cacheStoreLocation = __DIR__ . "/static/cache/";

  /**
   * @dataProvider resourceProvider
   * @param ResourceRequest $resourceRequest
   * @param ifResource|Resource $resource
   */
  public function testCacheServiceStoreFile(ResourceRequest $resourceRequest, ifResource $resource) {
    $cacheService = ResourceCacheService::withResourceRequest($resourceRequest)
      ->withCacheStoreLocation($this->cacheStoreLocation)
      ->withResource($resource)
      ->build();
    $cacheableUri = $cacheService->getCacheableUri($resourceRequest->getPath(), $resourceRequest->getParams());
    $resourceRequest->setCacheableUri($cacheableUri);
    $cacheableFilename = $cacheService->generateCacheablefilename();
    $cacheService->store();

    // Verify the existance of the cached resources
    $cachedFilenamePath = $this->cacheStoreLocation . $cacheableFilename;
    $this->assertFileExists($cachedFilenamePath, "The file $cachedFilenamePath does not exist");
  }

  /**
   * @return Resource[][]
   */
  public function resourceProvider() {
    Config::init(__DIR__ . "/config/config.json");
    Logger::init();
    $resources = array();
    $uriDataSet = self::resourceRequestProvider();
    $dispatcher = new Dispatcher();
    foreach($uriDataSet as $entry) {
      $resourceRequest = $entry[0];
      try {
        $resourceServiceFactory = new ResourceServiceFactory();
        // Crear el servicio
        $resourceService = $resourceServiceFactory->getService(
          $resourceRequest->getResourceType(),
          $resourceRequest->getEntityType()
        );

        // Obtener el recurso
        $resources[] = array(
          $resourceRequest,
          $dispatcher->getResource($resourceRequest, $resourceService));

      } catch(\Exception $e) {}
    }
    return $resources;
  }

  /**
   * @dataProvider cachedFileProvider
   * @param ResourceRequest $resourceRequest
   */
  public function testCacheServiceGetsFile(ResourceRequest $resourceRequest) {
    $cacheService = ResourceCacheService::withResourceRequest($resourceRequest)
      ->withCacheStoreLocation($this->cacheStoreLocation);
    $cacheableUri = $cacheService->getCacheableUri($resourceRequest->getPath(), $resourceRequest->getParams());
    $resourceRequest->setCacheableUri($cacheableUri);

    $resourceServiceFactory = new ResourceServiceFactory();
    $resourceService = $resourceServiceFactory->getService($resourceRequest->getResourceType(), $resourceRequest->getEntityType());

    $resourceService->setAgent(new InterventionImgAgent());
    $resource = $resourceService->getResource($resourceRequest);
    $cacheService->withResource($resource);

    $actualContent = $cacheService->getContent();
    $expectedContent = file_get_contents($this->cacheStoreLocation . "D018F6701000_755945adb07f0da793ef4f9fe55577d9.jpg");
    $this->assertEquals($expectedContent, $actualContent);
  }

  public function cachedFileProvider() {
    $URIString = "https://foo.bar.com/img/product/D018F6701000.jpg";
    $requestParser = new RequestParser(new LeagueHttpRequest($URIString));
    $request = $requestParser->getResourceRequest();
    return array(
      array($request)
    );
  }

  /**
   * @dataProvider implementedServiceResourceRequestProvider
   * @param ResourceRequest $resourceRequest
   */
  public function testCacheServiceThrowsCacheMissException(ResourceRequest $resourceRequest) {
    $resourceRequest->setCacheableUri("anonexistingeverfile.png");
    $this->expectException(CacheMissException::class);

    $resourceServiceFactory = new ResourceServiceFactory();
    $resourceService = $resourceServiceFactory->getService(
      $resourceRequest->getResourceType(),
      $resourceRequest->getEntityType()
    );
    $resource = $resourceService->getEmptyResource();

    $cacheService = ResourceCacheService::withResourceRequest($resourceRequest)
      ->withCacheStoreLocation($this->cacheStoreLocation)
      ->withResource($resource)
      ->build();

    $cacheService->getContent();

  }

  /**
   * @dataProvider cacheableUriProvider
   * @param string $actualUri
   * @param string $expectedUri
   */
  public function testGenerateCacheableUri(string $actualUri, string $expectedUri) {
    $this->assertEquals($expectedUri, $actualUri);
  }

  public function cacheableUriProvider() {
    $dataSet = array();
    foreach($this->URIDataSetProvider() as $entry) {
      $URIString = $entry["URIString"];
      try {
        $cacheService = new ResourceCacheService();
        $requestParser = new RequestParser(new SymfonyHttpRequest($URIString));
        $request = $requestParser->getResourceRequest();

        $resourceServiceFactory = new ResourceServiceFactory();
        $resourceService = $resourceServiceFactory->getService($request->getResourceType(), $request->getEntityType());
        $request->setParams($resourceService->parseRawParams($request));

        $cacheableUri = $cacheService->getCacheableUri(
          $request->getPath(),
          $request->getParams());

        $dataSet[] = array(
          "actualUri" => $cacheableUri,
          "expectedUri" => $entry["expected"]["cacheableURI"]);
      } catch(\Exception $e) {}
    }
    return $dataSet;
  }

}
