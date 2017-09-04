<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\agents\AgentFactory;
use cl\pcorp\ResourceServer\app\http\ifHttpRequest;
use cl\pcorp\ResourceServer\app\http\LeagueHttpRequest;
use cl\pcorp\ResourceServer\app\http\SymfonyHttpRequest;
use cl\pcorp\ResourceServer\app\RequestParser;
use cl\pcorp\ResourceServer\business\model\ifResource;
use cl\pcorp\ResourceServer\business\model\ProductImg;
use cl\pcorp\ResourceServer\business\services\ImgProductService;
use cl\pcorp\ResourceServer\common\Config;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class ImgProductServiceTest extends BaseTester{

  private $imageUri = "/img/product/15072OK31AMA.jpg";
  private $productPartNumber = "15072OK31AMA";
  private $basename = "15072OK31AMA";
  private $view = 0;
  private $imageWidth = 1024;
  private $imageHeight = 765;

  public function basenameProvider() {
    return array(
      array(
        'basename' => 'D018F6701000_1',
        'expected' => array(
          'partNumber' => 'D018F6701000',
          'view' => 1
        )
      ),
      array(
        'basename' => 'D018F6701000_01',
        'expected' => array(
          'partNumber' => 'D018F6701000',
          'view' => 1
        )
      ),
      array(
        'basename' => 'hda87da98hdiasdhiud_dsao8iu',
        'expected' => array(
          'partNumber' => 'hda87da98hdiasdhiud',
          'view' => 0
        )
      ),
      array(
        'basename' => 'WIGP40523013',
        'expected' => array(
          'partNumber' => 'WIGP40523013',
          'view' => 0
        )
      ),
      array(
        'basename' => '',
        'expected' => array(
          'partNumber' => '',
          'view' => 0
        )
      ),
      array(
        'basename' => '_567',
        'expected' => array(
          'partNumber' => '',
          'view' => 567
        )
      )
    );
  }

  public function testGetResourceThrowsFileNotFoundException() {
    $this->expectException(FileNotFoundException::class);
    $uriHandler = new LeagueHttpRequest('/img/product/NONEXISTINGFILE_2.jpg');
    $uri = new RequestParser($uriHandler);
    $request = $uri->getResourceRequest();
    $agentFactory = new AgentFactory();
    $agent = $agentFactory->getAgent("Intervention", $request->getResourceType());
    $service = new ImgProductService();
    $service->setAgent($agent);
    $service->getResource($request);
  }

  private function getResourceWithSymfonyHttpAgent() {
    return $this->getResourceGivenHttpAgent(new SymfonyHttpRequest($this->imageUri));
  }

  private function getResourceWithLeagueUriAgent() {
    return $this->getResourceGivenHttpAgent(new LeagueHttpRequest($this->imageUri));
  }

  public function testResourceWithSymfonyHttpAgent() {
    $resource = $this->getResourceWithSymfonyHttpAgent();
    $this->assertEquals(get_class($resource), ProductImg::class);
  }

  public function testResourceWithLeagueHttpAgent() {
    $resource = $this->getResourceWithLeagueUriAgent();
    $this->assertEquals(get_class($resource), ProductImg::class);
  }

  public function resourceProvider() {
    Config::init(__DIR__ . "/config/config.yml");
    $resources = array(
      array($this->getResourceWithSymfonyHttpAgent()),
      array($this->getResourceWithLeagueUriAgent())
    );
    return $resources;
  }

  /**
   * @dataProvider resourceProvider
   * @param ProductImg $productImg
   */
  public function testProductImgResourceHasCorrectPartNumber(ProductImg $productImg) {
    $this->assertEquals($productImg->getPartNumber(), $this->productPartNumber);
  }

  /**
   * @dataProvider resourceProvider
   * @param ProductImg $productImg
   */
  public function testProductImgResourceHasCorrectView(ProductImg $productImg) {
    $this->assertEquals($productImg->getView(), $this->view);
  }

  /**
   * @dataProvider resourceProvider
   * @param ProductImg $productImg
   */
  public function testProductImgResourceHasCorrectBasename(ProductImg $productImg) {
    $this->assertEquals($productImg->getBasename(), $this->basename);
  }

  /**
   * @dataProvider resourceProvider
   * @param ProductImg $productImg
   */
  public function testProductImgResourceHasCorrectImageProperties(ProductImg $productImg) {
    $this->assertEquals($this->imageWidth, $productImg->getWidth());
    $this->assertEquals($this->imageHeight, $productImg->getHeight());
  }

  private function getResourceGivenHttpAgent(ifHttpRequest $uriHandler) {
    Config::set('PRODUCT_IMG_STORE_LOCATION', __DIR__ . '/static/product/');
    Config::set('PRODUCT_WATERMARK_LOCATION', __DIR__ . '/static/product/');
    $uri = new RequestParser($uriHandler);
    $request = $uri->getResourceRequest();
    $agentFactory = new AgentFactory();
    $agent = $agentFactory->getAgent("Intervention", $request->getResourceType());
    $service = new ImgProductService();
    $service->setAgent($agent);
    $resource = $service->getResource($request);
    return $resource;
  }

  /**
   * @dataProvider basenameProvider
   * @param $basename
   * @param $expected
   */
  public function testExtractPartNumber($basename, $expected) {
    $service = new ImgProductService();
    $method = new \ReflectionMethod(
      'cl\pcorp\ResourceServer\business\services\ImgProductService',
      'extractPartNumber'
    );
    $method->setAccessible(true);
    $part = $method->invoke($service, $basename);
    $this->assertEquals($expected['partNumber'], $part);
  }

  /**
   * @dataProvider basenameProvider
   * @param $basename
   * @param $expected
   */
  public function testExtractView($basename, $expected) {
    $service = new ImgProductService();
    $method = new \ReflectionMethod(
      'cl\pcorp\ResourceServer\business\services\ImgProductService',
      'extractView'
    );
    $method->setAccessible(true);
    $view = $method->invoke($service, $basename);
    $this->assertSame($expected['view'], $view);
  }
}
