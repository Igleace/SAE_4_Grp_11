<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Accounting.php';
require_once __DIR__ . '/../model/File.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_comptabilite');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_accounting();
        break;

    case 'POST':     // CREATE
        create_accounting();
        break;

    case 'DELETE':   // DELETE
        delete_accounting();
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method Not Allowed"]);
        break;
}

function get_accounting(): void
{
    if (isset($_GET['id'])) {
        $id = FilterAdmin::int($_GET['id']);

        $data = Accounting::getInstance($id);

        if ($data === null) {
            http_response_code(404);
            echo json_encode(["message" => "Accounting file not found"]);
            return;
        }
    } else {
        $data = Accounting::bulkFetch();
    }

    http_response_code(200);
    echo json_encode($data);
}

function create_accounting(): void
{
    if (!isset($_POST['date'], $_POST['nom'], $_SESSION['userid'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing parameters"]);
        return;
    }

    $file = File::saveFile();

    if ($file === null) {
        http_response_code(400);
        echo json_encode(["message" => "Accounting file not created"]);
        return;
    }

    $date = FilterAdmin::date($_POST['date']);
    $nom = FilterAdmin::string($_POST['nom'], maxLenght: 100);
    $id_membre = FilterAdmin::int($_SESSION['userid']);

    $compta = Accounting::create($date, $nom, $file->getFileName(), $id_membre);

    http_response_code(201);
    echo json_encode($compta);
}

function delete_accounting(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing parameters"]);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);

    $compta = Accounting::getInstance($id);

    if ($compta === null) {
        http_response_code(404);
        echo json_encode(["message" => "Accounting file not found"]);
        return;
    }

    $compta->delete();

    http_response_code(200);
    echo json_encode(["message" => "Accounting file deleted"]);
}
