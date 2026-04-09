<h2>MON COMPTE</h2>

<?php if (!empty($data['message'])): ?>
    <?php $messageStyle = ($data['messageType'] === "error") ? "error-message" : "success-message"; ?>
    <div id="<?= $messageStyle ?>"><?= htmlspecialchars($data['message']) ?></div>
<?php endif; ?>

<section>
    <div id="account-generalInfo">
        <div>
            <form method="POST" enctype="multipart/form-data" id="pp-form">
                <label id="cadre-pp" for="profilePictureInput">
                    <?php if (empty($data['infoUser']['pp_membre'])): ?>
                        <img src="/admin/ressources/default_images/user.jpg" alt="Photo de profil de l'utilisateur">
                    <?php else: ?>
                        <img src="/api/files/<?= htmlspecialchars($data['infoUser']['pp_membre']) ?>" alt="Photo de profil de l'utilisateur">
                    <?php endif; ?>
                </label>

                <input type="file" id="profilePictureInput" name="file" accept="image/jpeg, image/png, image/webp" style="display:none;" onchange="this.form.submit()">

                <button type="button" id="edit-icon" onclick="document.getElementById('profilePictureInput').click()">
                    <img src="/assets/edit_logo.png" alt="Icone éditer la photo de profil">
                </button>
            </form>
        </div>

        <div>
            <p><?= htmlspecialchars($data['infoUser']['xp_membre']) ?></p>
            <p>XP</p>
        </div>

        <div id="cadre-grade">
            <?php if (empty($data['infoUser']['nom_grade'])): ?>
                <p>Vous n'avez pas de grade</p>
            <?php else: ?>
                <p><?= htmlspecialchars($data['infoUser']['nom_grade']) ?></p>
                <?php if (empty($data['infoUser']['image_grade'])): ?>
                    <img src="/admin/ressources/default_images/grade.webp" alt="Image du grade">
                <?php else: ?>
                    <img src="/api/files/<?= htmlspecialchars($data['infoUser']['image_grade']) ?>" alt="Illustration du grade de l'utilisateur">
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" action="" id="account-personalInfo-form">
        <div>
            <div>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Prénom"
                    value="<?= htmlspecialchars($data['infoUser']['prenom_membre']) ?>"
                    required>
                <input
                    type="text"
                    id="lastName"
                    name="lastName"
                    placeholder="Nom de famille"
                    value="<?= htmlspecialchars($data['infoUser']['nom_membre']) ?>"
                    required>
            </div>
            <div>
                <input
                    type="email"
                    id="mail"
                    name="mail"
                    placeholder="Adresse mail"
                    value="<?= htmlspecialchars($data['infoUser']['email_membre']) ?>"
                    required>

                <?php if (!empty($data['infoUser']['tp_membre'])): ?>
                    <select id="tp" name="tp">
                        <?php
                        $tp = $data['infoUser']['tp_membre'];
                        $options = ['11A','11B','12C','12D','21A','21B','22C','22D','31A','31B','32C','32D'];
                        foreach ($options as $opt): ?>
                            <option value="<?= $opt ?>" <?= $tp === $opt ? 'selected' : '' ?>>TP <?= substr($opt,0,2) . ' ' . substr($opt,2,1) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit">
            <img src="/assets/save_logo.png" alt="Logo enregistrer les modifications">
        </button>
    </form>

    <form method="POST" action="" id="account-editPass-form">
        <div>
            <div>
                <p>Modifier mon mot de passe :</p>
                <input type="password" id="mdp" name="mdp" placeholder="Mot de passe actuel">
            </div>
            <div>
                <input type="password" id="newMdp" name="newMdp" placeholder="Nouveau mot de passe" required>
                <input type="password" id="newMdpVerif" name="newMdpVerif" placeholder="Confirmation du nouveau mot de passe" required>
            </div>
        </div>

        <button type="submit">
            <img src="/assets/save_logo.png" alt="Logo editer le mot de passe">
        </button>
    </form>
</section>

<section>
    <div id="buttons-section">
        <button type="button">
            <a href="https://discord.com/login" target="_blank">
                <img src="/assets/logo_discord.png" alt="Logo de Discord">
                Associer mon compte à Discord
            </a>
        </button>

        <form action="" method="post">
            <input type="hidden" name="deconnexion" value="true">
            <button type="submit">
                <img src="/assets/logOut_icon.png" alt="icone de deconnexion">
                Déconnexion
            </button>
        </form>

        <form action="delete_account.php" method="post">
            <input type="hidden" name="delete_account" value="true">
            <button type="submit">
                <img src="/assets/delete_icon.png" alt="icone de suppression">
                Supprimer mon compte
            </button>
        </form>
    </div>
</section>

<section id="section-mesAchats">
    <h2>MES ACHATS</h2>

    <div id="historique-achats">
        <form method="GET" action="#section-mesAchats" id="viewAll-form">
            <?php if ($data['viewAll']): ?>
                <button type="submit" name="viewAll" value="0">Afficher moins</button>
            <?php else: ?>
                <button type="submit" name="viewAll" value="1">Afficher tout</button>
            <?php endif; ?>
        </form>

        <?php if (!empty($data['historiqueAchats'])): ?>
            <table id="tab-historique-achats">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Mode de paiement</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['historiqueAchats'] as $achat): ?>
                        <tr>
                            <td><?= htmlspecialchars($achat['date_transaction']) ?></td>
                            <td><?= htmlspecialchars($achat['type_transaction']) ?></td>
                            <td><?= htmlspecialchars($achat['element']) ?></td>
                            <td><?= htmlspecialchars($achat['quantite']) ?></td>
                            <td><?= htmlspecialchars(number_format($achat['montant'], 2, ',', ' ')) ?> €</td>
                            <td><?= htmlspecialchars($achat['mode_paiement']) ?></td>
                            <td><?= htmlspecialchars($achat['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez effectué aucun achat pour le moment.</p>
        <?php endif; ?>
    </div>
</section>