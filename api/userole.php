<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Role.php';
require_once __DIR__ . '/../model/Member.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_role');
Tools::checkPermission('p_utilisateur');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_userRoles();
        break;

    case 'POST':     // CREATE (rôle)
        create_role();
        break;

    case 'PUT':      // SET user roles
        if (Tools::methodAccepted('application/json')) {
            setUserRoles();
        }
        break;

    case 'DELETE':   // DELETE rôle
        delete_role();
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}

function get_userRoles(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing id"]);
        return;
    }

    $id   = FilterAdmin::int($_GET['id']);
    $user = Member::getInstance($id);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
        return;
    }

    http_response_code(200);
    echo json_encode($user->getRoles());
}

/**
 * PUT /api/userole.php?id=ID
 * Body JSON: { "roles": [1, 2, 3] }
 */
function setUserRoles(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing id"]);
        return;
    }

    $id   = FilterAdmin::int($_GET['id']);
    $user = Member::getInstance($id);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
        return;
    }

    $val = json_decode(file_get_contents('php://input'), true);

    if (!is_array($val) || !isset($val['roles']) || !is_array($val['roles'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing parameters']);
        return;
    }

    // On filtre chaque id de rôle
    $roles = [];
    foreach ($val['roles'] as $roleId) {
        $roles[] = FilterAdmin::int($roleId);
    }

    $success = $user->setRoles($roles);

    if ($success) {
        http_response_code(200);
        echo json_encode($user->getRoles());
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error while updating roles"]);
    }
}
