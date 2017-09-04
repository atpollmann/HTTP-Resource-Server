<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpRequest;

class HttpRequestTest extends BaseTester {

  /**
   * For each of the given $uriHandlers runs its
   * method identified by $methodName and expects
   * the results contained in the $expected array
   *
   * @param array $httpRequestAgent
   * @param string $methodName
   * @param array $expected
   */
  private function baseGetTest(
    array $httpRequestAgent,
    string $methodName,
    array $expected
  ) {
    if($expected['exception'] == false) {
      foreach($httpRequestAgent as $classname => $httpRequest) {
        $fn = 'get' . ucfirst($methodName);
        $this->assertEquals(
          $expected[$methodName],
          $httpRequest->$fn(),
          "Unexpected results in " . $classname . "::get" . $methodName
        );
      }
    }
  }

  /**
   * @dataProvider URIDataSetProvider
   * @param string $URIString
   * @param array $expected
   */
  public function testHttpRequestAgents(string $URIString, array $expected) {
    $httpAgents = array(
      LeagueHttpRequest::class => new LeagueHttpRequest($URIString),
      SymfonyHttpRequest::class => new SymfonyHttpRequest($URIString)
    );

    $testsMethods = array(
      'URI',
      'path',
      'pathArray',
      'pathCount',
      'query',
      'queryArray'
    );

    foreach($testsMethods as $test) {
      $this->baseGetTest($httpAgents, $test, $expected);
    }
  }
}
