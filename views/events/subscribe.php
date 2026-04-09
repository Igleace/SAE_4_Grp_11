<h1>INSCRIPTION</h1>

<div>
    <a id="cart-button" href="/?page=event_details&id=<?= htmlspecialchars($data['eventid']) ?>">
        <img src="/assets/fleche_retour.png" alt="Flèche de retour">
        Retourner à l'évènement
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
                <tr>
                    <td><?= strtoupper(htmlspecialchars($data['event']['nom_evenement'])) ?></td>
                    <td>1</td>
                    <td><?= number_format((float)$data['price'], 2, ',', ' ') ?> €</td>
                    <td><?= number_format((float)$data['price'], 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>

        <h3>Total&nbsp;:&nbsp;<?= number_format((float)$data['price'], 2, ',', ' ') ?> €</h3>
        <?php if ($data['user_reduction'] !== 1): ?>
            <h3>Total après réductions&nbsp;:&nbsp;
                <?= number_format((float)$data['final_price'], 2, ',', ' ') ?> €
            </h3>
        <?php endif; ?>
    </div>

    <div>
        <h3>Paiement</h3>

        <label for="mode_paiement">Mode de Paiement :</label>
        <select id="mode_paiement" name="mode_paiement" required>
            <option value="carte_credit">Carte de Crédit</option>
            <option value="paypal">PayPal</option>
        </select><br><br>

        <div id="carte_credit" class="mode_paiement_fields">
            <form method="POST" action="/?page=event_subscription">
                <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
                <input type="hidden" name="price" value="<?= htmlspecialchars($data['final_price']) ?>">
                <input type="hidden" name="mode_paiement" value="carte_credit">

                <label for="numero_carte">Numéro de Carte :</label>
                <input
                    type="text"
                    id="numero_carte"
                    name="numero_carte"
                    placeholder="XXXX XXXX XXXX XXXX"
                    value="4242 4242 4242 4242"
                    autocomplete="cc-number"
                    required
                ><br><br>

                <label for="expiration">Date d'Expiration :</label>
                <input
                    type="text"
                    id="expiration"
                    name="expiration"
                    placeholder="MM/AA"
                    value="12/30"
                    autocomplete="cc-exp"
                    required
                ><br><br>

                <label for="cvv">CVV :</label>
                <input
                    type="text"
                    id="cvv"
                    name="cvv"
                    placeholder="XXX"
                    value="123"
                    autocomplete="cc-csc"
                    required
                ><br><br>

                <button type="submit" id="finalise-order-button">Valider la commande</button>
            </form>
        </div>

        <div id="paypal" class="mode_paiement_fields" style="display:none;">
            <form method="POST" action="/?page=event_subscribe">
                <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
                <input type="hidden" name="price" value="<?= htmlspecialchars($data['final_price']) ?>">
                <input type="hidden" name="mode_paiement" value="paypal">

                <button type="button" id="paypal-button">Se connecter à PayPal</button><br><br>

                <button type="submit" id="finalise-order-button">Valider la commande</button>
            </form>
        </div>
    </div>
</div>