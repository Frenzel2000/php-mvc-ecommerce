<?php
require_once 'Controller.php';
require_once 'src/models/UserModel.php';
require_once 'src/views/AdminView.php';
require_once 'src/models/ProductModel.php';
require_once 'src/models/CategoryModel.php';

//Zentrale Klasse für Produkt- und Benutzermanagement
// Zugriff über Permissions gesteuert
class AdminController extends Controller
{
    private UserModel $userModel;
    private CategoryModel $categoryModel; 

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
        $this->categoryModel = new CategoryModel($db);

    }

    //Dashboard für Admins 
    //zeigt Links abhängig von Permissions an
    public function index()
    {
        $this->requireLogin();
        if (!(
            hasPermission('category.manage')
            || hasPermission('role.assign')
            || hasPermission('system.admin')
        )) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }

        $permissions = [
            'canManageUsers' => hasPermission('user.read'),
            'canManageProducts' => (hasPermission('product.create') || hasPermission('product.update') || hasPermission('product.delete')),
        ];

        $view = new AdminView();
        $view->set_title('Adminpage');
        $view->add_css('admin/admin.css'); 
        $view->render_html('dashboard', $this->mergeData($permissions));
    }

    //lädt alle User für Tabelle des user_managers
    public function users()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('user.read');

        $users = $this->userModel->getAllWithAddress();

        $view = new AdminView();
        $view->set_title('User verwalten');
        $view->add_css('admin/admin.css');
        $view->render_html('users_list', $this->mergeData([
            'users' => $users,
            'canCreate' => hasPermission('user.create'),
            'canUpdate' => hasPermission('user.update'),
            'canDelete' => hasPermission('user.delete'),
        ]));
    }

    //Formular zum User anlegen für user_manager
    public function userCreateForm()
    {
    $this->requireLogin();
    $this->denyAccessUnlessGranted('user.create');

    $canAssignRole = hasPermission('role.assign') || hasPermission('system.admin');
    $roles = $canAssignRole ? $this->userModel->getAllRoles() : [];

    $view = new AdminView();
    $view->set_title('User anlegen');
    $view->add_css('admin/admin.css');
    $view->render_html('user_form', $this->mergeData([
        'mode' => 'create',
        'user' => null,
        'canAssignRole' => $canAssignRole,
        'roles' => $roles,
        'currentRole' => 'user',
        ]));
    }


    //verarbeitet POST aus userCreateForm
    public function userCreate()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('user.create');

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/admin/users');
        }

        $first = trim((string)$this->getBody('first_name', ''));
        $last  = trim((string)$this->getBody('last_name', ''));
        $email = trim((string)$this->getBody('email', ''));
        $pw    = (string)$this->getBody('password', '');

        //Pflichtfelder check
        if ($first === '' || $last === '' || $email === '' || $pw === '') {
            $this->redirect(BASE_URL . '/admin/userCreateForm');
        }

        //Email Duplikat
        if ($this->userModel->getByEmail($email)) {
            $this->redirect(BASE_URL . '/admin/userCreateForm');
        }
        //Default Rolle 
        $roleName = 'user';

        // Nur wenn der eingeloggte Admin Rollen zuweisen darf, Rolle aus dem Formular übernehmen
        if (hasPermission('role.assign') || hasPermission('system.admin')) {
            $roleName = trim((string)$this->getBody('role_name', 'user'));
            if ($roleName === '') $roleName = 'user';
        }


        $this->userModel->createUser([
        'first_name' => $first,
        'last_name' => $last,
        'email' => $email,
        'password_hash' => password_hash($pw, PASSWORD_DEFAULT),
        //TODO: Adresse könnte erstmal leer sein
        'house_number' => $this->getBody('house_number', null),
        'street' => $this->getBody('street', null),
        'zip_code' => $this->getBody('zip_code', null),

        'role_name' => $roleName, 
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User wurde erfolgreich angelegt.'];
        $this->redirect(BASE_URL . '/admin/users');
    }

    //Formular zum User bearbeiten 
    public function userEditForm($id = null)
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('user.update');

        $id = (int)$id;
        $user = $this->userModel->getByIDWithAddress($id);

        if (!$user) {
            http_response_code(404);
            echo 'User not found';
            exit;
        }

        $view = new AdminView();
        $view->set_title('User bearbeiten');
         $canAssignRole = hasPermission('role.assign') || hasPermission('system.admin');

        $roles = [];
        $currentRole = 'user';

        if ($canAssignRole) {
            $roles = $this->userModel->getAllRoles(); 
            //holt nur aktuelle Rolle 
            $userRoles = $this->userModel->getRolesByUserId($id); 
            $currentRole = $userRoles[0]['role_name'] ?? 'user';  
        }
        $view->add_css('admin/admin.css');
        $view->render_html('user_form', $this->mergeData([
        'mode' => 'edit',
        'user' => $user,
        'canAssignRole' => $canAssignRole,
        'roles' => $roles,
        'currentRole' => $currentRole,
        ]));

    }

    //nimmt Daten aus Formular userEditForm
    public function userUpdate()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('user.update');

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/admin/users');
        }

        $id = (int)$this->getBody('user_id', 0);

        $data = [
            'first_name' => trim((string)$this->getBody('first_name', '')),
            'last_name'  => trim((string)$this->getBody('last_name', '')),
            'email'      => trim((string)$this->getBody('email', '')),
            'house_number' => $this->getBody('house_number', null),
            'street'       => $this->getBody('street', null),
            'zip_code'     => $this->getBody('zip_code', null),
        ];

        $newPw = (string)$this->getBody('password', '');
        if ($newPw !== '') {
            $data['password_hash'] = password_hash($newPw, PASSWORD_DEFAULT);
        }

        $this->userModel->updateUserWithAddress($id, $data);

        if (hasPermission('role.assign')) {
        $roleName = trim((string)$this->getBody('role_name', ''));
            if ($roleName !== '') {
                //alte Rolle entfernen und neue setzen
                $this->userModel->setSingleRole($id, $roleName); 
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User wurde erfolgreich aktualisiert.'];
        $this->redirect(BASE_URL . '/admin/users');
    }

    //User löschen, wenn user_manager den löschen Button drückt 
    public function userDelete()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('user.delete');

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/admin/users');
        }

        $id = (int)$this->getBody('user_id', 0);
        $this->userModel->deleteUser($id);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User wurde gelöscht.'];
        $this->redirect(BASE_URL . '/admin/users');
    }

    //zeigt alle Produkte für den product_manager an 
    public function products()
    {
    $this->requireLogin();
    $this->denyAccessUnlessGranted('product.read');

    $products = $this->productModel->getAllWithCategory();

    $view = new AdminView();
    $view->set_title('Produkte verwalten');
    $view->add_css('admin/admin.css');
    $view->render_html('products_list', $this->mergeData([
        'products' => $products,
        'canCreate' => hasPermission('product.create'),
        'canUpdate' => hasPermission('product.update'),
        'canDelete' => hasPermission('product.delete'),
        ]));
    }
    
    //Formular für product_manager zum erstellen eines Produktes 
    public function productCreateForm()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('product.create');

        $view = new AdminView();
        $view->set_title('Produkt anlegen');
        $view->add_css('admin/admin.css');
        $view->render_html('product_form', $this->mergeData([
            'mode' => 'create',
            'product' => [],
            'categories' => $this->categoryModel->getAll(),
        ]));
    }

    //nimmt Daten von productCreateForm 
    public function productCreate()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('product.create');

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/admin/products');
        }

        $data = [
            'product_name' => trim((string)$this->getBody('product_name', '')),
            'price' => trim((string)$this->getBody('price', '')),
            'category_id' => (int)$this->getBody('category_id', 0),
            'inventory' => (int)$this->getBody('inventory', 0),
            'units_sold' => (int)$this->getBody('units_sold', 0),
            //TODO: bis jetzt optionale Felder
            'flavour' => $this->getBody('flavour', null),
            'size' => $this->getBody('size', null),
            'description_short' => $this->getBody('description_short', null),
            'description_long' => $this->getBody('description_long', null),
            'asset_path' => $this->getBody('asset_path', null),
        ];

        //Pflichtfelder check
        if ($data['product_name'] === '' || $data['price'] === '' || $data['category_id'] <= 0) {
            $this->redirect(BASE_URL . '/admin/productCreateForm');
        }

        $this->productModel->create($data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produkt wurde erfolgreich angelegt.'];
        $this->redirect(BASE_URL . '/admin/products');
    }

    //Formular zum Produkt bearbeiten 
    public function productEditForm($id = null)
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('product.update');

        $id = (int)$id;
        $product = $this->productModel->getByID($id);

        if (!$product) {
            http_response_code(404);
            echo 'Produkt nicht gefunden';
            exit;
        }

        $view = new AdminView();
        $view->set_title('Produkt bearbeiten');
        $view->add_css('admin/admin.css');
        $view->render_html('product_form', $this->mergeData([
            'mode' => 'edit',
            'product' => $product,
            'categories' => $this->categoryModel->getAll(),
        ]));
    }

    //nimmt Daten von productEditForm 
    public function productUpdate()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('product.update');

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/admin/products');
        }

        $id = (int)$this->getBody('product_id', 0);

        $data = [
            'product_name' => trim((string)$this->getBody('product_name', '')),
            'price' => trim((string)$this->getBody('price', '')),
            'category_id' => (int)$this->getBody('category_id', 0),
            'inventory' => (int)$this->getBody('inventory', 0),
            'units_sold' => (int)$this->getBody('units_sold', 0),
            'flavour' => $this->getBody('flavour', null),
            'size' => $this->getBody('size', null),
            'description_short' => $this->getBody('description_short', null),
            'description_long' => $this->getBody('description_long', null),
            'asset_path' => $this->getBody('asset_path', null),
        ];

        if ($id <= 0 || $data['product_name'] === '' || $data['price'] === '' || $data['category_id'] <= 0) {
            $this->redirect(BASE_URL . '/admin/products');
        }

        $this->productModel->update($id, $data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produkt wurde erfolgreich aktualisiert.'];
        $this->redirect(BASE_URL . '/admin/products');
    }

    //löscht Produkt bei Button klick 
    public function productDelete()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('product.delete');

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/admin/products');
        }

        $id = (int)$this->getBody('product_id', 0);
        if ($id > 0) {
            $this->productModel->removeAdmin($id);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produkt wurde gelöscht.'];
        $this->redirect(BASE_URL . '/admin/products');
    }

}
