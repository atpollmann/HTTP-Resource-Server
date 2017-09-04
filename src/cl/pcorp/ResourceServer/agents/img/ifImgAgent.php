<?php
namespace cl\pcorp\ResourceServer\agents\img;

use cl\pcorp\ResourceServer\agents\ifAgent;
use cl\pcorp\ResourceServer\business\model\Img;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\exceptions\FileNotSavedException;

interface ifImgAgent extends ifAgent {

  /**
   * Receives an image resource with no content
   * It must load the image from the location designated
   * by the image file location, and store it in the
   * content field of the resource object
   *
   * @param Img $img
   * @throws FileNotFoundException
   */
  public function load(Img $img);

  /**
   * @param Img $img
   * @param $width
   * @param $height
   * @param bool $keepAspectRatio
   * @param bool $preventUpsize
   */
  public function resize(Img $img, $width, $height, $keepAspectRatio = true, $preventUpsize = true);

  /**
   * @param Img $img
   * @param Img $watermark
   */
  public function insertWatermark(Img $img, Img $watermark);

  /**
   * @param Img $img
   * @param float $angle
   * @internal param float $degrees
   */
  public function rotate(Img $img, float $angle);

  /**
   * @param Img $img
   * @param int $width
   * @param int $height
   * @param int $x
   * @param int $y
   */
  public function crop(Img $img, int $width, int $height, int $x = 0, int $y = 0);

  /**
   * @param Img $img
   * @param string $path
   * @param int $quality From 0 to 100
   * @return void
   * @throws FileNotSavedException
   */
  public function save(Img $img, string $path, int $quality = 80);

  /**
   * @param Img $img
   * @return int
   */
  public function getWidth(Img $img);

  /**
   * @param Img $img
   * @return int
   */
  public function getHeight(Img $img);

}