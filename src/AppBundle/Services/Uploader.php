<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    private $target_dir;

    public function __construct($target_dir)
    {
        $this->target_dir = $target_dir;
    }

    public function Upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->target_dir, $fileName);

        return $fileName;
    }
}