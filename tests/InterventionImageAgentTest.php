<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\agents\img\InterventionImgAgent;
use cl\pcorp\ResourceServer\business\model\ProductImg;
use cl\pcorp\ResourceServer\business\model\ResourceType;

class InterventionImageAgentTest extends \PHPUnit_Framework_TestCase {

  public function testLoad() {
    $agent = new InterventionImgAgent();
    $image = $this->getImgObject('testimage_50x30.jpg');
    $this->assertNull($image->getContent());
    $agent->load($image);
    $this->assertNotNull($image->getContent());
    $this->assertGreaterThan(1000, strlen($image->getContent()));
  }

  public function testResizeAspectRatioPreventUpsizing() {
    $kar = true;
    $pu = true;
    $filename = 'testimage_400x600.png';
    $this->resize($filename, 100, 100, 67, 100, $kar, $pu);
    $this->resize($filename, 800, 900, 400, 600, $kar, $pu);
    $this->resize($filename, 10, 1024, 10, 15, $kar, $pu);
  }

  public function testResizeNoAspectRatioPreventUpsizing() {
    $kar = false;
    $pu = true;
    $filename = 'testimage_400x600.png';
    $this->resize($filename, 100, 100, 100, 100, $kar, $pu);
    $this->resize($filename, 1024, null, 400, 600, $kar, $pu);
    $this->resize($filename, 399, 599, 399, 599, $kar, $pu);
  }

  public function testResizeAspectRatioNoPreventUpsizing() {
    $kar = true;
    $pu = false;
    $filename = 'testimage_50x30.jpg';
    $this->resize($filename, 100, 100, 100, 60, $kar, $pu);
    $this->resize($filename, 1024, 614, 1024, 614, $kar, $pu);
  }

  public function testResizeNoAspectRatioNoPreventUpsizing() {
    $kar = false;
    $pu = false;
    $filename = 'testimage_50x30.jpg';
    $this->resize($filename, 100, 100, 100, 100, $kar, $pu);
    $this->resize($filename, 10, 10, 10, 10, $kar, $pu);
  }

  public function testGetWidth() {
    $agent = new InterventionImgAgent();
    $img = $this->getImgObject('testimage_400x600.png');
    $agent->load($img);
    $realWidth = $this->getImageWidth($img->getPath());
    $this->assertEquals($realWidth, $agent->getWidth($img));
  }

  public function testGetHeight() {
    $agent = new InterventionImgAgent();
    $img = $this->getImgObject('testimage_400x600.png');
    $agent->load($img);
    $realHeight = $this->getImageHeight($img->getPath());
    $this->assertEquals($realHeight, $agent->getHeight($img));
  }

  private function resize(
                          $filename,
                          $newWidth,
                          $newHeight,
                          int $expectedWidth,
                          int $expectedHeight,
                          bool $keepAspectRatio,
                          bool $preventUpsizing
                          ) {
    $agent = new InterventionImgAgent();
    $img = $this->getImgObject($filename);
    $agent->load($img);
    $path = $img->getPath();
    $outputPath = __DIR__ . '/static/testimage_resized_' .
      $expectedWidth .'x' . $expectedHeight .
      '.' . $img->getExtension();

    $this->assertEquals(
      $img->getWidth(),
      $this->getImageWidth($path),
      "Original image width is not " . $img->getWidth()
    );

    $this->assertEquals(
      $img->getHeight(),
      $this->getImageHeight($path),
      "Original image height is not " . $img->getHeight()
    );

    $agent->resize($img, $newWidth, $newHeight, $keepAspectRatio, $preventUpsizing);

    $this->assertEquals(
      $expectedWidth,
      $img->getWidth(),
      "Field 'width' of resized Img object is not $expectedWidth");

    $this->assertEquals(
      $expectedHeight,
      $img->getHeight(),
      "Field 'height' of resized Img object is not $expectedHeight");

    if(file_put_contents($outputPath, $img->getContent()) === false) {
      $this->assertTrue(false, "Could not write $outputPath");
    } else {
      $resizedImageWidth = $this->getImageWidth($outputPath);
      $resizedImageHeight = $this->getImageHeight($outputPath);

      $this->assertEquals(
        $expectedWidth,
        $resizedImageWidth,
        "Resulting image width is not $expectedWidth, is $resizedImageWidth");

      $this->assertEquals(
        $expectedHeight,
        $resizedImageHeight,
        "Resulting image height is not $expectedHeight is $resizedImageHeight");
    }
  }

  private function getImgObject($filename) {
    $parts = explode('.', $filename);
    $dimensions = array();
    preg_match('/^.+(\d{1,})x(\d{1,})\.\w{3,4}?$/U', $filename, $dimensions);
    $img = new ProductImg();
    $img->setBasename($parts[0]);
    $img->setExtension($parts[1]);
    $img->setLocation(__DIR__ . '/static/');
    $img->setType(ResourceType::RESOURCE_TYPE_IMAGE);
    $img->setWidth($dimensions[1]);
    $img->setHeight($dimensions[2]);
    return $img;

  }

  private function getImageWidth($filename) {
    try {
      $meta = getimagesize($filename);
      return $meta[0];
    } catch(\Exception $e) {
      exit("Could not read $filename");
    }
  }

  private function getImageHeight($filename) {
    try {
      $meta = getimagesize($filename);
      return $meta[1];
    } catch(\Exception $e) {
      exit("Could not read $filename");
    }
  }



}
