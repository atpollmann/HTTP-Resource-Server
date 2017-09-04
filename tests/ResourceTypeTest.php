<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\business\model\ResourceType;

class ResourceTypeTest extends BaseTester {

  /**
   * @dataProvider resourceTypeDataProvider
   * @param $typeString
   * @param $expected
   */
  public function testIsValidType($typeString, $expected) {
    $isValid = ResourceType::isValidType($typeString);
    $this->assertEquals($expected['valid'], $isValid);
  }

  /**
   * @dataProvider resourceTypeDataProvider
   * @param $typeString
   * @param $expected
   */
  public function testGetType($typeString, $expected) {
    $actualType = ResourceType::getType($typeString);
    $this->assertEquals($expected['type'], $actualType);
  }
}
