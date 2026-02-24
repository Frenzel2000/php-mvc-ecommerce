<?php
require_once 'src/views/mainView.php';


class UserView extends mainView {

    public static function login($data = [])
    {
        ?>
        <div class="user-page">
            <div id="wrapper_login_page" style="margin-top:-200px;">
                <div id="wrapper_logo">
                    <a href="<?= BASE_URL ?>/">
                        <img src="/ws2526_dwp_frenzel_kocyatagi_brandmaier/assets/images/pwe.png" alt="PowerPure Logo" />
                    </a>
                </div>

                <form class="loginformular"
                    method="post"
                    action="<?= BASE_URL ?>/user/login">

                    <h2>Login</h2>
                    <!--zeigt roten Text, wenn eine error Nachricht beim login entstanden ist-->
                    <?php if (!empty($data['error'])): ?>
                        <p style="color:red; font-weight:bold; margin: 10px 0;">
                            <?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>

                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        required
                    />

                    <input
                        type="password"
                        name="password"
                        placeholder="Passwort"
                        required
                    />

                    <button type="submit" class="login_button">
                        Einloggen
                    </button>

                    <span class="forgot_password_link">
                    <a href="<?= BASE_URL ?>/user/forgotPasswordForm">
                        Passwort vergessen?
                    </a>
                    </span>

                    <span class="registration_link">
                        <a href="<?= BASE_URL ?>/user/registerForm">
                            Hier registrieren
                        </a>
                    </span>
                </form>
            </div>
        </div>
    <?php
    }

    public static function register()
    {
        ?>
        <div class="user-page">
            <div id="wrapper_login_page">
                <div id="wrapper_logo">
                    <a href="<?= BASE_URL ?>/">
                        <img src="/ws2526_dwp_frenzel_kocyatagi_brandmaier/assets/images/pwe.png" alt="PowerPure Logo" />
                    </a>
                </div>

                <form class="loginformular"
                    method="post"
                    action="<?= BASE_URL ?>/user/register">

                    <h2>Registrieren</h2>

                    <?php if (!empty($_GET['error']) && $_GET['error'] === 'pw_mismatch'): ?>
                    <p style="color:#ef4444; font-weight:bold; margin: 10px 0;">
                        Passwörter stimmen nicht überein.
                    </p>
                    <?php endif; ?>

                    <?php if (!empty($_GET['error']) && $_GET['error'] === 'email_exists'): ?>
                    <p style="color:#ef4444; font-weight:bold; margin: 10px 0;">
                        Diese E-Mail ist bereits registriert.
                    </p>
                    <?php endif; ?>

                    <h3>Angaben zur Person</h3>

                    <input type="text" name="first_name" placeholder="Vorname" required />
                    <input type="text" name="last_name" placeholder="Nachname" required />
                    <input type="email" name="email" placeholder="Email" required />

                    <input id="password" type="password" name="password" placeholder="Passwort" required />
                    <input id="repeat_password" type="password" name="repeat_password" placeholder="Passwort wiederholen" required />

                    <p id="pw-match-hint" style="color:#ef4444; font-weight:bold; margin-top:6px;"></p>


                    <h3>Angaben zur Lieferadresse</h3>

                    <input type="text" name="house_number" placeholder="Hausnummer" />
                    <input type="text" name="street" placeholder="Adresse" />
                    <input type="text" name="zip_code" placeholder="PLZ" />

                    <button type="submit" class="register_button">
                        Registrieren
                    </button>
                </form>
            </div>
        </div>
        <?php
    }

