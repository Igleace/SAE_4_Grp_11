<?php

require_once __DIR__ . '/../model/database.php';

class GradeController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();

        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function index()
    {
        $grades = $this->db->select(
            "SELECT * FROM GRADE WHERE deleted = false ORDER BY prix_grade"
        );

        $ownedGradeIds = [];

        if (!empty($_SESSION['userid'])) {
            $ownedRows = $this->db->select(
                "SELECT id_grade FROM ADHESION WHERE id_membre = ?",
                "i",
                [$_SESSION['userid']]
            );

            $ownedGradeIds = array_column($ownedRows, 'id_grade');
        }

        foreach ($grades as &$grade) {
            $grade['owned'] = in_array($grade['id_grade'], $ownedGradeIds, true);
        }
        unset($grade);

        return [
            'grades' => $grades
        ];
    }

    public function subscribe()
    {
        if (empty($_SESSION['userid'])) {
            header('Location: /?page=login');
            exit;
        }

        $userid = $_SESSION['userid'];

        if (empty($_GET['id'])) {
            header('Location: /?page=grade');
            exit;
        }

        $id_grade = (int) $_GET['id'];

        $grade = $this->db->select(
            "SELECT * FROM GRADE WHERE id_grade = ? AND deleted = false",
            "i",
            [$id_grade]
        );

        if (empty($grade)) {
            header('Location: /?page=grade');
            exit;
        }

        $alreadyOwned = $this->db->select(
            "SELECT id_grade FROM ADHESION WHERE id_membre = ? AND id_grade = ?",
            "ii",
            [$userid, $id_grade]
        );

        if (!empty($alreadyOwned)) {
            $_SESSION['message'] = "Vous possédez déjà ce grade.";
            $_SESSION['message_type'] = "error";
            header('Location: /?page=grade');
            exit;
        }

        $grade = $grade[0];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mode_paiement'])) {
            $mode_paiement = $_POST['mode_paiement'];

            $this->db->query(
                "INSERT INTO ADHESION (id_membre, id_grade, date_adhesion, paiement_adhesion, prix_adhesion)
                 VALUES (?, ?, NOW(), ?, ?)",
                "iisd",
                [$userid, $id_grade, $mode_paiement, $grade['prix_grade']]
            );

            $_SESSION['message'] = "Grade acheté avec succès.";
            $_SESSION['message_type'] = "success";

            header('Location: /?page=account');
            exit;
        }

        return [
            'grade' => $grade
        ];
    }
}