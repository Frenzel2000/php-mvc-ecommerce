<?php
require_once 'mainView.php';

class AdminView extends mainView
{
    //zeigt nur die Links, die eingeloggter Admin laut Controller sehen darf
    public static function dashboard($data)
    
    {

        ?>
        <div class="admin-main">
            <h1>Adminpage</h1>

            <div class="admin-actions">
                <?php if (!empty($data['canManageUsers'])): ?>
                    <a class="admin-btn" href="<?= BASE_URL ?>/admin/users">Benutzer verwalten</a>
                <?php endif; ?>

                <?php if (!empty($data['canManageProducts'])): ?>
                    <a class="admin-btn" href="<?= BASE_URL ?>/admin/products">Produkte verwalten</a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    //Zeigt alle User und Aktionsbuttons je nach Permission
    public static function users_list($data)
    {
        $users = $data['users'] ?? [];
        $canCreate = !empty($data['canCreate']);
        $canUpdate = !empty($data['canUpdate']);
        $canDelete = !empty($data['canDelete']);
        ?>
        <div class="admin-main">

        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
            <div style="
                margin: 12px 0 20px;
                padding: 12px 14px;
                border-radius: 10px;
                font-weight: 600;
                background: <?= ($flash['type'] ?? '') === 'success' ? '#dcfce7' : '#fee2e2' ?>;
                color: <?= ($flash['type'] ?? '') === 'success' ? '#166534' : '#991b1b' ?>;
                border: 1px solid <?= ($flash['type'] ?? '') === 'success' ? '#86efac' : '#fecaca' ?>;
            ">
                <?= htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

            <h1>User verwalten</h1>

            <div class="admin-actions">
                <?php if ($canCreate): ?>
                    <a class="admin-btn" href="<?= BASE_URL ?>/admin/userCreateForm">+ Neuen User anlegen</a>
                <?php endif; ?>
            </div>

            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Adresse</th><th>Rolle</th><th>Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['user_id'] ?></td>
                        <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars(($u['street'] ?? '').' '.($u['house_number'] ?? '').', '.($u['zip_code'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($u['roles'] ?? '') ?></td>
                        <td class="admin-actions-cell">
                        <div class="admin-actions-row">
                            <?php if ($canUpdate): ?>
                            <a class="admin-btn" href="<?= BASE_URL ?>/admin/userEditForm/<?= (int)$u['user_id'] ?>">Bearbeiten</a>
                            <?php endif; ?>

                            <?php if ($canDelete): ?>
                            <form class="admin-inline" method="post" action="<?= BASE_URL ?>/admin/userDelete"
                                    onsubmit="return confirm('User wirklich löschen?');">
                                <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                <button class="admin-btn admin-btn--danger" type="submit">Löschen</button>
                            </form>
                            <?php endif; ?>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    // Form für Create oder Edit Form
    // mode ist create oder edit
    public static function user_form($data)
    {
        $mode = $data['mode'] ?? 'create';
        $user = $data['user'] ?? null;
        $isEdit = ($mode === 'edit');

        //Action hängt davon ab, ob Create oder Update
        $action = $isEdit
            ? BASE_URL.'/admin/userUpdate'
            : BASE_URL.'/admin/userCreate';
        ?>
        <div class="admin-main">
            <h1><?= $isEdit ? 'User bearbeiten' : 'User anlegen' ?></h1>

            <form class="admin-form" method="post" action="<?= $action ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="user_id" value="<?= (int)$user['user_id'] ?>">
                <?php endif; ?>

                <div>
                    <label for="first_name">Vorname</label><br>
                    <input
                        id="first_name"
                        type="text"
                        name="first_name"
                        value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                        required
                    >
                </div>

                <div>
                    <label for="last_name">Nachname</label><br>
                    <input
                        id="last_name"
                        type="text"
                        name="last_name"
                        value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                        required
                    >
                </div>

                <div>
                    <label for="email">E-Mail</label><br>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div>
                    <label for="street">Straße (optional)</label><br>
                    <input
                        id="street"
                        type="text"
                        name="street"
                        value="<?= htmlspecialchars($user['street'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="house_number">Hausnummer (optional)</label><br>
                    <input
                        id="house_number"
                        type="text"
                        name="house_number"
                        value="<?= htmlspecialchars($user['house_number'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="zip_code">PLZ (optional)</label><br>
                    <input
                        id="zip_code"
                        type="text"
                        name="zip_code"
                        value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>"
                    >
                </div>

                <?php
                $canAssignRole = !empty($data['canAssignRole']);
                $roles = $data['roles'] ?? [];
                $currentRole = $data['currentRole'] ?? 'user';
                ?>

                <?php if ($canAssignRole): ?>
                    <div>
                        <label for="role_name">Rolle</label><br>
                        <select name="role_name" id="role_name">
                            <?php foreach ($roles as $r): ?>
                                <?php $roleName = $r['role_name']; ?>
                                <option value="<?= htmlspecialchars($roleName) ?>"
                                    <?= ($roleName === $currentRole) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roleName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if (!$isEdit): ?>
                    <div>
                        <label for="password">Passwort</label><br>
                        <input id="password" type="password" name="password" required>
                    </div>
                <?php endif; ?>

                <div class="admin-form-actions">
                    <button class="admin-btn" type="submit"><?= $isEdit ? 'Speichern' : 'Anlegen' ?></button>
                    <a class="admin-link" href="<?= BASE_URL ?>/admin/users">Abbrechen</a>
                </div>
            </form>
        </div>
        <?php
    }
    //zeigt eine Produktliste für den product_manager an 
    //bekommt array von getallWithCategory() 
    public static function products_list($data)
    {
        $products = $data['products'] ?? [];
        $canCreate = !empty($data['canCreate']);
        $canUpdate = !empty($data['canUpdate']);
        $canDelete = !empty($data['canDelete']);

        ?>
        <div class="admin-main">

        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
            <div style="
                margin: 12px 0 20px;
                padding: 12px 14px;
                border-radius: 10px;
                font-weight: 600;
                background: <?= ($flash['type'] ?? '') === 'success' ? '#dcfce7' : '#fee2e2' ?>;
                color: <?= ($flash['type'] ?? '') === 'success' ? '#166534' : '#991b1b' ?>;
                border: 1px solid <?= ($flash['type'] ?? '') === 'success' ? '#86efac' : '#fecaca' ?>;
            ">
                <?= htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        
            <h1>Produkte verwalten</h1>

            <div class="admin-actions">
                <?php if ($canCreate): ?>
                    <a class="admin-btn" href="<?= BASE_URL ?>/admin/productCreateForm">+ Neues Produkt anlegen</a>
                <?php endif; ?>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Preis</th><th>Kategorie</th><th>Bestand</th><th>Verkauft</th><th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= (int)$p['product_id'] ?></td>
                        <td><?= htmlspecialchars($p['product_name']) ?></td>
                        <td><?= htmlspecialchars($p['price']) ?></td>
                        <td><?= htmlspecialchars($p['category_name'] ?? '') ?></td>
                        <td><?= (int)$p['inventory'] ?></td>
                        <td><?= (int)$p['units_sold'] ?></td>
                        <td class="admin-actions-cell">
                        <div class="admin-actions-row">
                            <?php if ($canUpdate): ?>
                            <a class="admin-btn" href="<?= BASE_URL ?>/admin/productEditForm/<?= (int)$p['product_id'] ?>">Bearbeiten</a>
                            <?php endif; ?>

                            <?php if ($canDelete): ?>
                            <form class="admin-inline" method="post" action="<?= BASE_URL ?>/admin/productDelete"
                                    onsubmit="return confirm('Produkt wirklich löschen?');">
                                <input type="hidden" name="product_id" value="<?= (int)($p['product_id'] ?? 0) ?>">
                                <button class="admin-btn admin-btn--danger" type="submit">Löschen</button>
                            </form>
                            <?php endif; ?>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <div>
        <?php
    }

    //Form für Produkt erstellen oder bearbeiten je nach mode 
    //erwartete Keys create ode edit 
    public static function product_form($data)
    {
        $mode = $data['mode'] ?? 'create';
        $product = $data['product'] ?? [];
        $categories = $data['categories'] ?? [];
        $isEdit = ($mode === 'edit');

        $action = $isEdit
            ? BASE_URL.'/admin/productUpdate'
            : BASE_URL.'/admin/productCreate';
        ?>
        <div class="admin-main">
            <h1><?= $isEdit ? 'Produkt bearbeiten' : 'Produkt anlegen' ?></h1>

            <form class="admin-form" method="post" action="<?= $action ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                <?php endif; ?>

                <div>
                    <label for="product_name">Produktname</label><br>
                    <input
                        id="product_name"
                        type="text"
                        name="product_name"
                        value="<?= htmlspecialchars($product['product_name'] ?? '') ?>"
                        required
                    >
                </div>

                <div>
                    <label for="price">Preis (z.B. 29.99)</label><br>
                    <input
                        id="price"
                        type="text"
                        name="price"
                        value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                        required
                    >
                </div>

                <div>
                    <label for="category_id">Kategorie</label><br>
                    <select name="category_id" id="category_id" required>
                        <option value="">Bitte wählen</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['category_id'] ?>"
                                <?= ((int)($product['category_id'] ?? 0) === (int)$c['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="inventory">Bestand</label><br>
                    <input
                        id="inventory"
                        type="number"
                        name="inventory"
                        value="<?= htmlspecialchars((string)($product['inventory'] ?? 0)) ?>"
                        required
                    >
                </div>

                <div>
                    <label for="units_sold">Verkauft</label><br>
                    <input
                        id="units_sold"
                        type="number"
                        name="units_sold"
                        value="<?= htmlspecialchars((string)($product['units_sold'] ?? 0)) ?>"
                        required
                    >
                </div>

                <div>
                    <label for="flavour">Geschmack (optional)</label><br>
                    <input
                        id="flavour"
                        type="text"
                        name="flavour"
                        value="<?= htmlspecialchars($product['flavour'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="size">Größe (optional)</label><br>
                    <input
                        id="size"
                        type="text"
                        name="size"
                        value="<?= htmlspecialchars($product['size'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="asset_path">Asset Path (optional)</label><br>
                    <input
                        id="asset_path"
                        type="text"
                        name="asset_path"
                        value="<?= htmlspecialchars($product['asset_path'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="description_short">Kurzbeschreibung (optional)</label><br>
                    <input
                        id="description_short"
                        type="text"
                        name="description_short"
                        value="<?= htmlspecialchars($product['description_short'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="description_long">Langbeschreibung (optional)</label><br>
                    <textarea id="description_long" name="description_long" rows="6"><?= htmlspecialchars($product['description_long'] ?? '') ?></textarea>
                </div>
                <div class="admin-form-actions">
                    <button class="admin-btn" type="submit"><?= $isEdit ? 'Speichern' : 'Anlegen' ?></button>
                    <a class="admin-link" href="<?= BASE_URL ?>/admin/products">Abbrechen</a>
                </div>
            </form>
        </div>
        <?php
    }

}
