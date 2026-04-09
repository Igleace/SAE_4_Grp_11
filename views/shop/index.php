<h1>LA BOUTIQUE</h1>

<div id="principal-section">
    <form method="post" action="/?page=shop" id="filter-form">
        <fieldset>
            <input id="search-input" type="text" name="search" placeholder="Rechercher un article"
                value="<?= htmlspecialchars($data['searchTerm'] ?? '') ?>">
        </fieldset>

        <?php if (!empty($data['categories'])): ?>
            <details>
                <summary>Catégories</summary>
                <fieldset>
                    <?php foreach ($data['categories'] as $category): ?>
                        <label>
                            <input type="checkbox" name="category[]" value="<?= htmlspecialchars($category) ?>"
                                <?= in_array($category, $data['filters'] ?? []) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($category) ?>
                        </label><br>
                    <?php endforeach; ?>
                </fieldset>
            </details>
        <?php endif; ?>

        <div>
            <label for="sort">Trier par</label>
            <select name="sort" id="sort">
                <option value="name_asc" <?= ($data['sort'] ?? 'name_asc') === 'name_asc' ? 'selected' : '' ?>>
                    Ordre alphabétique (A-Z)
                </option>
                <option value="name_desc" <?= ($data['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>
                    Ordre anti-alphabétique (Z-A)
                </option>
                <option value="price_asc" <?= ($data['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>
                    Prix croissant
                </option>
                <option value="price_desc" <?= ($data['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>
                    Prix décroissant
                </option>
            </select>
        </div>

        <button type="submit" name="apply" value="1">Appliquer</button>
        <button type="submit" name="reset" value="1">Réinitialiser</button>
    </form>

    <div id="cart-info">
        <button>
            <a href="/?page=cart">
                <img src="/assets/logo_caddie.png" alt="Logo du panier">
                <p>Panier (<span id="count"><?= htmlspecialchars($data['cartCount'] ?? 0) ?></span>)</p>
            </a>
        </button>
    </div>
</div>

<p id="message-reduc">
    * Articles non éligibles aux réductions de grade
</p>

<?php if (!empty($data['products'])): ?>
    <div id="product-list">
        <?php foreach ($data['products'] as $product): ?>
            <div id="one-product">
                <div>
                    <?php if (empty($product['image_article'])): ?>
                        <img src="/admin/ressources/default_images/boutique.png" alt="Image de l'article">
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($product['image_article']) ?>"
                            alt="<?= htmlspecialchars($product['nom_article']) ?>">
                    <?php endif; ?>

                    <h3 title="<?= htmlspecialchars($product['nom_article']) ?>">
                        <?= htmlspecialchars($product['nom_article']) ?>
                    </h3>

                    <p><?= number_format((float) $product['prix_article'], 2, ',', ' ') ?> €</p>

                    <p>
                        <?= htmlspecialchars($product['xp_article']) ?> XP
                        <?php if (!(int) ($product['reduction_article'] ?? 0)): ?>
                            <span> *</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div>
                    <p id="stock-status">
                        <?php if ((int) $product['stock_article'] > 0 || (int) $product['stock_article'] < 0): ?>
                            <a class="addCart" id="add-to-cart-button"
                                href="/?page=cart_add&id=<?= htmlspecialchars($product['id_article']) ?>">
                                Ajouter au panier
                            </a>
                        <?php else: ?>
                            <button id="out-of-stock" type="button">Épuisé</button>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucun produit trouvé pour les critères sélectionnés.</p>
<?php endif; ?>