<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\RequestParser;
use cl\pcorp\ResourceServer\exceptions\BadURIFormatException;

class RequestParserTest extends BaseTester {

  private function baseTest($URIString, $expected, $entity) {
    $this->leagueBaseTest($URIString, $expected, $entity);
    //$this->symfonyBaseTest($URIString, $expected, $entity);
  }

  private function leagueBaseTest($URIString, $expected, $entity) {
    $parser = new RequestParser(new LeagueHttpRequest($URIString));
    if($expected['exception'] == BadURIFormatException::class) {
      $this->expectException($expected['exception']);
    }
    $fn = "get" . ucfirst($entity);
    $request = $parser->getResourceRequest();
    $callResult = $request->$fn();
    // some requests are expected to receive
    // another type of exception not triggered with the
    // parsing of the request. In that case, the $expected
    // array will only have the "exception" key
    if(isset($expected[$entity])) {
      $this->assertEquals($expected[$entity], $callResult);
    }
  }

  private function symfonyBaseTest($URIString, $expected, $entity) {
    $this->assertTrue(false, __FUNCTION__ . " not implemented");
  }

  private function leagueThrowsBadURIEception($badURIString) {
    $this->expectException(BadURIFormatException::class);
    $parser = new RequestParser(new LeagueHttpRequest($badURIString));
    $request = $parser->getResourceRequest();
  }

  private function symfonyThrowsBadURIEception($badURIString) {
    $this->assertTrue(false, __FUNCTION__ . " not implemented");
  }

  /**
   * @dataProvider badURIDataProvider
   * @param $badURIString
   */
  public function testThrowsBadURIException($badURIString) {
    $this->leagueThrowsBadURIEception($badURIString);
    //$this->symfonyThrowsBadURIEception($badURIString);
  }

  /**
   * @dataProvider URIDataSetProvider
   * @param $URIString
   * @param $expected
   */
  public function testGetFilename($URIString, $expected) {
    $this->baseTest($URIString, $expected, 'filename');
  }

  /**
   * @dataProvider URIDataSetProvider
   * @param $URIString
   * @param $expected
   */
  public function testGetBasename($URIString, $expected) {
    $this->baseTest($URIString, $expected, 'basename');
  }

  /**
   * @dataProvider URIDataSetProvider
   * @param $URIString
   * @param $expected
   */
  public function testGetParams($URIString, $expected) {
    $this->baseTest($URIString, $expected, 'rawParams');
  }

  /**
   * @dataProvider URIDataSetProvider
   * @param $URIString
   * @param $expected
   */
  public function testGetResourceType($URIString, $expected) {
    $this->baseTest($URIString, $expected, 'resourceType');
  }

  /**
   * @dataProvider URIDataSetProvider
   * @param $URIString
   * @param $expected
   */
  public function testGetEntityType($URIString, $expected) {
    $this->baseTest($URIString, $expected, 'entityType');
  }

}
