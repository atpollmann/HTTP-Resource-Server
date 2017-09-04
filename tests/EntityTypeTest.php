<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\business\model\EntityType;

class EntityTypeTest extends BaseTester {

  /**
   * @dataProvider entityTypeDataProvider
   * @param $typeString
   * @param $expected
   */
  public function testIsValidType($typeString, $expected) {
    $isValid = EntityType::isValidType($typeString);
    $this->assertEquals($expected['valid'], $isValid);
  }

  /**
   * @dataProvider entityTypeDataProvider
   * @param $typeString
   * @param $expected
   */
  public function testGetType($typeString, $expected) {
    $actualType = EntityType::getType($typeString);
    $this->assertEquals($expected['type'], $actualType);
  }

}
