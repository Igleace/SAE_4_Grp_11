<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Cart.php';

class OrderController
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
        if (empty($_SESSION['userid'])) {
            header('Location: /?page=login');
            exit;
        }

        $userid = $_SESSION['userid'];

        if (empty($_SESSION['cart'])) {
            header('Location: /?page=cart');
            exit;
        }

        $cart = $_SESSION['cart'];
        $productIds = array_keys($cart);

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
        $types = str_repeat('i', count($productIds));
        $products = $this->db->select($query, $types, $productIds);

        $cartItems = [];
        $total = 0;

        foreach ($products as $product) {
            if ($product['stock_article'] > 0 && $cart[$product['id_article']] > $product['stock_article']) {
                $cart[$product['id_article']] = $product['stock_article'];
            }
            $cartItems[$product['id_article']] = [
                'nom_article'  => $product['nom_article'],
                'prix_article' => $product['prix_article'],
                'quantite'     => $cart[$product['id_article']],
                'reduction_article' => $product['reduction_article'] ?? 0,
            ];
            $total += $product['prix_article'] * $cart[$product['id_article']];
        }

        $totalWithReduc = null;
        $hasReduction = false;

        $adherant = $this->db->select(
            "SELECT * FROM ADHESION 
             INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade 
             WHERE ADHESION.id_membre = ? AND reduction_grade > 0",
            "i",
            [$userid]
        );

        if (!empty($adherant)) {
            $reductionGrade = floatval($adherant[0]['reduction_grade'] ?? 0);
            $userReduction = 1 - ($reductionGrade / 100);
            $totalWithReduc = 0;
            $hasReduction = true;

            foreach ($products as $product) {
                $qte = $cart[$product['id_article']];
                if (!empty($product['reduction_article'])) {
                    $totalWithReduc += $product['prix_article'] * $qte * $userReduction;
                } else {
                    $totalWithReduc += $product['prix_article'] * $qte;
                }
            }
        }

        return [
            'cart_items'    => $cartItems,
            'total'         => $total,
            'hasReduction'  => $hasReduction,
            'totalWithReduc'=> $totalWithReduc,
        ];
    }

    public function submit()
    {
        if (empty($_SESSION['userid'])) {
            header('Location: /?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?page=order');
            exit;
        }

        $userid = $_SESSION['userid'];

        if (empty($_SESSION['cart'])) {
            header('Location: /?page=cart');
            exit;
        }

        if (empty($_POST['mode_paiement'])) {
            $_SESSION['message'] = "Veuillez choisir un mode de paiement.";
            $_SESSION['message_type'] = "error";
            header('Location: /?page=order');
            exit;
        }

        $modePaiement = $_POST['mode_paiement'];

        $cart = $_SESSION['cart'];
        $productIds = array_keys($cart);

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
        $types = str_repeat('i', count($productIds));
        $products = $this->db->select($query, $types, $productIds);

        $cartItems = [];
        foreach ($products as $product) {
            if ($product['stock_article'] > 0 && $cart[$product['id_article']] > $product['stock_article']) {
                $cart[$product['id_article']] = $product['stock_article'];
            }
            $cartItems[$product['id_article']] = [
                'prix_article' => $product['prix_article'],
                'quantite'     => $cart[$product['id_article']],
            ];
        }

        foreach ($cartItems as $productId => $item) {
            $this->db->query(
                "CALL achat_article(?, ?, ?, ?)",
                "iiis",
                [$userid, $productId, $item['quantite'], $modePaiement]
            );
        }

        $_SESSION['cart'] = [];
        $_SESSION['message'] = "Commande réalisée avec succès !";
        $_SESSION['message_type'] = "success";

        header('Location: /?page=cart');
        exit;
    }
}