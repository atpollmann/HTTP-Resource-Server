<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\common\Factory;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class ResourceServiceFactory extends Factory{

  /**
   * @var string
   */
  private $type;

  /**
   * @var string
   */
  private $entity;

  /**
   * @param string $resourceType Img, doc, audio, css, js
   * @param string $entityType Tech, product, web, etc.
   * @param string $basePath For testing purposes.
   *  The location of the services.
   *  If blank, the location is the same as this class
   * @return ifResourceService An instance of the requested service object
   * @throws FileNotFoundException
   * @TODO Implement a resource pool to reuse instances
   */
  public function getService($resourceType, $entityType, $basePath = '') {
    $this->type = $resourceType;
    $this->entity = $entityType;
    $this->setBasename($this->getServiceBasename());
    $this->setNamespace(__NAMESPACE__);

    if(!empty($basePath)) {
      $this->setBasePath($basePath);
    } else {
      $this->setBasePath(__DIR__ . '/');
    }

    return $this->getInstance();
  }

  /**
   * @return string
   */
  private function getServiceBasename() {
    return ucfirst($this->type)
      . ucfirst($this->entity)
      . "Service";
  }

}