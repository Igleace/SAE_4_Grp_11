<section class="event-details">
    <?php if (empty($data['news']['image_actualite'])): ?>
        <img src="/admin/ressources/default_images/event.jpg" alt="Image de l'actualité">
    <?php else: ?>
        <img src="/api/files/<?= htmlspecialchars($data['news']['image_actualite']) ?>" alt="Image de l'actualité">
    <?php endif; ?>

    <h1><?= strtoupper(htmlspecialchars($data['news']['titre_actualite'])) ?></h1>

    <div>
        <h2>
            <?= date('d/m/Y', strtotime($data['news']['date_actualite'])) ?>
        </h2>
    </div>

    <ul></ul>

    <p>
        <?= nl2br(htmlspecialchars($data['news']['contenu_actualite'])) ?>
    </p>
</section>