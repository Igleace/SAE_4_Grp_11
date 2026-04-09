<?php
// www/utils.php

require_once __DIR__ . '/model/database.php';

// ------------------
// Fonctions globales
// ------------------

function generateUUID()
{
    $data = random_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return bin2hex($data);
}

function saveFile(): string|null
{
    $name = generateUUID() . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/api/files/' . $name)) {
        return $name;
    }

    return null;
}

function saveImage(): string|null
{
    if (!isset($_FILES['file']) || $_FILES['file']['tmp_name'] === '') {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES['file']['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }

    return saveFile();
}

function deleteFile(string $fileName): bool
{
    if (file_exists(__DIR__ . "/api/files/" . $fileName)) {
        unlink(__DIR__ . "/api/files/" . $fileName);
        return true;
    }
    return false;
}

// ------------------
// Filtrage / typage
// ------------------

class Filter
{
    public static function int($value, $default = 0)
    {
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function string($value, $maxLength = null)
    {
        $str = is_string($value) ? trim($value) : '';
        if ($maxLength !== null && strlen($str) > $maxLength) {
            $str = substr($str, 0, $maxLength);
        }
        return $str;
    }

    public static function float($value, $default = 0.0)
    {
        return is_numeric($value) ? (float) $value : $default;
    }

    public static function bool($value)
    {
        return (bool) $value;
    }

    public static function date($value)
    {
        return self::string($value);
    }
}

// ------------------
// Classe Tools unifiée
// ------------------

class Tools
{

    public static function sanitize($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function methodAccepted(...$acceptedContentType): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        foreach ($acceptedContentType as $type) {
            if (str_starts_with($contentType, $type)) {
                return true;
            }
        }

        http_response_code(415);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Unsupported Media Type',
            'message' => "Content-Type '{$contentType}' is not supported. Accepted types: " . implode(', ', $acceptedContentType),
        ]);
        exit;
    }

    public static function hasPermission(string $permission): bool
    {
        if (!isset($_SESSION['userid'])) {
            return false;
        }

        $db = new DB();
        $perms = $db->select(
            "SELECT * FROM LISTE_PERMISSIONS WHERE id_membre = ?",
            'i',
            [$_SESSION['userid']]
        );

        if (count($perms) === 0 || !isset($perms[0][$permission]) || $perms[0][$permission] == 0) {
            return false;
        }

        return true;
    }

    public static function checkPermission(string $permission): void
    {
        if (!self::hasPermission($permission)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.',
            ]);
            exit;
        }
    }

    public static function json(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }
}