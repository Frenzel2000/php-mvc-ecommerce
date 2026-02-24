<?php

require_once 'mainView.php';
require_once 'src/views/CategoryView.php';

class HomeView extends mainView {

    public static function index($data)
    {
        $bestsellers = $data['bestsellers'] ?? [];
        $categories  = $data['categories'] ?? [];
        
        //rendert Bestseller
        self::listBestsellers($bestsellers);

        //rendert Kategorien darunter
        CategoryView::index($categories);
    }

    public static function listBestsellers($products) {
        ?>
        <div class="home-main">
        <section class="bestseller_section">

            <h1 >Bestseller</h1>

            <div class="wrapper_bestseller">
                <?php foreach ($products as $p): ?>
                    <div class="bestseller">

                        <a href="<?= BASE_URL ?>/product/show/<?= (int)$p['product_id'] ?>">
                            <img src="<?= htmlspecialchars($p['asset_path']) ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" />
                        </a>

                        <div class="bestseller_information">

                            <h3>
                                <a href="<?= BASE_URL ?>/product/show/<?= (int)$p['product_id'] ?>">
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </a>
                            </h3>

                            <p><?= htmlspecialchars($p['description_short']) ?></p>

                            <p><?= htmlspecialchars($p['price']) ?> €</p>

                            <div class="stock_info" style="display: flex; align-items: center; margin-top: 10px;margin-bottom: 10px; margin-left: 70px; font-weight: bold;">
                                <?php if (isset($p['inventory']) && $p['inventory'] > 0): ?>
                                    <span style="height: 12px; width: 12px; background-color: #28a745; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                    <span style="color: #28a745;">Verfügbar</span>
                                <?php else: ?>
                                    <span style="height: 12px; width: 12px; background-color: #dc3545; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                    <span style="color: #dc3545;">Derzeit nicht verfügbar</span>
                                <?php endif; ?>
                            </div>

                            <form action="<?= BASE_URL ?>/cart/add" class="cart_form" method="POST">

                                <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">

                                <input type="hidden" name="product_amount" value="1">

                                <button type="submit" class="bestseller_button" style="cursor: pointer; border: none; width: 100%;">
                                    In den Warenkorb
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        </div>

        <?php
    }
}