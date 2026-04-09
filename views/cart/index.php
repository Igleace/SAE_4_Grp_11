<div>
    <h1>MON PANIER</h1>

    <div>
        <?php
            if (isset($_SESSION['message'])) {
                $messageStyle = isset($_SESSION['message_type']) && $_SESSION['message_type'] === "error"
                    ? "error-message"
                    : "success-message";
                echo '<div id="' . $messageStyle . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
        ?>
    </div>

    <div>
        <a id="shop-button" href="/?page=shop">
            <img src="/assets/fleche_retour.png" alt="Fleche de retour">
            Retourner à la boutique
        </a>
    </div>
</div>

<?php if (!empty($data['items'])) : ?>
<div id="cart-container">
    <form method="POST" action="/?page=cart" id="form-quantity">
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['items'] as $item) : ?>
                <tr>
                    <td id="article-cell">
                        <img src="/api/files/<?= htmlspecialchars($item['image']) ?>" alt="Image de l'article" />
                        <p><?= htmlspecialchars($item['name']) ?></p>
                    </td>
                    <td><?= number_format((float)$item['price'], 2, ',', ' ') ?> €</td>
                    <td>
                        <input
                            type="text"
                            name="cart[quantity][<?= $item['id'] ?>]"
                            value="<?= $item['quantity'] ?>"
                            onkeydown="pressEnter(event)"
                        >
                    </td>
                    <td><?= number_format((float)($item['price'] * $item['quantity']), 2, ',', ' ') ?> €</td>
                    <td>
                        <a href="/?page=cart&del=<?= $item['id'] ?>">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Nombre d'articles :</th>
                    <td><?= $data['count'] ?></td>
                </tr>
                <tr>
                    <th>Total :</th>
                    <td><?= number_format((float)$data['total'], 2, ',', ' ') ?> €</td>
                </tr>

                <?php if ($data['hasReduction']) : ?>
                    <tr>
                        <th style="min-width: 400px">Total après réductions :</th>
                        <td style="min-width: 50px"><?= number_format((float)$data['totalWithReduc'], 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </form>
</div>

<div>
    <form class="subscription" action="/?page=order" method="post">
        <?php
        if (isset($_SESSION['cart'])) {
            echo '<input type="hidden" name="cart" value="' .
                htmlspecialchars(json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE)) .
                '">';
        }
        ?>
        <button type="submit" id="order-button">
            Commander
        </button>
    </form>
</div>

<?php else : ?>
    <p id="empty-cart">Votre panier est vide</p>
<?php endif; ?>

<script>
    function pressEnter(event) {
        var code = event.which || event.keyCode;
        if (code == 13) {
            document.getElementById("form-quantity").submit();
        }
    }
</script>