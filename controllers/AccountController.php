<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Member.php';
require_once __DIR__ . '/../model/File.php';

class AccountController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index()
    {
        $this->requireLogin();
        $userId = $_SESSION['userid'];

        $this->handleLogout();

        $infoUser = $this->getUserInfo($userId);

        $this->handleProfilePictureUpload($userId, $infoUser);
        $this->handlePersonalInfoUpdate($userId);
        $this->handlePasswordUpdate($userId);

        $viewAll = isset($_GET['viewAll']) && $_GET['viewAll'] === '1';
        $historiqueAchats = $this->getPurchaseHistory($userId, $viewAll);

        return [
            'infoUser' => $this->getUserInfo($userId)[0] ?? null,
            'historiqueAchats' => $historiqueAchats,
            'viewAll' => $viewAll,
            'message' => $_SESSION['message'] ?? null,
            'messageType' => $_SESSION['message_type'] ?? null,
        ];
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['userid'])) {
            header('Location: /?page=login');
            exit;
        }
    }

    private function handleLogout()
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['deconnexion'])
            && $_POST['deconnexion'] === 'true'
        ) {
            session_destroy();
            header("Location: /?page=home");
            exit();
        }
    }

    private function getUserInfo($userId)
    {
        return $this->db->select(
            "SELECT pp_membre, xp_membre, prenom_membre, nom_membre, email_membre, tp_membre, discord_token_membre, nom_grade, image_grade
             FROM MEMBRE
             LEFT JOIN ADHESION ON MEMBRE.id_membre = ADHESION.id_membre
             LEFT JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
             WHERE MEMBRE.id_membre = ?",
            "i",
            [$userId]
        );
    }

    private function handleProfilePictureUpload($userId, $infoUser)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            require_once __DIR__ . '/../model/File.php';

            $fileName = saveImage();

            if ($fileName !== null) {
                if (!empty($infoUser[0]['pp_membre'])) {
                    deleteFile($infoUser[0]['pp_membre']);
                }

                $this->db->query(
                    "UPDATE MEMBRE SET pp_membre = ? WHERE id_membre = ?",
                    "si",
                    [$fileName, $userId]
                );

                $_SESSION['message'] = "Mise à jour de la photo de profil réussie !";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur : veuillez vérifier le fichier envoyé.";
                $_SESSION['message_type'] = "error";
            }

            header("Location: /?page=account");
            exit;
        }
    }

    private function handlePersonalInfoUpdate($userId)
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['name'], $_POST['lastName'], $_POST['mail'])
            && !isset($_POST['mdp'])
        ) {

            $currentUserData = $this->db->select(
                "SELECT prenom_membre, nom_membre, email_membre, tp_membre FROM MEMBRE WHERE id_membre = ?",
                "i",
                [$userId]
            );

            if (!empty($currentUserData)) {
                $currentName = $currentUserData[0]['prenom_membre'];
                $currentLastName = $currentUserData[0]['nom_membre'];
                $currentMail = $currentUserData[0]['email_membre'];
                $currentTp = $currentUserData[0]['tp_membre'];

                $name = empty($_POST['name']) ? $currentName : htmlspecialchars($_POST['name']);
                $lastName = empty($_POST['lastName']) ? $currentLastName : htmlspecialchars($_POST['lastName']);
                $mail = empty($_POST['mail']) ? $currentMail : htmlspecialchars($_POST['mail']);
                $tp = isset($_POST['tp']) && !empty($_POST['tp']) ? htmlspecialchars($_POST['tp']) : $currentTp;

                $existingEmail = $this->db->select(
                    "SELECT id_membre FROM MEMBRE WHERE email_membre = ? AND id_membre != ?",
                    "si",
                    [$mail, $userId]
                );

                if (!empty($existingEmail)) {
                    $_SESSION['message'] = "Les modifications n'ont pas pu être effectuées car l'adresse e-mail est déjà utilisée par un autre compte.";
                    $_SESSION['message_type'] = "error";
                } else {
                    $this->db->query(
                        "UPDATE MEMBRE SET prenom_membre = ?, nom_membre = ?, email_membre = ?, tp_membre = ? WHERE id_membre = ?",
                        "ssssi",
                        [$name, $lastName, $mail, $tp, $userId]
                    );

                    $_SESSION['message'] = "Vos informations ont été mises à jour avec succès !";
                    $_SESSION['message_type'] = "success";
                }
            } else {
                $_SESSION['message'] = "Erreur : utilisateur introuvable dans la base de données.";
                $_SESSION['message_type'] = "error";
            }

            header("Location: /?page=account");
            exit();
        }
    }

    private function handlePasswordUpdate($userId)
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['mdp'], $_POST['newMdp'], $_POST['newMdpVerif'])
        ) {

            $currentPassword = htmlspecialchars(trim($_POST['mdp']));
            $newPassword = htmlspecialchars(trim($_POST['newMdp']));
            $newPasswordVerif = htmlspecialchars(trim($_POST['newMdpVerif']));

            $user = $this->db->select(
                "SELECT password_membre FROM MEMBRE WHERE id_membre = ?",
                "i",
                [$userId]
            );

            if (!empty($user)) {
                if ($user[0]['password_membre'] == null && $currentPassword == "") {
                    $password_ok = true;
                } else {
                    $password_ok = password_verify($currentPassword, $user[0]['password_membre']);
                }

                if ($password_ok && $newPassword == $newPasswordVerif) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $this->db->query(
                        "UPDATE MEMBRE SET password_membre = ? WHERE id_membre = ?",
                        "si",
                        [$hashedPassword, $userId]
                    );

                    $_SESSION['message'] = "Mot de passe mis à jour avec succès !";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Les nouveaux mots de passe ne correspondent pas.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['message'] = "Mot de passe actuel incorrect.";
                $_SESSION['message_type'] = "error";
            }

            header("Location: /?page=account");
            exit();
        }
    }

    private function getPurchaseHistory(int $userId, bool $viewAll): array
    {
        $sql = "
            SELECT type_transaction, element, quantite, montant, mode_paiement, date_transaction, 
            CASE 
            WHEN recupere = 1 THEN 'Récupéré'
            ELSE 'Non récupéré'
            END AS statut 
            FROM HISTORIQUE WHERE id_membre=? ORDER BY date_transaction DESC";

        if (!$viewAll) {
            $sql .= " LIMIT 6";
        }

        return $this->db->select($sql, 'i', [$userId]);
    }
}