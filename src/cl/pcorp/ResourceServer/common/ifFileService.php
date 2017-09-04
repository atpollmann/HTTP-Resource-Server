<?php
namespace cl\pcorp\ResourceServer\common;

use cl\pcorp\ResourceServer\exceptions\FileNotFoundException;
use cl\pcorp\ResourceServer\exceptions\FileNotSavedException;

interface ifFileService {

  /**
   * Return the file content if found.
   *
   * @param string $directory
   * @param string $filename
   * @return mixed
   * @throws FileNotFoundException
   */
  public function getFileContent(string $directory, string $filename);

  /**
   * @param string $filename
   * @param string $content
   * @return mixed
   * @throws FileNotSavedException
   */
  public function saveFile(string $filename, string $content);


}