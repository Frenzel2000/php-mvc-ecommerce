<?php

require_once 'mainView.php';
require_once 'src/views/RatingView.php';

class ProductView extends mainView
{
    public static function detail($data)
    {
        $p = $data['product'];
        ?>
        <div class="product-page" >
            <div class="content_wrapper" style="margin-top: -200px;">

                <section class="product_main">

                    <div class="gallery">
                        <nav aria-label="product_picture_gallery" class="product_picture_gallery">
                            <a href="#"><img src="<?= htmlspecialchars(asset_url($p['asset_path'])) ?>" class="secondary_product_picture" /></a>
                            <a href="#"><img src="<?= htmlspecialchars(asset_url($p['asset_path'])) ?>" class="secondary_product_picture" /></a>
                            <a href="#"><img src="<?= htmlspecialchars(asset_url($p['asset_path'])) ?>" class="secondary_product_picture" /></a>
                        </nav>

                        <a href="#"><img src="<?= htmlspecialchars(asset_url($p['asset_path'])) ?>" class="main_product_picture" /></a>
                    </div>

                    <div class="product_options">
                        <h1><?= htmlspecialchars($p['product_name']) ?></h1>
                        <p><?= htmlspecialchars($p['description_short']) ?></p>

                        <div class="select_flavour_form_group">
                            <label for="select-flavour">Geschmack</label>
                            <select name="select-flavour" id="select-flavour" style="background-color: rgba(248, 212, 165, 0.757)">
                                <option value="<?= htmlspecialchars($p['flavour']) ?>">
                                    <?= htmlspecialchars($p['flavour']) ?>
                                </option>
                            </select>
                        </div>

                        <div class="price_group">
                            <h1>€<?= htmlspecialchars($p['price']) ?></h1>

                            <?php if (!empty($p['size'])): ?>
                                <p><?= htmlspecialchars($p['size']) ?></p>
                            <?php endif; ?>

                            <div class="stock_info" style="display: flex; align-items: center; margin-top: 10px; font-weight: bold;">
                                <?php if (isset($p['inventory']) && $p['inventory'] > 0): ?>
                                    <span style="height: 12px; width: 12px; background-color: #28a745; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                    <span style="color: #28a745;">Verfügbar</span>
                                <?php else: ?>
                                    <span style="height: 12px; width: 12px; background-color: #dc3545; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                    <span style="color: #dc3545;">Derzeit nicht verfügbar</span>
                                <?php endif; ?>
                            </div>

                            <div class="shipping_info" style="margin-top: 5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                </svg>
                                <p>Lieferzeit: 2-4 Werktage</p>
                            </div>

                            <form action="<?= BASE_URL ?>/cart/add" class="cart_form" method="POST" style="width: 100%;">
                                <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                                <input type="hidden" name="product_amount" value="1">

                                <button type="submit" class="add_to_cart_button">
                                    <span class="add_to_cart_text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                             class="feather feather-plus-circle">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="16"></line>
                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                        </svg>
                                        <span>Warenkorb</span>
                                    </span>
                                </button>
                            </form>

                        </div>
                    </div>

                </section>

                <div style="clear: both; width: 100%; height: 20px;"></div>

                <section class="product_secondary" style="clear: both; display: block; height: auto; overflow: visible;">
                    <hr />
                    <h1>Produktbeschreibung</h1>
                    <p><?= nl2br(htmlspecialchars($p['description_long'])) ?></p>
                    <hr />

                    <?php
                    RatingView::list([
                        'ratings'    => $data['rating_list'] ?? [],
                        'score'      => $data['rating_score'] ?? ['average' => 0, 'count' => 0],
                        'product_id' => (int)$p['product_id']
                    ]);
                    ?>
                </section>

            </div>
        </div>
        <?php
    }

