<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Item.php';
require_once __DIR__ . '/../model/File.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_boutique');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':
        get_items();
        break;

    case 'POST':
        create_item();
        break;

    case 'PUT':
        if (Tools::methodAccepted('application/json')) {
            update_item();
        }
        break;

    case 'PATCH':
        update_image();
        break;

    case 'DELETE':
        delete_item();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function get_items(): void
{
    if (isset($_GET['id'])) {
        $id = FilterAdmin::int($_GET['id']);
        $item = Item::getInstance($id);

        if (!$item) {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
            return;
        }
    } else {
        $item = Item::bulkFetch();
    }

    http_response_code(200);
    echo json_encode($item);
}

function create_item(): void
{
    try {
        $item = Item::create(
            "Nouvel article",
            1,
            0,
            true,
            1.99,
            File::getFile("boutique.png")
        );

        http_response_code(201);
        echo json_encode($item);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

function update_item(): void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !isset(
            $_GET['id'],
            $data['name'],
            $data['xp'],
            $data['stocks'],
            $data['reduction'],
            $data['price']
        )
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $name = FilterAdmin::string($data['name'], maxLenght: 100);
    $xp = FilterAdmin::int($data['xp']);
    $stocks = FilterAdmin::int($data['stocks'], min: -100000);
    $reduction = FilterAdmin::bool($data['reduction']);
    $price = FilterAdmin::float($data['price']);

    $item = Item::getInstance($id);

    if (!$item) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    $item->update($name, $xp, $stocks, $reduction, $price);

    http_response_code(200);
    echo json_encode($item);
}

function update_image(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $item = Item::getInstance(FilterAdmin::int($_GET['id']));

    if (!$item) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    $imageName = File::saveImage();

    if (!$imageName) {
        http_response_code(400);
        echo json_encode(['error' => 'Image could not be processed']);
        return;
    }

    $item->getImage()?->deleteFile();
    $item->updateImage($imageName);

    http_response_code(200);
    echo json_encode($item);
}

function delete_item(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $item = Item::getInstance($id);

    if (!$item) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    $item->delete();

    http_response_code(200);
    echo json_encode(['message' => 'Item deleted']);
}
