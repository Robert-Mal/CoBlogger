<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function upload(string $targetDirectory, UploadedFile $file): string
    {
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
        }

        return $fileName;
    }

    public function remove(string $targetDirectory, string $fileName): void
    {
        $this->filesystem->remove($targetDirectory . '/' . preg_replace('/\/\w+\//i', '', $fileName));
    }
}
