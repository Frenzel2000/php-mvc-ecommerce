<div class="user-page" style="transform: translateY(-200px);">
    <div id="wrapper_login_page" style="position:relative; top:-200px;">
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
                Wenn ein Konto existiert, erhältst du einen Link zum Zurücksetzen.
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
