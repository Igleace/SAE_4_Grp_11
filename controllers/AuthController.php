<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Member.php';

class AuthController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = htmlspecialchars(trim($_POST['mail'] ?? ''));
            $password = htmlspecialchars(trim($_POST['password'] ?? ''));

            $selection_db = $this->db->select(
                "SELECT id_membre, email_membre, password_membre FROM MEMBRE WHERE email_membre = ?",
                "s",
                [$mail]
            );

            if (!empty($selection_db)) {
                $db_mail = $selection_db[0]["email_membre"];
                $db_password = $selection_db[0]["password_membre"];

                $mail_ok = ($db_mail == $mail);

                if ($db_password == null && $password == "") {
                    $password_ok = true;
                } else {
                    $password_ok = password_verify($password, $db_password);
                }

                if ($mail_ok && $password_ok) {
                    $_SESSION['userid'] = $selection_db[0]["id_membre"];

                    $adminCheck = $this->db->select(
                        "SELECT COUNT(*) as nb_roles FROM ASSIGNATION WHERE id_membre = ?",
                        "i",
                        [$selection_db[0]["id_membre"]]
                    );

                    if (!empty($adminCheck) && $adminCheck[0]["nb_roles"] > 0) {
                        $_SESSION["isAdmin"] = true;
                    }

                    header("Location: /?page=home");
                    exit;
                } else {
                    $error = "Erreur dans les informations de connexion.";
                }
            } else {
                $error = "Erreur dans les informations de connexion.";
            }
        }

        return ['error' => $error];
    }

    public function signin()
    {
        $error = null;
        $mail = '';
        $fname = '';
        $lname = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = htmlspecialchars(trim($_POST['mail'] ?? ''));
            $password = htmlspecialchars(trim($_POST['password'] ?? ''));
            $password_verif = htmlspecialchars(trim($_POST['password_verif'] ?? ''));
            $fname = htmlspecialchars(trim($_POST['fname'] ?? ''));
            $lname = htmlspecialchars(trim($_POST['lname'] ?? ''));

            // Vérifier si l'utilisateur existe déjà
            $selection_db = $this->db->select(
                "SELECT id_membre FROM MEMBRE WHERE email_membre = ?",
                "s",
                [$mail]
            );

            if (!empty($selection_db)) {
                $error = "Utilisateur déjà présent";
            } else {
                if ($password !== $password_verif) {
                    $error = "Les mots de passe ne correspondent pas.";
                } else {
                    if ($fname === '') {
                        $fname = 'N/A';
                    }
                    if ($lname === '') {
                        $lname = 'N/A';
                    }

                    $this->db->query(
                        "CALL creationCompte ( ? , ? , ? , ? , ? );",
                        "sssss",
                        [
                            $lname,
                            $fname,
                            $mail,
                            password_hash($password, PASSWORD_DEFAULT),
                            'defaultPP.png'
                        ]
                    );

                    header("Location: /?page=login");
                    exit;
                }
            }
        }

        return [
            'error' => $error,
            'mail' => $mail,
            'fname' => $fname,
            'lname' => $lname
        ];
    }

    public function logout()
    {
        session_destroy();
        header('Location: /?page=home');
        exit;
    }
}
