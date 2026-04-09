<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../model/Member.php';
require_once __DIR__ . '/../model/File.php';

class EventsController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index()
    {
        $isLoggedIn = isset($_SESSION["userid"]);
        $show = isset($_GET['show']) && is_numeric($_GET['show']) ? (int) $_GET['show'] : 5;

        $date = getdate();
        $sql_date = $date["year"] . "-" . $date["mon"] . "-" . $date["mday"];

        $current_date = new DateTime(date("Y-m-d"));

        $events_to_display = $this->db->select(
            "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement 
             FROM EVENEMENT 
             WHERE date_evenement >= ? AND deleted = false 
             ORDER BY date_evenement ASC;",
            "s",
            [$sql_date]
        );
        $passed_events = $this->db->select(
            "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement 
             FROM EVENEMENT 
             WHERE date_evenement < ? AND deleted = false 
             ORDER BY date_evenement ASC 
             LIMIT ?;",
            "si",
            [$sql_date, $show]
        );
        $events = array_merge($passed_events, $events_to_display);

        $closest_event_id = "";
        if (!empty($events)) {
            $closest_event_id = $events[0]['id_evenement'];
        }

        $processed_events = [];
        foreach ($events as $event) {
            $eventid = $event["id_evenement"];

            $isPlaceDisponible = $this->db->select(
                "SELECT (EVENEMENT.places_evenement - (
                    SELECT COUNT(*) 
                    FROM INSCRIPTION 
                    WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement
                )) > 0 AS isPlaceDisponible 
                 FROM EVENEMENT 
                 WHERE EVENEMENT.id_evenement = ?;",
                "i",
                [$eventid]
            )[0]['isPlaceDisponible'];

            $subscription_status = 'not-subscribed';
            $label = "S'inscrire";

            if (!$isPlaceDisponible) {
                $subscription_status = 'full';
                $label = "Complet";
            } elseif ($isLoggedIn) {
                $isSubscribed = !empty($this->db->select(
                    "SELECT MEMBRE.id_membre 
                     FROM MEMBRE 
                     JOIN INSCRIPTION ON MEMBRE.id_membre = INSCRIPTION.id_membre 
                     WHERE MEMBRE.id_membre = ? 
                       AND INSCRIPTION.id_evenement = ?;",
                    "ii",
                    [$_SESSION['userid'], $eventid]
                ));
                if ($isSubscribed) {
                    $subscription_status = 'subscribed';
                    $label = "Inscrit";
                }
            }

            // Forcer "Passé" si l'événement est dans le passé
            $eventDateObj = new DateTime(substr($event['date_evenement'], 0, 10));
            if ($eventDateObj < $current_date) {
                $subscription_status = 'full';
                $label = 'Passé';
            }

            $processed_events[] = [
                'id' => $eventid,
                'name' => $event['nom_evenement'],
                'location' => $event['lieu_evenement'],
                'date' => $event['date_evenement'],
                'subscription_status' => $subscription_status,
                'label' => $label
            ];
        }

        return [
            'events' => $processed_events,
            'show' => $show,
            'closest_event_id' => $closest_event_id,
            'isLoggedIn' => $isLoggedIn
        ];
    }

    public function details()
    {
        $show = 8;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $eventid = $_GET['id'];
            $event = $this->db->select(
                "SELECT nom_evenement, xp_evenement, places_evenement, prix_evenement,
                        reductions_evenement, lieu_evenement, date_evenement,
                        image_evenement, description_evenement
                 FROM EVENEMENT
                 WHERE id_evenement = ?",
                "i",
                [$eventid]
            );
            if (empty($event) || is_null($event)) {
                header("Location: /");
                exit;
            }
            $event = $event[0];

            if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show']) {
                $show = (int) $_GET['show'];
            }

            $isLoggedIn = isset($_SESSION["userid"]);
            $current_date = new DateTime(date("Y-m-d"));
            $event_date = new DateTime(substr($event['date_evenement'], 0, 10));

            $subscription_button = '';
            if ($event_date < $current_date) {
                $subscription_button = '<button class="subscription" id="passed_subscription">Passé</button>';
            } else {
                $a = $this->db->select(
                    "SELECT * FROM INSCRIPTION WHERE id_evenement = ? AND id_membre = ?;",
                    "ii",
                    [$eventid, $_SESSION['userid'] ?? 0]
                );
                $isSubscribed = !empty($a);
                if ($isSubscribed) {
                    $subscription_button = '<button class="subscription" id="passed_subscription">Inscrit</button>';
                } else {
                    $subscription_button = '<form class="subscription" action="/?page=event_subscription" method="post">
                        <input type="hidden" name="eventid" value="' . (int)$eventid . '">
                        <button type="submit">Inscription</button>
                    </form>';
                }
            }

            $isLoggedIn = isset($_SESSION["userid"]);

            $my_medias = [];
            $general_medias = [];
            if ($isLoggedIn) {
                $my_medias = $this->db->select(
                    "SELECT url_media FROM MEDIA
                     WHERE id_membre = ? AND id_evenement = ?
                     ORDER BY date_media ASC
                     LIMIT 4;",
                    "ii",
                    [$_SESSION["userid"], $eventid]
                );
            }

            $general_medias = $this->db->select(
                "SELECT url_media FROM MEDIA
                 WHERE id_evenement = ?
                 ORDER BY date_media ASC
                 LIMIT ?;",
                "ii",
                [$eventid, $show]
            );

            return [
                'event' => $event,
                'subscription_button' => $subscription_button,
                'my_medias' => $my_medias,
                'general_medias' => $general_medias,
                'show' => $show,
                'eventid' => $eventid,
                'isLoggedIn' => $isLoggedIn
            ];
        } else {
            header("Location: /");
            exit;
        }
    }

    public function subscribe()
    {
        if (!isset($_SESSION["userid"])) {
            header("Location: /?page=login");
            exit;
        }

        $userid = $_SESSION["userid"];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventid = $_POST["eventid"];

            // Étape 2 : validation et insertion
            if (isset($_POST["price"], $_POST["eventid"])) {
                $this->db->query(
                    "INSERT INTO INSCRIPTION (id_membre, id_evenement, date_inscription, paiement_inscription, prix_inscription)
                     VALUES (?, ?, NOW(), 'WEB', ?);",
                    "iid",
                    [$userid, $eventid, $_POST["price"]]
                );
                $xp = $this->db->select(
                    "SELECT xp_evenement FROM EVENEMENT WHERE id_evenement = ?",
                    "i",
                    [$eventid]
                )[0]['xp_evenement'];

                $this->db->query(
                    "UPDATE MEMBRE
                     SET MEMBRE.xp_membre = MEMBRE.xp_membre + ?
                     WHERE MEMBRE.id_membre = ?;",
                    "ii",
                    [$xp, $userid]
                );

                // Retour à la liste MVC
                header("Location: /?page=events");
                exit;
            }
            // Étape 1 : calcul du prix (formulaire de confirmation)
            elseif (isset($_POST["eventid"])) {
                $event = $this->db->select(
                    "SELECT nom_evenement, xp_evenement, prix_evenement, reductions_evenement
                     FROM EVENEMENT
                     WHERE id_evenement = ?;",
                    "i",
                    [$eventid]
                );
                if (empty($event)) {
                    header("Location: /");
                    exit;
                }
                $event = $event[0];

                $price = $event["prix_evenement"];

                $isDiscounted = boolval($event["reductions_evenement"]);
                $user_reduction = 1;

                if ($isDiscounted) {
                    $user_reduction_result = $this->db->select(
                        "SELECT reduction_grade
                         FROM ADHESION
                         JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
                         WHERE id_membre = ?
                           AND reduction_grade > 0
                         ORDER BY ADHESION.date_adhesion DESC
                         LIMIT 1",
                        "i",
                        [$userid]
                    );
                    if (!empty($user_reduction_result)) {
                        $user_reduction = 1 - ($user_reduction_result[0]["reduction_grade"] / 100);
                    } else {
                        $user_reduction = 1;
                    }
                }

                return [
                    'event' => $event,
                    'eventid' => $eventid,
                    'price' => $price,
                    'user_reduction' => $user_reduction,
                    'final_price' => $price * $user_reduction
                ];
            }
        }

        header("Location: /");
        exit;
    }

    public function addMedia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?page=events');
            exit();
        }

        if (!isset($_FILES['file'], $_POST['userid'], $_POST['eventid'])) {
            header('Location: /?page=events');
            exit();
        }

        $file = File::saveFile();

        if ($file !== null) {
            $fileName = $file->getFileName();

            $date = new DateTime();
            $sqlDate = $date->format('Y-m-d H:i:s');

            $this->db->query(
                "INSERT INTO MEDIA VALUES (NULL, ?, ?, ?, ?)",
                "ssii",
                [$fileName, $sqlDate, (int)$_POST['userid'], (int)$_POST['eventid']]
            );
        }

        header('Location: /?page=my_gallery&eventid=' . (int)$_POST['eventid']);
        exit();
    }

    public function myGallery(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $isLoggedIn = isset($_SESSION['userid']);
        if (!$isLoggedIn) {
            header('Location: /?page=login');
            exit();
        }

        if (empty($_GET['eventid']) || !ctype_digit($_GET['eventid'])) {
            header('Location: /?page=events');
            exit();
        }

        $eventid = (int)$_GET['eventid'];
        $userid  = (int)$_SESSION['userid'];

        $show = 10;
        if (isset($_GET['show']) && ctype_digit($_GET['show'])) {
            $show = (int)$_GET['show'];
        }

        $event = $this->db->select(
            "SELECT nom_evenement
             FROM EVENEMENT
             WHERE id_evenement = ? AND deleted = 0",
            "i",
            [$eventid]
        );

        if (empty($event)) {
            header('Location: /?page=events');
            exit();
        }

        $medias = $this->db->select(
            "SELECT id_media, url_media
             FROM MEDIA
             WHERE id_membre = ? AND id_evenement = ?
             ORDER BY date_media ASC
             LIMIT ?",
            "iii",
            [$userid, $eventid, $show]
        );

        return [
            'event'     => $event[0],
            'medias'    => $medias,
            'eventid'   => $eventid,
            'userid'    => $userid,
            'show'      => $show,
            'isLoggedIn'=> $isLoggedIn,
        ];
    }

    public function deleteMedia(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?page=events');
            exit();
        }

        if (
            !isset($_POST['mediaid'], $_POST['eventid']) ||
            !ctype_digit((string)$_POST['mediaid']) ||
            !ctype_digit((string)$_POST['eventid'])
        ) {
            header('Location: /?page=events');
            exit();
        }

        $mediaid = (int)$_POST['mediaid'];
        $eventid = (int)$_POST['eventid'];

        $media = $this->db->select(
            "SELECT url_media FROM MEDIA WHERE id_media = ?",
            "i",
            [$mediaid]
        );

        if (!empty($media)) {
            $fileName = $media[0]['url_media'] ?? null;
            if ($fileName) {
                $file = File::getFile($fileName);
                if ($file !== null) {
                    $file->deleteFile();
                }
            }

            $this->db->query(
                "DELETE FROM MEDIA WHERE id_media = ?",
                "i",
                [$mediaid]
            );
        }

        header('Location: /?page=my_gallery&eventid=' . $eventid);
        exit();
    }
}