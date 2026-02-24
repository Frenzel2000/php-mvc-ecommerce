<?php

require_once 'mainView.php';

class OrderView extends mainView
{
    public static function render_checkout($context)
    {
        $user = $context['user'] ?? [];
        $items = $context['cartItems'] ?? [];
        $total = $context['cartTotal'] ?? 0.0;
        $error = $_GET['error'] ?? null;
        ?>

        <section class="shopping_cart checkout-page">
            <a href="<?= BASE_URL ?>/">
                <h2 style="color:#333; margin-top:0;">PowerPure</h2>
            </a>

            <?php if ($error === 'stock'): ?>
                <div class="alert">Einige Artikel sind nicht mehr verfügbar.</div>
            <?php elseif ($error === 'order_failed'): ?>
                <div class="alert">Fehler bei der Bestellung. Bitte erneut versuchen.</div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/order/placeOrder" method="POST" style="width: 100%;">

                <div class="shipping_info">
                    <h4>Versand & Kontakt</h4>

                    <input type="email" name="email" placeholder="E-Mail Adresse" required
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" />

                    <div class="name_wrapper">
                        <input type="text" name="first_name" placeholder="Vorname" required
                               value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" />
                        <input type="text" name="last_name" placeholder="Nachname" required
                               value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" />
                    </div>

                    <div class="address_wrapper">
                        <input type="text" name="street" placeholder="Straße" style="flex: 3;" required
                               value="<?= htmlspecialchars($user['street'] ?? '') ?>" />
                        <input type="text" name="house_number" placeholder="Nr." style="flex: 1;" required
                               value="<?= htmlspecialchars($user['house_number'] ?? '') ?>" />
                    </div>

                    <div class="postal_wrapper">
                        <input type="text" name="zip_code" placeholder="PLZ" style="flex: 1;" required
                               value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>" />
                        <input type="text" name="city" placeholder="Stadt" style="flex: 2;" />
                    </div>
                </div>

                <div class="cart_summary">
                    <h4 style="margin-top:0;">Bestellübersicht</h4>
                    <?php foreach ($items as $item): ?>
                        <div class="cart_item">
                            <span><?= (int)$item['product_amount'] ?>x <?= htmlspecialchars($item['product_name']) ?></span>
                            <span><?= number_format($item['price'] * $item['product_amount'], 2, ',', '.') ?>€</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="payment_info">
                    <h4>Zahlung</h4>

                    <label class="payment-option">
                        <input type="radio" name="payment" value="paypal" checked />
                        <span>PayPal</span>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment" value="invoice" />
                        <span>Rechnung</span>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment" value="creditcard" />
                        <span>Kreditkarte</span>
                    </label>
                </div>

                <div class="final_price">
                    <div class="left_side">
                        <h4>Gesamtbetrag</h4>
                        <p>inkl. MwSt.</p>
                    </div>
                    <div class="right_side">
                        <div class="price_display"><?= number_format($total, 2, ',', '.') ?>€</div>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button type="submit" class="payment_button">Kostenpflichtig bestellen</button>
                </div>

            </form>
        </section>

        <?php
    }

    public static function render_success($context)
    {
        $orderId = $context['order_id'] ?? 0;
        ?>

        <div class="success-container">
            <div class="card">
                <div class="success-icon">✓</div>
                <h2>Vielen Dank!</h2>
                <p>Deine Bestellung ist erfolgreich eingegangen.</p>
                <a href="<?= BASE_URL ?>/" class="btn-home">Weiter einkaufen</a>
            </div>
        </div>

        <?php
    }

    public static function render_error($context)
    {
        $reason = $context['reason'] ?? 'general';
        $failedProducts = $context['failed_products'] ?? [];
        $userId = $context['user_id'] ?? null;
        ?>

        <div class="failure-container">
            <div class="card">
                <div class="failure-icon">✗</div>

                <?php if ($reason === 'stock'): ?>
                    <h2>Lagerbestand geändert</h2>
                    <p class="error-reason">Einige Artikel sind nicht mehr ausreichend verfügbar</p>

                    <ul class="error-list" style="text-align: left; margin: 20px auto; max-width: 300px;">
                        <?php foreach ($failedProducts as $item): ?>
                            <li style="margin-bottom: 10px;">
                                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                <span style="color: #d9534f; font-size: 0.9rem;">
                                    <?php
                                    if (($item['stock'] ?? 0) <= 0) {
                                        echo "Leider komplett ausverkauft";
                                    } else {
                                        echo "Nur noch " . (int)$item['stock'] . " Stück verfügbar";
                                    }
                                    ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                <?php else: ?>
                    <h2>Es ist ein Fehler aufgetreten!</h2>
                    <p>Bei der Verarbeitung Ihrer Bestellung ist ein technisches Problem aufgetreten.</p>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/cart/show" class="btn-home">Warenkorb bearbeiten</a>
            </div>
        </div>

        <?php
    }
}
