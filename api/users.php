<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Member.php';
require_once __DIR__ . '/../model/File.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_utilisateur');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_users();
        break;

    case 'POST':     // CREATE
        create_user();
        break;

    case 'PUT':      // UPDATE (données seulement)
        if (Tools::methodAccepted('application/json')) {
            update_user();
        }
        break;

    case 'PATCH':    // UPDATE (image seulement)
        update_image();
        break;

    case 'DELETE':   // DELETE
        delete_user();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

/**
 * GET /api/users.php
 * GET /api/users.php?id=1
 */
function get_users(): void
{
    if (isset($_GET['id'])) {
        // Un utilisateur précis
        $id = FilterAdmin::int($_GET['id']);
        $user = Member::getInstance($id);

        if ($user) {
            $data = $user->toJsonWithRoles();
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
            return;
        }
    } else {
        // Tous les utilisateurs (sans détails de rôles)
        $data = Member::bulkFetch();
    }

    http_response_code(200);
    echo json_encode($data);
}

/**
 * POST /api/users.php
 * (endpoint de test : crée un user bidon)
 */
function create_user(): void
{
    $user = Member::create(
        "Nom",
        "Prenom",
        "prenom.nom@univ-lemans.fr",
        null,
        "21a"
    );

    http_response_code(201);
    echo json_encode($user->toJsonWithRoles());
}

/**
 * PUT /api/users.php?id=1
 * Body JSON : { "name": "...", "firstname": "...", "email": "...", "tp": "...", "xp": 42 }
 */
function update_user(): void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !isset(
            $data['name'],
            $data['firstname'],
            $data['email'],
            $data['tp'],
            $data['xp'],
            $_GET['id']
        )
    ) {
        http_response_code(400);
        echo json_encode(["message" => "Missing parameters"]);
        return;
    }

    $id      = FilterAdmin::int($_GET['id']);
    $name    = FilterAdmin::string($data['name'], maxLenght: 100);
    $surname = FilterAdmin::string($data['firstname'], maxLenght: 100);
    $email   = FilterAdmin::email($data['email'], maxLenght: 100);
    $tp      = FilterAdmin::string($data['tp'], maxLenght: 3);
    $xp      = FilterAdmin::int($data['xp']);

    $user = Member::getInstance($id);

    if ($user) {
        $user->update($name, $surname, $email, $tp, $xp);

        http_response_code(200);
        echo json_encode($user->toJsonWithRoles());
    } else {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
    }
}

/**
 * PATCH /api/users.php?id=1
 * Body : multipart/form-data avec le fichier image
 */
function update_image(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing parameters"]);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);

    $user = Member::getInstance($id);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
        return;
    }

    $newImage = File::saveImage();

    if (!$newImage) {
        http_response_code(415);
        echo json_encode(["message" => "Image could not be processed"]);
        return;
    }

    // Suppression éventuelle de l'ancienne image
    $current = $user->toJson();
    if (!empty($current['pp_membre'])) {
        $deleteFile = File::getFile($current['pp_membre']);
        $deleteFile?->deleteFile();
    }

    // Mise à jour en base
    $user->updateProfilePic($newImage);

    http_response_code(200);
    echo json_encode($user->toJsonWithRoles());
}

/**
 * DELETE /api/users.php?id=1
 */
function delete_user(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing parameters"]);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);

    $user = Member::getInstance($id);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
        return;
    }

    $user->delete();

    http_response_code(200);
    echo json_encode(["message" => "User deleted"]);
}
