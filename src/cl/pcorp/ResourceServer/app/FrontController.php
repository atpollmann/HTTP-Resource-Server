<?php
namespace cl\pcorp\ResourceServer\app;

use cl\pcorp\ResourceServer\app\cache\ifResourceCacheService;
use cl\pcorp\ResourceServer\app\cache\ifResourceCacheServiceBuilder;
use cl\pcorp\ResourceServer\app\cache\ResourceCacheService;
use cl\pcorp\ResourceServer\app\http\ifHttpRequest;
use cl\pcorp\ResourceServer\app\http\ifHttpResponse;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpRequest;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpResponse;
use cl\pcorp\ResourceServer\business\model\GenericResource;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\Param;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\business\services\ifResourceService;
use cl\pcorp\ResourceServer\business\services\ResourceServiceFactory;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\common\Logger;
use cl\pcorp\ResourceServer\exceptions\AgentErrorException;
use cl\pcorp\ResourceServer\exceptions\BadParamException;
use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;
use cl\pcorp\ResourceServer\exceptions\CacheMissException;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\exceptions\FileNotSavedException;
use cl\pcorp\ResourceServer\exceptions\InvalidTokenException;
use cl\pcorp\ResourceServer\exceptions\WatermarkNotFoundException;

class FrontController {

  /**
   * @var ifResourceCacheService|ifResourceCacheServiceBuilder
   */
  private $cacheService;
  /**
   * @var Dispatcher
   */
  private $dispatcher;

  /**
   * @var ResourceRequest
   */
  private $resourceRequest;

  /**
   * @var ifResourceService
   */
  private $resourceService;

  /**
   * @var ifResource
   */
  private $resource;

  /**
   * @var int
   */
  private $fileModificationTimestamp;

  /**
   * @var ifHttpResponse
   */
  private $response;

  /**
   * @var int
   */
  private $responseStatusCode;

  /**
   * @var ifHttpRequest
   */
  private $request;

  private function __construct() {}

  public static function dispatch(string $configFile) {
    Config::init($configFile);
    Logger::init();
    $front = new self;
    $front->fetch();
  }

  private function fetch() {
    try {
      $this->prepareResourceRequest();
      if($this->isRequestAuthorized()) {
        if($this->resource->isReusable() && $this->sameETags()) {
          $this->setUnmodifiedResponse();
        } else {
          $this->responseStatusCode = ifHttpResponse::HTTP_OK;
          $this->retrieveResource();
        }
      } else {
        $this->setUnauthorizedResponse();
      }
    } catch(\Exception $e) {
      $this->handleException($e);
    } finally {
      $this->send();
    }
  }

  private function prepareResourceRequest() {
    $this->response = new SymfonyHttpResponse();
    $this->request = new SymfonyHttpRequest();
    $this->dispatcher = new Dispatcher();
    $requestParser = new RequestParser($this->request);
    Logger::debug("Request: " . $this->request->getURI());
    $this->resourceRequest = $requestParser->getResourceRequest();

    $serviceFactory = new ResourceServiceFactory();
    $this->resourceService = $serviceFactory->getService(
      $this->resourceRequest->getResourceType(),
      $this->resourceRequest->getEntityType()
    );
    $params = $this->resourceService->parseRawParams($this->resourceRequest);
    $this->resourceRequest->setParams($params);

    $this->resource = $this->resourceService->getEmptyResource();

    // Cache service
    $this->cacheService = ResourceCacheService::
    withResourceRequest($this->resourceRequest)
      ->build();
    $path = $this->resourceRequest->getPath();
    $params = $this->resourceRequest->getParams();
    $cacheableUri = $this->cacheService->getCacheableUri($path,$params);
    $this->resourceRequest->setCacheableUri($cacheableUri);
  }

  private function isRequestAuthorized() {
    // TODO: implement
    return true;
  }

