<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFileName);
        $filename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        // Fichier original : Nom de l'image.jpg
        // Fichier normalisé : nomdelimage.jpg
        // Fichier avec identifiant unique : nomdelimage-01f4g5g8df7.jpg

        try {
            $file->move($this->getTargetDirectory(), $filename);
        } catch (FileException $e) {
           
        }

        return $filename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}