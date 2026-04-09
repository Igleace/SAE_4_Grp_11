<section class="event-details">
    <?php if (empty($data['event']['image_evenement'])): ?>
        <img src="/admin/ressources/default_images/event.jpg" alt="Image de l'événement">
    <?php else: ?>
        <img src="/api/files/<?= htmlspecialchars($data['event']['image_evenement']) ?>" alt="Image de l'événement">
    <?php endif; ?>

    <h1><?= strtoupper(htmlspecialchars($data['event']['nom_evenement'])) ?></h1>

    <div>
        <h2>
            <?php
            $current_date = new DateTime(date('Y-m-d'));
            $event_date = new DateTime(substr($data['event']['date_evenement'], 0, 10));
            echo date('d/m/Y', strtotime($data['event']['date_evenement']));
            ?>
        </h2>

        <?= $data['subscription_button'] ?>
    </div>

    <ul>
        <li>
            <div>📍<h3><?= htmlspecialchars($data['event']['lieu_evenement']) ?></h3>
            </div>
        </li>
        <li>
            <div>💸<h3><?= number_format((float) $data['event']['prix_evenement'], 2, ',', ' ') ?> € par personne</h3>
            </div>
        </li>
        <?php if (boolval($data['event']['reductions_evenement'])): ?>
            <li>
                <div>💎<h3>Réductions disponibles pour certains grades</h3>
                </div>
            </li>
        <?php endif; ?>
    </ul>

    <p>
        <?= htmlspecialchars($data['event']['description_evenement'] ?? 'Aucune description disponible.') ?>
    </p>
</section>

<section class="gallery">
    <h2>GALLERIE</h2>

    <?php if (!empty($data['isLoggedIn'])): ?>
        <h3>Mes photos</h3>
        <div class="my-medias">
            <?php if (!empty($data['my_medias'])): ?>
                <?php foreach ($data['my_medias'] as $img): ?>
                    <img src="/assets/uploads/events/<?= htmlspecialchars(trim($img['url_media'])) ?>"
                        alt="Image personnelle de l'événement">
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune photo personnelle pour cet événement.</p>
            <?php endif; ?>

            <!-- Upload de média : on pointe vers le contrôleur -->
            <form id="add-media" action="/?page=add_media" method="post" enctype="multipart/form-data">
                <label for="file-picker">
                    <img src="/assets/add_media.png" alt="Ajouter un média">
                </label>
                <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
                <input type="hidden" name="userid" value="<?= htmlspecialchars($_SESSION['userid']) ?>">

                <input type="file" id="file-picker" name="file" accept="image/jpeg, image/png, image/webp" hidden>
                <button type="submit" style="display:none;">Envoyer</button>
            </form>

            <!-- Ouverture de la galerie perso : route MVC aussi -->
            <form id="open-gallery" action="/" method="get">
                <input type="hidden" name="page" value="my_gallery">
                <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">

                <label for="open-gallery-button">
                    <img src="/assets/explore_gallery.png" alt="Voir ma galerie entière">
                </label>

                <button id="open-gallery-button" type="submit" style="display:none;">Envoyer</button>
            </form>
        </div>
    <?php endif; ?>

    <h3>Collection générale</h3>

    <div class="general-medias">
        <?php if (!empty($data['general_medias'])): ?>
            <?php foreach ($data['general_medias'] as $img): ?>
                <img src="/assets/uploads/events/<?= htmlspecialchars(trim($img['url_media'])) ?>" alt="Image de l'événement">
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune image disponible pour cet événement.</p>
        <?php endif; ?>
    </div>

    <div class="show-more">
        <form action="" method="get" style="display:inline;">
            <input type="hidden" name="page" value="event_details">
            <input type="hidden" name="id" value="<?= htmlspecialchars($data['eventid']) ?>">
            <input type="hidden" name="show" value="<?= (int) $data['show'] + 8 ?>">
            <button type="submit">Voir plus</button>
        </form>

        <form action="" method="get" style="display:inline;">
            <input type="hidden" name="page" value="event_details">
            <input type="hidden" name="id" value="<?= htmlspecialchars($data['eventid']) ?>">
            <?php if ($data['show'] >= 20): ?>
                <input type="hidden" name="show" value="<?= (int) $data['show'] - 10 ?>">
            <?php else: ?>
                <input type="hidden" name="show" value="<?= (int) $data['show'] ?>">
            <?php endif; ?>
            <button type="submit">Voir moins</button>
        </form>
    </div>
</section>