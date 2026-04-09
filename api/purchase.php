<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_achat');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_purchase();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function get_purchase(): void
{
    $db = new DB();

    $data = $db->select("SELECT * FROM HISTORIQUE");

    http_response_code(200);
    echo json_encode(array_reverse($data));
}