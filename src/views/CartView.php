<?php 

require_once 'mainView.php';

class CartView extends mainView {

    private static function formatPrice($price) {
        return number_format($price, 2, ',', '.') . '€';
    }

    public static function cart($data) {

        $cartItems = $data['cartItems'] ?? [];
        
        //Leere Warenkorb Ansicht
        if (empty($cartItems)):?>
        
            <section class="shopping_cart">
                <div class="shopping_cart_items">
                    <h1>Dein Warenkorb ist leer.</h1>
                    <a href="<?= BASE_URL ?>">Hier geht's zum Shop</a>
                </div>
            </section>
    
        <?php else: ?>
    
            <section class="shopping_cart">
                <div class="shopping_cart_items">
                    <h1>Dein Warenkorb</h1>
    
                    <?php foreach ($cartItems as $item): 
                        $rowPrice = $item['price'] * $item['product_amount'];
                    ?>
                        <div class="item_wrapper">
                            <div class="top_group">
                                <a href="<?= BASE_URL ?>/product/show/<?= $item['product_id'] ?>">
                                    <img 
                                    src="<?= htmlspecialchars(asset_url($item['asset_path'] ?? 'assets/images/placeholder.png')) ?>" 
                                    alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                    />
                                </a>
                                <article class="product_details">
                                    <a href="<?= BASE_URL ?>/product/detail/<?= $item['product_id'] ?>">
                                        <h2><?= htmlspecialchars($item['product_name']) ?></h2>
                                    </a>
                                    <p><?= htmlspecialchars($item['flavour'] ?? 'N/A') ?></p>
                                </article>
                            </div>
    
                            <div class="bottom_group">
                                <div class="change_amount_button">
                                    <!-- Anzahl verringern -->
                                    <form action="<?= BASE_URL ?>/cart/remove" class="cart_form" method="POST">             
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <button type="submit" class="reduce_amount_button">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                                                stroke="grey" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-minus-square">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="8" y1="12" x2="16" y2="12"></line>
                                            </svg>
                                        </button>
                                    </form>
                                    <span id="product_amount"><?= $item['product_amount'] ?></span>
                                    <!-- Anzahl erhöhen -->
                                    <form action="<?= BASE_URL ?>/cart/add" class="cart_form" method="POST">             
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="product_amount" value="1">
                                        <button type="submit" class="add_button">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                                                stroke="grey" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-plus-square">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                                <line x1="8" y1="12" x2="16" y2="12"></line>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
    
                                <article class="price_information">
                                    <h2><?= self::formatPrice($rowPrice) ?></h2>
                                    <p><?= self::formatPrice($item['price']) ?>/Stk</p>
                                </article>
                            </div>
                        </div>
                    <?php endforeach; ?>
    
                    <hr />
    
                    <!-- Unterer Bereich -->
                    <div class="total_sum">
                        <article>
                            <h2>Gesamtsumme</h2>
                            <p>inkl. MwSt, zzgl. Versandkosten</p>
                        </article>
                        <h1 class="total_price_display"><?= self::formatPrice($data['total']) ?></h1>
                    </div>
    
                    <a href="<?= BASE_URL ?>/order/checkout" class="checkout_button">
                        <span class="checkout_button_content">
                            Zur Kasse
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="white"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="feather feather-arrow-right"
                            >
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </span>
                    </a>
                </div>
            </section>
    
        <?php endif;
    }
}
