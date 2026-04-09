<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/News.php';
require_once __DIR__ . '/../model/File.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_actualite');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_news();
        break;

    case 'POST':     // CREATE
        create_news();
        break;

    case 'PUT':      // UPDATE (données)
        if (Tools::methodAccepted('application/json')) {
            update_news();
        }
        break;

    case 'PATCH':    // UPDATE (image)
        update_image();
        break;

    case 'DELETE':   // DELETE
        delete_news();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function get_news(): void
{
    if (isset($_GET['id'])) {
        $id = FilterAdmin::int($_GET['id']);
        $news = News::getInstance($id);

        if ($news === null) {
            http_response_code(404);
            echo json_encode(['error' => 'News not found']);
            return;
        }

        http_response_code(200);
        echo json_encode($news);
        return;
    }

    $news = News::bulkFetch();

    http_response_code(200);
    echo json_encode($news);
}

function create_news(): void
{
    if (!isset($_SESSION['userid'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
        return;
    }

    $news = News::create(
        "Nouvel article",
        "Description de l'article",
        "2021-01-01",
        FilterAdmin::int($_SESSION['userid']),
        null
    );

    http_response_code(201);
    echo json_encode($news);
}

function update_news(): void
{
    if (!isset($_GET['id'], $_SESSION['userid'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $news = News::getInstance($id);

    if ($news === null) {
        http_response_code(404);
        echo json_encode(['error' => 'News not found']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data) || !isset($data['name'], $data['description'], $data['date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Incomplete data']);
        return;
    }

    $name = FilterAdmin::string($data['name'], maxLenght: 100);
    $description = FilterAdmin::string($data['description'], maxLenght: 1000);
    $date = FilterAdmin::string($data['date']);
    $id_membre = FilterAdmin::int($_SESSION['userid']);

    $news->update($name, $description, $date, $id_membre);

    http_response_code(200);
    echo json_encode($news);
}

function update_image(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $news = News::getInstance($id);

    if ($news === null) {
        http_response_code(404);
        echo json_encode(['error' => 'News not found']);
        return;
    }

    $image = File::saveImage();

    if ($image === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Image could not be processed']);
        return;
    }

    $news->updateImage($image);

    http_response_code(200);
    echo json_encode($news);
}

function delete_news(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $news = News::getInstance($id);

    if ($news === null) {
        http_response_code(404);
        echo json_encode(['error' => 'News not found']);
        return;
    }

    $news->delete();

    http_response_code(200);
    echo json_encode(['message' => 'News deleted']);
}
