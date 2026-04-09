<h1>MON ADHÉSION</h1>

<div>
    <a id="cart-button" href="/?page=grade">
        <img src="/assets/fleche_retour.png" alt="Flèche de retour">
        Retourner aux grades
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
                    <td>Grade <?= htmlspecialchars($data['grade']['nom_grade']); ?></td>
                    <td>1</td>
                    <td><?= number_format((float)$data['grade']['prix_grade'], 2, ',', ' ') ?> €</td>
                    <td><?= number_format((float)$data['grade']['prix_grade'], 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>

        <h3>Total&nbsp;:&nbsp;<?= number_format((float)$data['grade']['prix_grade'], 2, ',', ' ') ?> €</h3>
    </div>

    <div>    
        <h3>Paiement</h3>

        <form method="POST" action="/?page=grade_subscribe&id=<?= htmlspecialchars($data['grade']['id_grade']) ?>">
            <label for="mode_paiement">Mode de Paiement :</label>
            <select id="mode_paiement" name="mode_paiement" required>
                <option value="carte_credit">Carte de Crédit</option>
                <option value="paypal">PayPal</option>
            </select><br><br>

            <div id="carte_credit" class="mode_paiement_fields">
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

                <button type="submit" id="finalise-order-button">Valider l'adhésion</button>
            </div>
        </form>
    </div>
</div>