<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageService
{
    private string $uploadDir;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->uploadDir = $parameterBag->get('kernel.project_dir') . '/public/uploads/colis';
    }

    /**
     * Upload une image et retourne le nom du fichier
     */
    public function upload(UploadedFile $file): ?string
    {
        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Générer un nom unique pour le fichier
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->uploadDir, $newFilename);
            return $newFilename;
        } catch (FileException $e) {
            return null;
        }
    }

    /**
     * Upload plusieurs images et retourne un tableau de noms de fichiers
     *
     * @param UploadedFile[] $files
     * @return string[]
     */
    public function uploadMultiple(array $files): array
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = $this->upload($file);
                if ($filename) {
                    $uploadedFiles[] = $filename;
                }
            }
        }

        return $uploadedFiles;
    }
}

