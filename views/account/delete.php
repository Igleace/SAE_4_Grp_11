<h1>Supprimer le compte</h1>

<?php if (isset($data['show_confirmation']) && $data['show_confirmation']): ?>
    <div id="deleteAccountAlert" class="alert-container">
        <div class="alert-content">
            <p>
                ⚠️ Vous êtes sur le point de supprimer votre compte. Cette action est irréversible.
                Toutes vos données seront perdues. Veuillez cocher la case ci-dessous pour confirmer que vous comprenez les conséquences.
            </p>
            <input type="checkbox" id="confirmCheckbox"> <label for="confirmCheckbox">J'ai compris</label>
            <br><br>
            <ul>
                <li>
                    <form action="/delete_account.php" method="POST">
                        <button id="confirmDelete" name="delete_account_valid" value="true" disabled>Valider</button>
                    </form>
                </li>
                <li>
                    <button id="cancelDelete" onclick="window.location.href='/account.php'">Revenir en arrière</button>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <form action="/delete_account.php" method="POST">
        <input type="hidden" name="delete_account" value="true">
        <button type="submit">Supprimer mon compte</button>
    </form>
<?php endif; ?>