    public static function forgot_password()
{
    ?>
    <div class="user-page">
        <div id="wrapper_login_page">
            <div id="wrapper_logo">
                <a href="<?= BASE_URL ?>/">
                    <img src="/ws2526_dwp_frenzel_kocyatagi_brandmaier/assets/images/pwe.png" alt="PowerPure Logo" />
                </a>
            </div>

            <form class="loginformular"
                  method="post"
                  action="<?= BASE_URL ?>/user/forgotPassword">

                <h2>Passwort vergessen</h2>

                <p>
                    Gib deine E-Mail-Adresse ein.  
                    Wenn ein Konto existiert, erhältst du einen Reset-Link.
                </p>

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                />

                <button type="submit" class="login_button">
                    Passwort zurücksetzen
                </button>

                <span class="registration_link">
                    <a href="<?= BASE_URL ?>/user/loginForm">
                        Zurück zum Login
                    </a>
                </span>
            </form>
        </div>
    </div>
    <?php
}

public static function reset_password($context = [])
    {
    $token = $context['token'] ?? '';
    ?>
    <div class="user-page">
        <div id="wrapper_login_page">
            <div id="wrapper_logo">
                <a href="<?= BASE_URL ?>/">
                    <img src="/ws2526_dwp_frenzel_kocyatagi_brandmaier/assets/images/pwe.png"
                         alt="PowerPure Logo" />
                </a>
            </div>

            <form class="loginformular"
                  method="post"
                  action="<?= BASE_URL ?>/user/resetPassword">

                <h2>Neues Passwort setzen</h2>

                <?php if (!empty($_GET['error']) && $_GET['error'] === 'pw_mismatch'): ?>
                <p style="color:#ef4444; font-weight:bold; margin: 10px 0;">
                    Passwörter stimmen nicht überein.
                </p>
                <?php endif; ?>

                <?php if (!empty($_GET['error']) && $_GET['error'] === 'invalid_token'): ?>
                <p style="color:#ef4444; font-weight:bold; margin: 10px 0;">
                    Ungültiger oder abgelaufener Token.
                </p>
                <?php endif; ?>

                <input
                    type="password"
                    name="password"
                    placeholder="Neues Passwort"
                    required
                />

                <input
                    type="password"
                    name="repeat_password"
                    placeholder="Passwort wiederholen"
                    required
                />

                <!-- Reset-Token -->
                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                />

                <button type="submit" class="login_button">
                    Passwort ändern
                </button>

                <span class="registration_link">
                    <a href="<?= BASE_URL ?>/user/loginForm">
                        Zurück zum Login
                    </a>
                </span>
            </form>
        </div>
    </div>
    <?php
}
    public static function profile($data = [])
    {
        $user = $data['userData'] ?? [];
        $orders = $data['orders'] ?? [];
        $ratings = $data['ratings'] ?? [];
        $success = $data['success'] ?? null;
        $error = $data['error'] ?? null;
        ?>
        <main>
            <div class="profile-wrapper">
                <div class="profile-logo">
                    <a href="<?= BASE_URL ?>/">
                    </a>
                </div>

                <div class="profile-container">
                    <h2 class="profile-title">Mein Profil</h2>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="profile-grid">
                        <!-- Persönliche Daten Form -->
                        <div class="profile-section">
                            <form class="profile-form" method="post" action="<?= BASE_URL ?>/user/updateUser">
                                <h3 class="section-title">Persönliche Daten</h3>

                                <div class="form-group">
                                    <label for="first_name">Vorname</label>
                                    <input type="text" id="first_name" name="first_name"
                                           placeholder="Vorname"
                                           value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                           required />
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Nachname</label>
                                    <input type="text" id="last_name" name="last_name"
                                           placeholder="Nachname"
                                           value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                           required />
                                </div>

                                <div class="form-group">
                                    <label for="email">E-Mail</label>
                                    <input type="email" id="email" name="email"
                                           placeholder="E-Mail"
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                           required />
                                </div>

                                <h3 class="section-title section-title-spacing">Lieferadresse</h3>

                                <div class="form-group">
                                    <label for="street">Straße</label>
                                    <input type="text" id="street" name="street"
                                           placeholder="Straße"
                                           value="<?= htmlspecialchars($user['street'] ?? '') ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="house_number">Hausnummer</label>
                                    <input type="text" id="house_number" name="house_number"
                                           placeholder="Hausnummer"
                                           value="<?= htmlspecialchars($user['house_number'] ?? '') ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="zip_code">Postleitzahl</label>
                                    <input type="text" id="zip_code" name="zip_code"
                                           placeholder="PLZ"
                                           value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>" />
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Änderungen speichern
                                </button>
                            </form>
                        </div>

                        <!-- Bestellungen & Bewertungen -->
                        <div class="profile-section profile-section-wide">
                            <!-- Bestellungen -->
                            <div class="orders-section">
                                <h3 class="section-title">Meine Bestellungen</h3>
                                <?php if (empty($orders)): ?>
                                    <p class="empty-message">Du hast noch keine Bestellungen.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="orders-table">
                                            <thead>
                                            <tr>
                                                <th>Produkt</th>
                                                <th>Datum</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($order['items'])): ?>
                                                            <ul style="padding-left: 1rem;">
                                                                <?php foreach ($order['items'] as $item): ?>
                                                                    <li>
                                                                        <?= htmlspecialchars($item['product_name']) ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <em>Keine Produkte</em>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d.m.Y', strtotime($order['date'])) ?></td>
                                                    <td>
                                                        <span class="status-badge">
                                                            <?= htmlspecialchars($order['state']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Bewertungen -->
                            <div class="ratings-section">
                                <h3 class="section-title">Meine Bewertungen</h3>
                                <?php if (empty($ratings)): ?>
                                    <p class="empty-message">Keine Bewertungen vorhanden.</p>
                                <?php else: ?>
                                    <div class="ratings-list">
                                        <?php foreach ($ratings as $rating): ?>
                                            <div class="rating-card">
                                                <div class="rating-header">
                                                    <strong class="product-name">
                                                        <?= htmlspecialchars($rating['product_name'] ?? 'Unbekanntes Produkt') ?>
                                                    </strong>
                                                    <div class="rating-score">
                                                        <?php
                                                        $score = $rating['rating_score'] ?? 0;
                                                        for ($i = 1; $i <= 5; $i++): ?>
                                                            <span class="star <?= $i <= $score ? 'star-filled' : '' ?>">★</span>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>

                                                <?php if (!empty($rating['comment'])): ?>
                                                    <p class="rating-comment">
                                                        <?= htmlspecialchars($rating['comment']) ?>
                                                    </p>
                                                <?php endif; ?>

                                                <small class="rating-date">
                                                    <?= isset($rating['date']) ? date('d.m.Y', strtotime($rating['date'])) : '' ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Gefahrenzone -->
                    <div class="danger-zone">
                        <h3 class="danger-zone-title">Gefahrenzone</h3>
                        <p class="danger-zone-description">
                            Diese Aktion kann nicht rückgängig gemacht werden.
                        </p>
                        <form action="<?= BASE_URL ?>/user/deleteAccount" method="post"
                              onsubmit="return confirm('Möchtest du dein Konto wirklich dauerhaft löschen? Diese Aktion kann nicht rückgängig gemacht werden.');">
                            <button type="submit" class="btn btn-danger">
                                Konto dauerhaft löschen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <?php
    }

}