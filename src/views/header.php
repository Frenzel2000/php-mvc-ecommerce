<?php
//Sicherheitshalber leeres Array, wenn keine Kategorien übergeben werden
$categories = $categories ?? [];
?>

<header>
    <!-- Logo, Suchleiste, Konto, Warenkorb-->
    <div class="upper_header">
      <a href="<?= BASE_URL ?>" class="logo">
        <img src="<?= htmlspecialchars(asset_url('assets/images/pwe.png')) ?>" alt="PowerPure-Logo" />
      </a>

      <!-- Suchleiste-->
      <form action="<?= BASE_URL ?>/product/showSearch" method="get" class="search-form">
          <input type="hidden" name="controller" value="product">
          <input type="hidden" name="action" value="showSearch">
          <input 
              type="search" 
              id="live-search-input" 
              name="term" 
              placeholder="Suchen..." 
              autocomplete="off" 
          />
          
          <button type="submit" aria-label="Suche starten">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
          </button>

          <!--- Klasse "active" zeigt Inhalt an --> 
          <div id="live-search-results" class="search-results-dropdown"></div>
      </form>

      <!-- Rechter Bereich -->
        <div class="main-header-right">
            <?php
            // Wir entscheiden hier, wohin das Icon führt:
            // Eingeloggt -> Profilseite | Nicht eingeloggt -> Login-Formular
            $profileLink = isset($_SESSION['user']) ? BASE_URL . '/user/profile' : BASE_URL . '/user/loginForm';
            ?>

            <a href="<?= $profileLink ?>" class="personal-account" title="<?= isset($_SESSION['user']) ? 'Mein Profil' : 'Anmelden' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>

            <a href="<?= BASE_URL ?>/cart/show" class="shopping-cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </a>

            <?php if (!empty($_SESSION['user'])): ?>
                <a href="<?= BASE_URL ?>/user/logout">Logout</a>
            <?php endif; ?>
        </div>

  <nav>
  <input type="checkbox" id="toggle_button" />
  <label for="toggle_button">
    <span class="material-symbols-rounded"> menu </span>
  </label>

  <ul>
    <!-- rendert kategorien im Header -->
    <?php foreach ($categories as $category): ?>
      <li>
        <a href="<?= BASE_URL ?>/category/show/<?= $category['category_id'] ?>">
          <?= htmlspecialchars($category['name']) ?>
        </a>

        <?php if (!empty($category['products'])): ?>
          <ul>
            <?php foreach ($category['products'] as $product): ?>
              <li>
                <a href="<?= BASE_URL ?>/product/show/<?= $product['product_id'] ?>">
                  <?= htmlspecialchars($product['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>

    <li><a href="<?= BASE_URL ?>/static/html/about_us.html">Über uns</a></li>

    <!--zeigt Adminpage, wenn ein Admin eingeloggt ist-->
    <?php if (
      hasPermission('category.manage')   // product_manager
      || hasPermission('role.assign')    // user_manager
      || hasPermission('system.admin')   // admin
      ): ?>
      <li><a href="<?= BASE_URL ?>/admin/index">Adminpage</a></li>
    <?php endif; ?>
    </ul>
  </nav>

</header>

