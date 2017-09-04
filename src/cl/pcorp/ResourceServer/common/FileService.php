<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\exceptions\FileNotSavedException;
use Symfony\Component\Finder\Finder;

class FileService implements ifFileService {

  /**
   * Return the file content if found.
   *
   * @param string $directory
   * @param string $filename
   * @return string
   * @throws FileNotFoundException
   */
  public function getFileContent(string $directory, string $filename) {
    $finder = new Finder();
    $contents = null;
    try {
      $finder->in($directory)->files()->name($filename);
      if($finder->count() == 1) {
        $iterator = $finder->getIterator();
        $iterator->rewind();
        $contents = $iterator->current()->getContents();
      }
    } catch(\Exception $e) {
      throw new FileNotFoundException($e->getMessage());
    }

    return $contents;
  }

  /**
   * @param string $filename
   * @param string $content
   * @return mixed
   * @throws FileNotSavedException
   */
  public function saveFile(string $filename, string $content) {
    $realFilename = trim($filename);
    try {
      file_put_contents($realFilename, $content, LOCK_EX);
    } catch(\Exception $e) {
      throw new FileNotSavedException("The file $filename could not be written to disk. " . $e->getMessage());
    }
  }
}