<section class="user-gallery">

    <a href="/?page=event_details&id=<?= htmlspecialchars($data['eventid']) ?>" class="back-arrow">
        &#8592;<span>Retour</span>
    </a>

    <h1>MA GALLERIE</h1>
    <h2><?= htmlspecialchars($data['event']['nom_evenement']) ?></h2>

    <div class="my-medias">

        <form id="add-media" action="/?page=add_media" method="post" enctype="multipart/form-data">
            <label for="file-picker">
                <img src="/assets/add_media.png" alt="Ajouter un média">
            </label>
            <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
            <input type="hidden" name="userid" value="<?= htmlspecialchars($data['userid']) ?>">

            <input type="file" id="file-picker" name="file" accept="image/jpeg, image/png, image/webp" hidden>
            <button type="submit" style="display:none;">Envoyer</button>
        </form>

        <?php if (!empty($data['medias'])): ?>
            <?php foreach ($data['medias'] as $img): ?>
                <div class="media-container">
                    <img src="/assets/uploads/events/<?= htmlspecialchars(trim($img['url_media'])) ?>" alt="Image personnelle de l'événement">

                    <div class="delete-icon">
                        <form class="delete-media" action="/?page=delete_media" method="post">
                            <label>
                                <img src="/assets/delete_icon.png" alt="Supprimer le média">
                            </label>
                            <input type="hidden" name="mediaid" value="<?= htmlspecialchars($img['id_media']) ?>">
                            <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
                            <button type="submit" style="display:none;">Envoyer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun média pour cet événement.</p>
        <?php endif; ?>

    </div>

    <div class="show-more">
        <form action="" method="get" style="display:inline;">
            <input type="hidden" name="page" value="my_gallery">
            <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
            <input type="hidden" name="show" value="<?= (int)$data['show'] + 8 ?>">
            <button type="submit">Voir plus</button>
        </form>

        <form action="" method="get" style="display:inline;">
            <input type="hidden" name="page" value="my_gallery">
            <input type="hidden" name="eventid" value="<?= htmlspecialchars($data['eventid']) ?>">
            <?php if ($data['show'] >= 20): ?>
                <input type="hidden" name="show" value="<?= (int)$data['show'] - 10 ?>">
            <?php else: ?>
                <input type="hidden" name="show" value="<?= (int)$data['show'] ?>">
            <?php endif; ?>
            <button type="submit">Voir moins</button>
        </form>
    </div>
</section>