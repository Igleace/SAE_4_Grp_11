<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Cart.php';

class CartController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index()
    {
        $cart = new Cart($this->db);
        $items = $cart->getItems();
        $total = $cart->total();
        $count = $cart->count();

        $totalWithReduc = $total;
        $hasReduction = false;
        $isLoggedIn = isset($_SESSION['userid']);

        if ($isLoggedIn) {
            // Vérifie l'adhésion de l'utilisateur
            $adherant = $this->db->select(
                "SELECT * FROM ADHESION 
                INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade 
                WHERE ADHESION.id_membre = ? AND reduction_grade > 0",
                "i",
                [$_SESSION['userid']]
            );

            //récupérer la réduction liée au grade
            if (!empty($adherant)) {
                $reductionGrade = floatval($adherant[0]["reduction_grade"] ?? 0);
                $user_reduction = 1 - ($reductionGrade / 100);
                $totalWithReduc = 0;
                $hasReduction = true;

                // Calcule le total en tenant compte des réductions applicables
                foreach ($items as $item) {
                    $product = $this->db->select("SELECT reduction_article FROM ARTICLE WHERE id_article = ?", "i", [$item['id']])[0];
                    if (!empty($product['reduction_article'])) { // Vérifie si une réduction est applicable
                        $totalWithReduc += $item['price'] * $item['quantity'] * $user_reduction;
                    } else {
                        $totalWithReduc += $item['price'] * $item['quantity'];
                    }
                }
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'totalWithReduc' => $totalWithReduc,
            'hasReduction' => $hasReduction,
            'count' => $count,
            'isLoggedIn' => $isLoggedIn
        ];
    }

    public function add()
    {
        $cart = new Cart($this->db);

        if (isset($_GET['id'])) {
            $product = $this->db->select(
                "SELECT id_article FROM ARTICLE WHERE id_article = ?",
                "i",
                [$_GET['id']]
            );

            if (!empty($product)) {
                $cart->add($product[0]['id_article']);
            }
        }

        header('Location: /?page=shop');
        exit;
    }
}