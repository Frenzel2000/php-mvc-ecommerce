<?php

require_once 'mainView.php';
require_once 'ProductView.php';

class CategoryView extends mainView {

    //rendert Liste aller Kategorien (für Body)
    public static function index($data)
    {
        $categories = $data ?? [];
        ?>
        <section class="bestseller_section"> <h1>Alle Kategorien</h1>

            <div class="wrapper_bestseller"> <?php foreach ($categories as $cat): ?>
                    <div class="bestseller"> <a href="<?= BASE_URL ?>/category/show/<?= $cat['category_id'] ?>">
                            <?php
                            $catImg = !empty($cat['asset_path']) ? $cat['asset_path'] : 'assets/images/Test_Bild-removebg-preview.png';
                            ?>
                            <img src="<?= htmlspecialchars(asset_url($catImg)) ?>" alt="<?= htmlspecialchars($cat['category_name']) ?>" />
                        </a>

                        <div class="bestseller_information"> <h3>
                                <a href="<?= BASE_URL ?>/category/show/<?= $cat['category_id'] ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </a>
                            </h3>

                            <p>
                                <?= !empty($cat['description']) ? htmlspecialchars($cat['description']) : 'Entdecke unsere Auswahl in dieser Kategorie.' ?>
                            </p>

                            <a href="<?= BASE_URL ?>/category/show/<?= $cat['category_id'] ?>"
                               class="bestseller_button"
                               style="display: block; text-align: center; text-decoration: none; line-height: 2.5;">
                                Anzeigen
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }

    public static function show($data) {
        $category = $data['category'];
        $products = $data['products'] ?? [];
        ?>
        <main style="padding-top: 120px; min-height: 100vh;">
            <section class="category_detail_section" style="padding: 20px;">

                <h1 style="text-align:center;"><?= htmlspecialchars($category['category_name']) ?></h1>

                <?= ProductView::renderFilterBar($category['category_id'], null) ?>

                <div id="products-output">
                    <?= ProductView::renderProductGrid($products) ?>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <button id="load-more-btn" class="bestseller_button" style="width: auto; padding: 10px 30px;">
                        Mehr laden
                    </button>
                </div>

            </section>
        </main>
        <script src="<?= BASE_URL ?>/static/js/category/category.js"></script>
        <?php
    }
}