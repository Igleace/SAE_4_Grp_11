<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../model/Member.php';

class HomeController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index()
    {
        $isLoggedIn = isset($_SESSION["userid"]);

        // Get podium
        $podium = $this->db->select(
            "SELECT prenom_membre, xp_membre, pp_membre FROM MEMBRE ORDER BY xp_membre DESC LIMIT 3;"
        );

        // Get upcoming events
        $date = getdate();
        $sql_date = $date["year"]."-".$date["mon"]."-".$date["mday"];
        $events_to_display = $this->db->select(
            "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? ORDER BY date_evenement ASC LIMIT 2;",
            "s",
            [$sql_date]
        );

        // Process events for display
        $events = [];
        foreach ($events_to_display as $event) {
            $eventid = $event["id_evenement"];
            $isPlaceDisponible = $this->db->select(
                "SELECT (EVENEMENT.places_evenement - (SELECT COUNT(*) FROM INSCRIPTION WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement)) > 0 AS isPlaceDisponible FROM EVENEMENT WHERE EVENEMENT.id_evenement = ? ;",
                "i",
                [$eventid])[0]['isPlaceDisponible'];

            $subscription_status = 'not-subscribed';
            $label = "S'inscrire";

            if (!$isPlaceDisponible) {
                $subscription_status = 'full';
                $label = "Complet";
            } elseif ($isLoggedIn) {
                $isSubscribed = !empty($this->db->select(
                    "SELECT MEMBRE.id_membre FROM MEMBRE JOIN INSCRIPTION on MEMBRE.id_membre = INSCRIPTION.id_membre WHERE MEMBRE.id_membre = ? AND INSCRIPTION.id_evenement = ? ;",
                    "ii",
                    [$_SESSION['userid'], $eventid]
                ));
                if ($isSubscribed) {
                    $subscription_status = 'subscribed';
                    $label = "Inscrit";
                }
            }

            $events[] = [
                'id' => $eventid,
                'name' => $event['nom_evenement'],
                'location' => $event['lieu_evenement'],
                'date' => $event['date_evenement'],
                'subscription_status' => $subscription_status,
                'label' => $label
            ];
        }

        return [
            'isLoggedIn' => $isLoggedIn,
            'podium' => $podium,
            'events' => $events
        ];
    }
}
