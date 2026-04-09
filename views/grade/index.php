<h1>Les grades</h1>

<div>
    <?php
        if (isset($_SESSION['message'])) {
            $messageStyle = isset($_SESSION['message_type']) && $_SESSION['message_type'] === "error"
                ? "error-message"
                : "success-message";
            echo '<div id="' . $messageStyle . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
    ?>
</div>

<?php if (!empty($data['grades'])) : ?>
    <div id="product-list">
        <?php foreach ($data['grades'] as $grade) : ?>
            <div id="one-product">
                <div>
                    <?php if (empty($grade['image_grade'])): ?>
                        <img src="/admin/ressources/default_images/grade.webp" alt="Image du grade" />
                    <?php else: ?>
                        <img src="/api/files/<?= htmlspecialchars($grade['image_grade']) ?>" alt="Image du grade" />
                    <?php endif; ?>

                    <h3 title="<?= htmlspecialchars($grade['nom_grade']) ?>">
                        <?= htmlspecialchars($grade['nom_grade']) ?>
                    </h3>

                    <?php if (!empty($grade['description_grade'])): ?>
                        <p><?= htmlspecialchars($grade['description_grade']) ?></p>
                    <?php endif; ?>

                    <p>-- Prix : <?= number_format((float)$grade['prix_grade'], 2, ',', ' ') ?> € --</p>
                </div>

                <div>
                    <p id="adhesion-status">
                        <?php if (!empty($grade['owned'])): ?>
                            <button id="detention" type="button">Vous détenez ce grade</button>
                        <?php else: ?>
                            <a id="buy-button" href="/?page=grade_subscribe&id=<?= htmlspecialchars($grade['id_grade']) ?>">
                                Acheter
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p>Aucun grade trouvé.</p>
<?php endif; ?>