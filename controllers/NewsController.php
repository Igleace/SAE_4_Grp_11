<?php

require_once __DIR__ . '/../model/database.php';

class NewsController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index(): array
    {
        $show = isset($_GET['show']) && is_numeric($_GET['show']) ? (int)$_GET['show'] : 5;

        $date = getdate();
        $sql_date = $date["year"] . "-" . $date["mon"] . "-" . $date["mday"];

        $events_to_display = $this->db->select(
            "SELECT id_actualite, titre_actualite, date_actualite
             FROM ACTUALITE
             WHERE date_actualite <= NOW()
             ORDER BY date_actualite ASC
             LIMIT ?;",
            "i",
            [$show]
        );

        return [
            'news' => $events_to_display,
            'show' => $show,
        ];
    }

    public function details(): array
{
    if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
        header('Location: /?page=news');
        exit();
    }

    $newsId = (int) $_GET['id'];

    $news = $this->db->select(
        "SELECT id_actualite, titre_actualite, date_actualite, image_actualite, contenu_actualite
         FROM ACTUALITE
         WHERE id_actualite = ?",
        "i",
        [$newsId]
    );

    if (empty($news)) {
        header('Location: /?page=news');
        exit();
    }

    return [
        'news' => $news[0],
        'newsid' => $newsId,
        'isLoggedIn' => isset($_SESSION['userid'])
    ];
}
}