<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../utils.php';

class File implements JsonSerializable
{
    private string $fileName;

    public function getFileName(): string
    {
        return $this->fileName;
    }

    private function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public static function getFile(string|null $fileName): File|null
    {
        if (!is_null($fileName) && file_exists(__DIR__ . '/../assets/uploads/events/' . $fileName)) {
            return new File($fileName);
        }

        return null;
    }

    public static function saveFile(): ?File
    {
        if (!isset($_FILES['file'])) {
            throw new Exception('Aucun fichier reçu');
        }

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erreur upload PHP : ' . $_FILES['file']['error']);
        }

        $uploadDir = __DIR__ . '/../api/files/';

        if (!is_dir($uploadDir)) {
            throw new Exception('Dossier inexistant : ' . $uploadDir);
        }

        if (!is_writable($uploadDir)) {
            throw new Exception('Dossier non inscriptible : ' . $uploadDir);
        }

        $originalName = basename($_FILES['file']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $uploadDir . $newName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            throw new Exception('move_uploaded_file a échoué vers : ' . $destination);
        }

        return File::getFile($newName);
    }

    // cf. mon commentaire de la méthode ci dessus
    public static function saveImage(): File|null
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        $rawData = file_get_contents('php://input');
        if (!$rawData) {
            return null;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tmpFile, $rawData);

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpFile);

        if (!in_array($mimeType, $allowedTypes, true)) {
            unlink($tmpFile);
            return null;
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $extension = $extensions[$mimeType];
        $name = generateUUID() . '.' . $extension;

        $targetDir = __DIR__ . '/../assets/uploads/events/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . $name;

        if (rename($tmpFile, $targetPath)) {
            chmod($targetPath, 0644);
            return new File($name);
        }

        @unlink($tmpFile);
        return null;
    }

    public function deleteFile(): bool
    {
        $path = __DIR__ . '/../assets/uploads/events/' . $this->fileName;
        if (file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }

    public function __toString(): string
    {
        return $this->fileName;
    }

    public function jsonSerialize(): string
    {
        return $this->fileName;
    }
}