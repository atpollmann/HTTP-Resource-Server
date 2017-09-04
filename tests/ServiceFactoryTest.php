<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\business\model\EntityType;
use cl\pcorp\ResourceServer\business\model\ResourceType;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\business\services\ResourceServiceFactory;

class ServiceFactoryTest extends BaseTester {

  public function testGetServiceThrowsFileNotFoundException() {
    $type = 'Type';
    $entity = 'Entity';
    $filename = __DIR__ .
      '../src/atoledo/ResourceServer/business/services/' .
                $type .
                $entity . 'php';
    $this->assertFileNotExists($filename, 'The test file exists, so the test cant continue');
    $this->expectException(FileNotFoundException::class);
    $factory = new ResourceServiceFactory();
    $handler = $factory->getService($type, $entity);
  }

  public function testGetServiceFromFactory() {
    $resourceType = ResourceType::RESOURCE_TYPE_IMAGE;
    $entity = EntityType::ENTITY_TYPE_PRODUCT;
    $namespace = 'cl\pcorp\ResourceServer\business\services';
    $class = ucfirst($resourceType) . ucfirst($entity) . 'Service';
    $factory = new ResourceServiceFactory();
    $service = $factory->getService($resourceType, $entity);
    $this->assertInstanceOf($namespace . '\\' . $class, $service);
  }


}
