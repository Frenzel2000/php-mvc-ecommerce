<?php
$title = $title ?? 'PowerPure';
?>

<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= htmlspecialchars($title)?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <script>
      window.BASE_URL = "<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>";
    </script>

    <!--Burger-Menu-Icon-->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu"
    />
    <!-- Social Media Icons-->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
      <link rel="stylesheet" href="<?= BASE_URL ?>/static/css/user/profile.css">
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/general_settings/normalize.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/general_settings/color_scheme.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/header.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/hero_banner.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/bestseller.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/category.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/eye_catcher.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/interests.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/index.css/footer.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/login/login.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/login/registration.css"
    />
    <link
      rel="stylesheet"
      href="<?= BASE_URL ?>/static/css/admin/admin.css"
    />

    <!-- lädt spezifische Styles -->
    <?php if (!empty($stylesheets)): ?>
        <?php foreach ($stylesheets as $cssFile): ?>
            <link 
                rel="stylesheet" 
                href="<?= BASE_URL ?>/static/css/<?= htmlspecialchars($cssFile) ?>" 
            />
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- lädt spezifisches JS -->
    <?php if (!empty($scripts)): ?>
        <?php foreach ($scripts as $s):?>
          <script src="<?= BASE_URL ?>/static/js/<?= htmlspecialchars($s)?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="<?= BASE_URL ?>/static/js/search_bar/searchbar.js"></script>
  </head>
