<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Grade.php'; // ou ../model/grade.php si le fichier est en minuscule
require_once __DIR__ . '/../model/File.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_grade');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_grades();
        break;

    case 'POST':     // CREATE
        create_grade();
        break;

    case 'PUT':      // UPDATE (données seulement)
        if (Tools::methodAccepted('application/json')) {
            update_grade();
        }
        break;

    case 'PATCH':    // UPDATE (image seulement)
        update_image();
        break;

    case 'DELETE':   // DELETE
        delete_grade();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function get_grades(): void
{
    if (isset($_GET['id'])) {
        $id = FilterAdmin::int($_GET['id']);
        $grade = Grade::getInstance($id);

        if ($grade === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Grade not found']);
            return;
        }

        http_response_code(200);
        echo json_encode($grade);
        return;
    }

    $grades = Grade::bulkFetch();

    http_response_code(200);
    echo json_encode($grades);
}

function create_grade(): void
{
    $grade = Grade::create(
        "Nouveau grade",
        "Ceci est un nouveau grade",
        10.99,
        null,
        0
    );

    http_response_code(201);
    echo json_encode($grade);
}

function update_grade(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide an id']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !is_array($data) ||
        !isset($data['name'], $data['description'], $data['price'], $data['reduction'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Incomplete data']);
        return;
    }

    $id          = FilterAdmin::int($_GET['id']);
    $name        = FilterAdmin::string($data['name'], maxLenght: 100);
    $description = FilterAdmin::string($data['description'], maxLenght: 500);
    $price       = FilterAdmin::float($data['price']);
    $reduction   = FilterAdmin::int($data['reduction']);

    $grade = Grade::getInstance($id);

    if ($grade === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Grade not found']);
        return;
    }

    $grade->update($name, $description, $price, $reduction);

    http_response_code(200);
    echo json_encode($grade);
}

function update_image(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide an id']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $grade = Grade::getInstance($id);

    if ($grade === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Grade not found']);
        return;
    }

    $image = File::saveImage();

    if (!$image) {
        http_response_code(400);
        echo json_encode(['error' => 'Image could not be processed']);
        return;
    }

    $grade->updateImage($image);

    http_response_code(200);
    echo json_encode($grade);
}

function delete_grade(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide an id']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $grade = Grade::getInstance($id);

    if ($grade === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Grade not found']);
        return;
    }

    $grade->delete();

    http_response_code(200);
    echo json_encode(['message' => 'Grade deleted']);
}
