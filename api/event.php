<?php
session_start();

require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../model/File.php';
require_once __DIR__ . '/../model/database.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

Tools::checkPermission('p_evenement');

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':      // READ
        get_events();
        break;

    case 'POST':     // CREATE
        create_event();
        break;

    case 'PUT':      // UPDATE (données seulement)
        if (Tools::methodAccepted('application/json')) {
            update_event();
        }
        break;

    case 'PATCH':    // UPDATE (image seulement)
        update_image();
        break;

    case 'DELETE':   // DELETE
        delete_event();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

function get_events(): void
{
    if (isset($_GET['id'])) {
        $id = FilterAdmin::int($_GET['id']);
        $event = Event::getInstance($id);

        if ($event === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Event not found']);
            return;
        }

        http_response_code(200);
        echo json_encode($event);
        return;
    }

    $events = Event::bulkFetch();

    http_response_code(200);
    echo json_encode($events);
}

function create_event(): void
{
    $event = Event::create(
        "Nouvel événement",
        "Description de l'événement",
        0,
        0,
        false,
        0,
        "Lieu de l'événement",
        "2021-01-01"
    );

    http_response_code(201);
    echo json_encode($event);
}

function update_event(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !is_array($data) ||
        !isset(
            $data['nom'],
            $data['description'],
            $data['xp'],
            $data['places'],
            $data['reductions'],
            $data['prix'],
            $data['lieu'],
            $data['date']
        )
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Incomplete data']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $event = Event::getInstance($id);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        return;
    }

    $event->update(
        FilterAdmin::string($data['nom'], maxLenght: 100),
        FilterAdmin::string($data['description'], maxLenght: 1000),
        FilterAdmin::int($data['xp']),
        FilterAdmin::int($data['places'], -100000),
        FilterAdmin::bool($data['reductions']),
        FilterAdmin::float($data['prix']),
        FilterAdmin::string($data['lieu'], maxLenght: 50),
        FilterAdmin::date($data['date'])
    );

    http_response_code(200);
    echo json_encode($event);
}

function update_image(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $event = Event::getInstance($id);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        return;
    }

    $image = File::saveImage();

    if (!$image) {
        http_response_code(400);
        echo json_encode(['error' => 'Image could not be processed']);
        return;
    }

    $event->updateImage($image);

    http_response_code(200);
    echo json_encode($event);
}

function delete_event(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        return;
    }

    $id = FilterAdmin::int($_GET['id']);
    $event = Event::getInstance($id);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        return;
    }

    $event->delete();

    http_response_code(200);
    echo json_encode(['message' => 'Event deleted']);
}
