<?php
namespace cl\pcorp\ResourceServer\agents\img;

use cl\pcorp\ResourceServer\business\model\Img;
use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\exceptions\FileNotSavedException;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManagerStatic as ImageManager;

class InterventionImgAgent implements ifImgAgent {

  public function __construct() {
    ImageManager::configure(array('driver' => 'gd'));
  }

  public function load(Img $img) {
    try {
      $imgObj = ImageManager::make($img->getPath());
      $content = (string) $imgObj->encode($img->getExtension());
      $img->setContent($content);
    } catch(\Exception $e) {
      throw new FileNotFoundException($e->getMessage() . ': ' . $img->getPath());
    }
  }

  /**
   * Using the binary content instead of the path
   * to create the image gives a performance
   * boost of 8%
   *
   * @param Img $img
   * @param $width
   * @param $height
   * @param bool $keepAspectRatio
   * @param bool $preventUpsize
   */
  public function resize(Img $img, $width, $height, $keepAspectRatio = true, $preventUpsize = true) {
    $imgObj = ImageManager::make($img->getContent())->resize($width, $height,
          function(Constraint $constraint) use($keepAspectRatio, $preventUpsize) {
            if($keepAspectRatio) $constraint->aspectRatio();
            if($preventUpsize) $constraint->upsize();
        });
    $img->setContent($imgObj->encode($img->getExtension()));
    $img->setWidth($imgObj->width());
    $img->setHeight($imgObj->height());
  }

  public function insertWatermark(Img $img, Img $watermark) {
    $original = ImageManager::make($img->getContent());
    $this->resize($watermark, $img->getWidth(), $img->getHeight());
    $original->insert($watermark->getContent());
    $img->setContent((string)$original->encode($img->getExtension()));
  }

  public function rotate(Img $img, float $angle) {
    $imgObj = ImageManager::make($img->getContent());
    $content = (string) $imgObj->rotate($angle)->encode($img->getExtension());
    $img->setContent($content);
  }

  public function crop(Img $img, int $width, int $height, int $x = 0, int $y = 0) {
    // TODO: Implement crop() method.
  }

  public function save(Img $img, string $path, int $quality = 80) {
    $image = ImageManager::make($img->getContent());
    try {
      $image->save($path, $quality);
    } catch(\Exception $e) {
      throw new FileNotSavedException($e->getMessage());
    }
  }

  public function getWidth(Img $img) {
    $imgObj = ImageManager::make($img->getContent());
    return intval($imgObj->width());
  }

  public function getHeight(Img $img) {
    $imgObj = ImageManager::make($img->getContent());
    return intval($imgObj->height());
  }

}