<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem; // to delete scan pdf
use Symfony\Component\Filesystem\Exception\IOExceptionInterface; // to delete scan pdf (errors)
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    public function delete(string $fileName) {

        $file = $this->getTargetDir()."/".$fileName;

        $fs = new Filesystem();

        try {
            $fs->remove($file);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while deleting file at ".$e->getPath();
        }
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
}