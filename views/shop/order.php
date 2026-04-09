<h1>COMMANDER</h1>

<div>
    <a id="cart-button" href="/?page=cart">
        <img src="/assets/fleche_retour.png" alt="Flèche de retour">
        Retourner au panier
    </a>
</div>

<div>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['cart_items'] as $product_id => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nom_article']) ?></td>
                        <td><?= htmlspecialchars($item['quantite']) ?></td>
                        <td><?= number_format($item['prix_article'], 2, ',', ' ') ?> €</td>
                        <td><?= number_format($item['prix_article'] * $item['quantite'], 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total&nbsp;:&nbsp;<?= number_format($data['total'], 2, ',', ' ') ?> €</h3>

        <?php if (!empty($data['hasReduction']) && $data['totalWithReduc'] !== null): ?>
            <h3>Total après réductions&nbsp;:&nbsp;<?= number_format($data['totalWithReduc'], 2, ',', ' ') ?> €</h3>
        <?php endif; ?>
    </div>

    <div>
        <h3>Paiement</h3>

        <form method="POST" action="/?page=order&action=submit">
            <label for="mode_paiement">Mode de Paiement :</label>
            <select id="mode_paiement" name="mode_paiement" required>
                <option value="carte_credit">Carte de Crédit</option>
                <option value="paypal">PayPal</option>
            </select><br><br>

            <div id="carte_credit" class="mode_paiement_fields">
                <label for="numero_carte">Numéro de Carte :</label>
                <input type="text" id="numero_carte" name="numero_carte" placeholder="XXXX XXXX XXXX XXXX"
                    value="4242 4242 4242 4242" autocomplete="cc-number" required><br><br>

                <label for="expiration">Date d'Expiration :</label>
                <input type="text" id="expiration" name="expiration" placeholder="MM/AA" value="12/30"
                    autocomplete="cc-exp" required><br><br>

                <label for="cvv">CVV :</label>
                <input type="text" id="cvv" name="cvv" placeholder="XXX" value="123" autocomplete="cc-csc"
                    required><br><br>

                <button type="submit" id="finalise-order-button">Valider la commande</button>
            </div>
        </form>
    </div>
</div>