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
    /** @var string */
    private $targetDir;

    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    public function delete(string $fileName): void
    {

        $file = $this->getTargetDir()."/".$fileName;

        $fs = new Filesystem();

        try {
            $fs->remove($file);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while deleting file at ".$e->getPath();
        }
    }

    public function getTargetDir(): string
    {
        return $this->targetDir;
    }
}