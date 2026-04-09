<div class="login-form">
    <h1>S'inscrire</h1>

    <?php if (!empty($data['error'])): ?>
        <h3 class="login-error"><?= htmlspecialchars($data['error']) ?></h3>
    <?php endif; ?>

    <form method="POST" action="/?page=signin">
        <label for="fname">Prénom :</label>
        <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($data['fname'] ?? '') ?>">

        <label for="lname">Nom :</label>
        <input type="text" id="lname" name="lname" value="<?= htmlspecialchars($data['lname'] ?? '') ?>">

        <label for="mail">Adresse Mail :*</label>
        <input type="email" id="mail" name="mail" required value="<?= htmlspecialchars($data['mail'] ?? '') ?>">

        <label for="password">Mot de passe :*</label>
        <input type="password" id="password" name="password" required>

        <label for="password_verif">Confirmez le Mot de passe :*</label>
        <input type="password" id="password_verif" name="password_verif" required>

        <button type="submit">Confirmer</button>
    </form>
</div>

<div id="create-account">
    <h2>Déjà un compte ?</h2>
    <button type="button" onclick="window.location.href='/?page=signin'">Connectez vous</button>
</div>