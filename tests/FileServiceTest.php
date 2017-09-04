<?php
namespace cl\pcorp\ResourceServer\tests;

use cl\pcorp\ResourceServer\common\FileService;
use cl\pcorp\ResourceServer\common\ifFileService;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

class FileServiceTest extends BaseTester {

  /**
   * @var ifFileService
   */
  private static $fileService;

  public function testServiceGetsFileContents() {
    $directory = __DIR__ . "/static/";
    $filename = "testimage_50x30.jpg";
    $service = $this->getServiceInstance();
    $content = file_get_contents($directory . $filename);
    $this->assertEquals($content, $service->getFileContent($directory, $filename));
  }

  public function testServiceThrowsFileNotFoundException() {
    $directory = "/non/existing/directory/";
    $filename = "file.png";
    $service = $this->getServiceInstance();
    $this->expectException(FileNotFoundException::class);
    $service->getFileContent($directory, $filename);
  }

  /**
   * @return ifFileService
   */
  private function getServiceInstance() {
    if(static::$fileService == null) {
      static::$fileService = new FileService();
    }
    return static::$fileService;
  }

}
