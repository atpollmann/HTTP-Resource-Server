<?php
namespace cl\pcorp\ResourceServer\business\services;

use cl\pcorp\ResourceServer\agents\img\ifImgAgent;
use cl\pcorp\ResourceServer\business\model\Img;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;

abstract class ImgService extends ResourceService {

  /**
   * @var ifImgAgent
   */
  protected $agent;

  /**
   * @param Img $img
   * @return int
   */
  protected function getWidth(Img $img) {
    return $this->agent->getWidth($img);
  }

  /**
   * @param Img $img
   * @return int
   */
  protected function getHeight(Img $img) {
    return $this->agent->getHeight($img);
  }

  /**
   * @param Img $img
   * @param int $width
   * @param int $height
   * @param bool $keepAspectRatio
   * @param bool $preventUpsize
   * @return Img
   */
  protected function resize(Img $img, int $width, int $height, $keepAspectRatio = true, $preventUpsize = true) {
    $this->agent->resize($img, $width, $height, $keepAspectRatio, $preventUpsize);
  }

  protected function loadImage(Img $img) {
    try {
      $this->loadFile($img->getPath());
      $img->setContent($this->getFileContent());
    } catch (FileNotFoundException $exception) {
      throw $exception;
    }
  }

  protected function insertWatermark(Img $img, Img $watermark) {
    $this->agent->insertWatermark($img, $watermark);
  }



}