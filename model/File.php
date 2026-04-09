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

    public static function getFile(string | null $fileName): File | null
    {
        if (!is_null($fileName) && file_exists(__DIR__ . '/../assets/uploads/events/' . $fileName)) {
            return new File($fileName);
        }

        return null;
    }

    public static function saveFile(): File | null
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Gestion des requêtes POST (formulaires classiques)
        if ($method === 'POST') {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return null;
            }

            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $name = generateUUID() . '.' . $extension;

            // Dossier cible : /var/www/html/assets/uploads/events/
            $targetDir = __DIR__ . '/../assets/uploads/events/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetPath = $targetDir . $name;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                chmod($targetPath, 0644);
                return new File($name);
            }

            return null;
        }

        // Gestion des requêtes PUT/PATCH
        if ($method === 'PUT' || $method === 'PATCH') {
            $putData = fopen('php://input', 'r');

            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
            chmod($tempFile, 0644);

            $tempHandle = fopen($tempFile, 'w');
            stream_copy_to_stream($putData, $tempHandle);
            fclose($putData);
            fclose($tempHandle);

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tempFile);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
                'application/pdf' => 'pdf',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'application/vnd.ms-excel' => 'xls',
            ];

            $extension = $extensions[$mimeType] ?? 'bin';
            $name = generateUUID() . '.' . $extension;

            $targetDir = __DIR__ . '/../assets/uploads/events/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetPath = $targetDir . $name;

            if (rename($tempFile, $targetPath)) {
                chmod($targetPath, 0644);
                return new File($name);
            }

            @unlink($tempFile);
            return null;
        }

        return null;
    }

    // cf. mon commentaire de la méthode ci dessus
    public static function saveImage(): File | null
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
        if (!in_array($mimeType, $allowedTypes)) {
            unlink($tmpFile);
            return null;
        }

        $savedFile = self::saveFile();

        unlink($tmpFile);

        return $savedFile;
    }

    public function deleteFile() : bool
    {
        $path = __DIR__ . '/../assets/uploads/events/' . $this->fileName;
        if (file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }

    public function __toString() : string
    {
        return $this->fileName;
    }

    public function jsonSerialize(): string
    {
        return $this->fileName;
    }
}