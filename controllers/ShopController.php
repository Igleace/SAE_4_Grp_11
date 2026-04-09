<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/Cart.php';

class ShopController
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function index()
    {
        $isLoggedIn = isset($_SESSION["userid"]);

        $cart = new Cart($this->db);

        $filters = [];
        $orderBy = "name_asc";
        $searchTerm = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['reset'])) {
                $filters = [];
                $orderBy = "name_asc";
                $searchTerm = "";
            } else {
                if (isset($_POST['category']) && is_array($_POST['category'])) {
                    $filters = $_POST['category'];
                }

                if (isset($_POST['sort'])) {
                    $orderBy = $_POST['sort'];
                }

                if (!empty($_POST['search'])) {
                    $searchTerm = $_POST['search'];
                }
            }
        }

        $query = "SELECT * FROM ARTICLE";
        $whereClauses = ["deleted = false"];
        $params = [];
        $types = "";

        if (!empty($searchTerm)) {
            $whereClauses[] = "nom_article LIKE ?";
            $params[] = '%' . $searchTerm . '%';
            $types .= "s";
        }

        $hasCategorieColumn = false;
        $columns = $this->db->select("SHOW COLUMNS FROM ARTICLE LIKE 'categorie_article'");
        if (!empty($columns)) {
            $hasCategorieColumn = true;
        }

        if ($hasCategorieColumn && !empty($filters)) {
            $placeholders = implode(", ", array_fill(0, count($filters), "?"));
            $whereClauses[] = "categorie_article IN ($placeholders)";
            $params = array_merge($params, $filters);
            $types .= str_repeat("s", count($filters));
        } else {
            $filters = [];
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        if ($orderBy === "price_asc") {
            $query .= " ORDER BY prix_article ASC";
        } elseif ($orderBy === "price_desc") {
            $query .= " ORDER BY prix_article DESC";
        } elseif ($orderBy === "name_asc") {
            $query .= " ORDER BY nom_article ASC";
        } elseif ($orderBy === "name_desc") {
            $query .= " ORDER BY nom_article DESC";
        }

        $products = $this->db->select($query, $types, $params);

        $categories = [];
        if ($hasCategorieColumn) {
            $categoriesResult = $this->db->select("SELECT DISTINCT categorie_article FROM ARTICLE WHERE deleted = false");
            $categories = array_column($categoriesResult, 'categorie_article');
        }

        return [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'sort' => $orderBy,
            'searchTerm' => $searchTerm,
            'cartCount' => $cart->count(),
            'isLoggedIn' => $isLoggedIn
        ];
    }
}