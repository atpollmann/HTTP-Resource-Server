<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\business\model\Param;
use cl\pcorp\ResourceServer\business\model\ParamBuilder;
use cl\pcorp\ResourceServer\business\model\ResourceRequest;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\exceptions\BadParamException;
use cl\pcorp\ResourceServer\exceptions\InvalidParamValueException;

class ParamsParsingService {

  /**
   * Fetches from configuration all the params accepted
   * by the entity given <b>and all its super classes</b>
   *
   * @param $entityType
   * @param $resourceType
   * @return Param[]
   */
  public function fetchAllowedResourceParams($entityType, $resourceType) {
    $allowed = array();
    $allowed = array_merge($allowed, $this->parseResourceParamsFromConfig($entityType . ucfirst($resourceType)));
    $allowed = array_merge($allowed, $this->parseResourceParamsFromConfig($resourceType));
    $allowed = array_merge($allowed, $this->parseResourceParamsFromConfig("resource"));
    ksort($allowed, SORT_STRING);
    return $allowed;
  }

  /**
   * Identify which raw params present in the request
   * are allowed, validate the values and returns
   * the array of params
   *
   * @param ResourceRequest $resourceRequest
   * @return Param[]
   */
  public function parseRequestedParams(ResourceRequest $resourceRequest) {
    $allowedParams = $this->fetchAllowedResourceParams(
      $resourceRequest->getEntityType(),
      $resourceRequest->getResourceType());
    $rawParams = $resourceRequest->getRawParams();
    $allowedParams = $this->doIntersection($allowedParams, $rawParams);

    $this->validateParams($allowedParams, $rawParams);

    return $allowedParams;

  }

  /**
   * Fetches an entry $type in the configuration
   * and builds a Param object for each entry found.
   * Returns an array of params with their key as the
   * array key
   *
   * @param string $type
   * @return Param[]
   */
  private function parseResourceParamsFromConfig(string $type) {
    $configParams = Config::getResourceParams($type);
    $paramObjects = array();
    if(count($configParams) > 0) {
      foreach($configParams as $key => $config) {
        $paramObjects[$key] = ParamBuilder::withKey($key)
          ->withValidationRegex($config["validationRegex"])
          ->cacheable($config["cacheable"])
          ->build();
      }
    }
    return $paramObjects;
  }

  /**
   * @param Param[] $allowedParams
   * @param array $rawParams
   */
  private function validateParams($allowedParams, $rawParams) {
    foreach($allowedParams as $key => $paramObj) {
      $paramObj->setValue($rawParams[$key]);
      $paramObj->setValid($this->validateParam(
        $paramObj->getValue(),
        $paramObj->getValidationRegex()));
    }
  }

  /**
   * @param $value
   * @param $regex
   * @return bool
   * @throws InvalidParamValueException
   */
  private function validateParam($value, $regex) {
    $valid = preg_match("/" . $regex . "/", $value);
    if(!$valid) {
      throw new InvalidParamValueException("One or more params have invalid values");
    }
    return (bool) $valid;
  }

  /**
   * @param $allowedParams
   * @param $rawParams
   * @return Param[]
   * @throws BadParamException
   */
  private function doIntersection($allowedParams, $rawParams) {
    $allowedParams = array_intersect_key($allowedParams, $rawParams);
    if(count($allowedParams) != count($rawParams)) {
      throw new BadParamException("One or more param are not allowed");
    }

    return $allowedParams;
  }
}