  private function sameETags() {
    $theSame = false;
    $storeLocation = $this->resourceService->getResourceStoreLocation();
    $filename = $this->resourceRequest->getFilename();
    $filePath = $storeLocation . $filename;

    $requestETag = $this->request->getETag();
    $responseETag = $this->resourceService->getLastModificationTime($filePath);

    $this->fileModificationTimestamp = $responseETag;

    Logger::debug("Request eTag: " . $requestETag);
    Logger::debug("Response eTag: " . $responseETag);

    if($requestETag != null && $responseETag != null && $requestETag == $responseETag) {
      $theSame = true;
    }

    return $theSame;
  }

  private function setUnmodifiedResponse() {
    $this->responseStatusCode = ifHttpResponse::HTTP_NOT_MODIFIED;
  }

  private function retrieveResource() {
    if($this->resourceRequest->rawParamExists(Param::PARAM_NO_CACHING)  ) {
      $this->retrieveFromLibrary();
    } else {
      $this->retrieveFromCache();
    }
  }

  private function retrieveFromLibrary() {
    Logger::debug("Retrieving from library");
    $this->resource = $this->dispatcher->getResource($this->resourceRequest, $this->resourceService);
    if($this->resource->isReusable()) {
      $this->cacheService->withResource($this->resource);
      $this->cacheService->store();
    }
  }

  private function retrieveFromCache() {
    try {
      Logger::debug("Retrieving from cache");
      $this->resource = $this->resourceService->getEmptyResource();
      $extension = $this->resourceRequest->getExtension();
      $this->resource->setExtension($extension);
      $this->cacheService->withResource($this->resource);
      $content = $this->cacheService->getContent();
      $this->resource->setContent($content);
    } catch(CacheMissException $e) {
      $this->retrieveFromLibrary();
    }
  }

  private function setUnauthorizedResponse() {
    $this->response->setStatusCode(ifHttpResponse::HTTP_UNAUTHORIZED);
  }

  private function send() {
    $this->prepareResponse();
    //if($this->responseStatusCode == ifHttpResponse::HTTP_OK) {
      $this->prepareResponseContent();
    //}
    $this->response->send();
  }

  private function prepareResponse() {
    $this->response->setStatusCode($this->responseStatusCode);
    if($this->responseStatusCode < 400) {
      $this->prepareCacheSettings();
    }
  }

  private function prepareCacheSettings() {
    if($this->resource->isReusable()) {
      $this->response->setMaxAge($this->resource->getMaxCacheLifetime());
      $this->response->setETag($this->fileModificationTimestamp);
      if($this->resource->getAlwaysRevalidate()) {
        $this->response->setNoCache();
      } else {
        $this->resource->setPublic($this->resource->isPublic());
      }
    } else {
      $this->response->setNoStore();
    }
  }

  private function prepareResponseContent() {
    $this->response->setContentType($this->resourceRequest->getExtension());
    if($this->resource->isPublic()) {
      $this->response->setPublic();
    } else {
      $this->response->setPrivate();
    }

    $this->response->setContent($this->resource->getContent());
  }

  private function handleException(\Exception $e) {
    $type = get_class($e);
    $this->responseStatusCode = ifHttpResponse::HTTP_INTERNAL_SERVER_ERROR;

    if($type == FileNotFoundException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_NOT_FOUND; }
    if($type == BadURIFormatException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_BAD_REQUEST; }
    if($type == BadParamException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_BAD_REQUEST; }
    if($type == AgentErrorException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_INTERNAL_SERVER_ERROR; }
    if($type == InvalidTokenException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_UNAUTHORIZED; }
    if($type == FileNotSavedException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_OK; }
    if($type == WatermarkNotFoundException::class) { $this->responseStatusCode = ifHttpResponse::HTTP_OK; }

    $this->prepareGenericErrorPage();
  }

  private function prepareGenericErrorPage() {
    if($this->resource != null) {
      $this->resource = new GenericResource();
      $this->resource->setExtension("html");
      $this->resource->setContent("Error " . $this->responseStatusCode);
    }
  }

}