    // Hilfsfunktion: gibt nur das HTML der Produkt-Karten zurück
    public static function renderProductGrid($products)
    {
        if (empty($products)) {
            return '<p style="text-align:center; width:100%;">Keine Produkte in diesem Preisbereich gefunden.</p>';
        }

        ob_start();
        ?>
        <div class="wrapper_bestseller" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); justify-content: start; gap: 20px;">
            <?php foreach ($products as $p): ?>
                <div class="bestseller">
                    <a href="<?= BASE_URL ?>/product/show/<?= (int)$p['product_id'] ?>">
                        <?php $img = !empty($p['asset_path']) ? $p['asset_path'] : 'assets/images/placeholder.png'; ?>
                        <img src="<?= htmlspecialchars(asset_url($img)) ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" />
                    </a>

                    <div class="bestseller_information">
                        <h3>
                            <a href="<?= BASE_URL ?>/product/show/<?= (int)$p['product_id'] ?>">
                                <?= htmlspecialchars($p['product_name']) ?>
                            </a>
                        </h3>

                        <p><?= htmlspecialchars($p['price']) ?> €</p>

                        <div class="stock_status" style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px; font-size: 0.85em;">
                            <?php if (isset($p['inventory']) && $p['inventory'] > 0): ?>
                                <span style="height: 8px; width: 8px; background-color: #28a745; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                                <span style="color: #28a745; font-weight: bold;">Verfügbar</span>
                            <?php else: ?>
                                <span style="height: 8px; width: 8px; background-color: #dc3545; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                                <span style="color: #dc3545; font-weight: bold;">Ausverkauft</span>
                            <?php endif; ?>
                        </div>

                        <form action="<?= BASE_URL ?>/cart/add" class="cart_form" method="POST">
                            <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                            <button type="submit" class="bestseller_button">In den Warenkorb</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // zeigt die Suchergebnisse in eigener Seite an
    public static function searchResults($data)
    {
        $products = $data['products'];
        $term = $data['searchTerm'];

        if (empty($term)) {
            $headline = "Alle Produkte";
        } else {
            $headline = 'Suchergebnisse für "' . htmlspecialchars($term) . '"';
        }

        ?>
        <div class="product-search">
            <h1 style="text-align: center; margin-bottom: 20px;"><?= $headline ?></h1>

            <?= self::renderFilterBar(null, $term) ?>

            <div id="products-output">
                <?= self::renderProductGrid($products) ?>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button id="load-more-btn" class="bestseller_button" style="width: auto; padding: 10px 30px;">
                    Mehr laden
                </button>
            </div>
        </div>
        <?php
    }

    // lädt den Filter
    public static function renderFilterBar($categoryId = null, $searchTerm = null)
    {
        $controller = $categoryId ? 'category' : 'product';
        $dataAttrs = 'data-controller="' . $controller . '" ';

        if ($categoryId) {
            $dataAttrs .= 'data-id="' . (int)$categoryId . '" data-mode="category"';
        } else {
            if (!empty($searchTerm)) {
                $dataAttrs .= 'data-term="' . htmlspecialchars($searchTerm) . '" data-mode="search"';
            } else {
                $dataAttrs .= 'data-mode="all"';
            }
        }
        ?>

        <div class="filter-container" style="text-align:center; margin-bottom: 30px;" <?= $dataAttrs ?>>
            <label>Min: <input type="number" id="min-price" value="0" style="width:50px;"></label>
            <label>Max: <input type="number" id="max-price" value="1000" style="width:50px;"></label>

            <br><br>

            <label style="margin-right: 15px; cursor: pointer;">
                <input type="checkbox" id="check-available"> Nur verfügbare
            </label>

            <label style="margin-right: 15px; cursor: pointer;">
                <input type="checkbox" id="check-bestseller"> Nach Bestseller sortieren
            </label>

            <button id="filter-btn" class="bestseller_button" style="width:auto; padding: 5px 15px;">Filtern</button>
        </div>
        <?php
    }
}
