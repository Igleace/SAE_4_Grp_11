<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Role.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_role');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_role();
        break;

    case 'POST':     // CREATE
        create_role();
        break;

    case 'PUT':      // UPDATE
        if (Tools::methodAccepted('application/json')) {
            update_role();
        }
        break;

    case 'DELETE':   // DELETE
        delete_role();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

/**
 * GET /api/role.php
 * GET /api/role.php?id=1
 */
function get_role(): void
{
    if (isset($_GET['id'])) {
        $id = FilterAdmin::int($_GET['id']);

        $role = Role::getInstance($id);

        if (!$role) {
            http_response_code(404);
            echo json_encode(["message" => "Role not found"]);
            return;
        }

        $data = $role->toJson();
    } else {
        $data = Role::bulkFetch();
    }

    http_response_code(200);
    echo json_encode($data);
}

/**
 * POST /api/role.php
 * Crée un rôle de test avec toutes les permissions à 0
 */
function create_role(): void
{
    $role = Role::create(
        "Nouveau role",
        0, // p_log
        0, // p_boutique
        0, // p_reunion
        0, // p_utilisateur
        0, // p_grade
        0, // p_role
        0, // p_actualite
        0, // p_evenement
        0, // p_comptabilite
        0, // p_achat
        0  // p_moderation
    );

    http_response_code(201);
    echo json_encode($role);
}

/**
 * PUT /api/role.php?id=1
 * Body JSON :
 * {
 *   "name": "...",
 *   "permissions": {
 *     "p_log": true, "p_boutique": false, ...
 *   }
 * }
 */
function update_role(): void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['permissions'], $_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing parameters']);
        return;
    }

    $id   = FilterAdmin::int($_GET['id']);
    $name = FilterAdmin::string($data['name']);

    $p_log          = FilterAdmin::bool($data['permissions']['p_log']          ?? false);
    $p_boutique     = FilterAdmin::bool($data['permissions']['p_boutique']     ?? false);
    $p_reunion      = FilterAdmin::bool($data['permissions']['p_reunion']      ?? false);
    $p_utilisateur  = FilterAdmin::bool($data['permissions']['p_utilisateur']  ?? false);
    $p_grade        = FilterAdmin::bool($data['permissions']['p_grade']        ?? false);
    $p_role         = FilterAdmin::bool($data['permissions']['p_role']         ?? false);
    $p_actualite    = FilterAdmin::bool($data['permissions']['p_actualite']    ?? false);
    $p_evenement    = FilterAdmin::bool($data['permissions']['p_evenement']    ?? false);
    $p_comptabilite = FilterAdmin::bool($data['permissions']['p_comptabilite'] ?? false);
    $p_achat        = FilterAdmin::bool($data['permissions']['p_achat']        ?? false);
    $p_moderation   = FilterAdmin::bool($data['permissions']['p_moderation']   ?? false);

    $role = Role::getInstance($id);

    if (!$role) {
        http_response_code(404);
        echo json_encode(['message' => 'Role not found']);
        return;
    }

    $role->update(
        $name,
        $p_log,
        $p_boutique,
        $p_reunion,
        $p_utilisateur,
        $p_grade,
        $p_role,
        $p_actualite,
        $p_evenement,
        $p_comptabilite,
        $p_achat,
        $p_moderation
    );

    http_response_code(200);
    echo json_encode($role);
}

/**
 * DELETE /api/role.php?id=1
 */
function delete_role(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing parameters']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);

    $role = Role::getInstance($id);

    if (!$role) {
        http_response_code(404);
        echo json_encode(['message' => 'Role not found']);
        return;
    }

    $role->delete();

    http_response_code(200);
    echo json_encode(['message' => 'Role deleted']);
}
