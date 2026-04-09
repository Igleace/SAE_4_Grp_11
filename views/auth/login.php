<form method="POST" action="/?page=login" class="login-form">
    <h1>Connexion</h1>

    <label for="mail">Adresse Mail :</label>
    <input type="email" name="mail" required>

    <label for="password">Mot de passe :</label>
    <input type="password" name="password">

    <button type="submit">Se connecter</button>
</form>

<div id="create-account">
    <h2>Pas encore de compte ?</h2>
    <button type="button" onclick="window.location.href='/?page=signin'">Créez en un</button>
</div>

<?php if (!empty($data['error'])): ?>
    <h3 class="login-error"><?= htmlspecialchars($data['error']) ?></h3>
<?php endif; ?>