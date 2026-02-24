<?php
// index.php
require_once __DIR__ . '/src/controllers/helpers/Auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';

$db = new Database();
$db->connect();

define('BASE_URL', '/ws2526_dwp_frenzel_kocyatagi_brandmaier');


require_once 'src/controllers/ProductController.php';
require_once 'src/controllers/CategoryController.php';
require_once 'src/controllers/CartController.php';
require_once 'src/controllers/UserController.php';
require_once 'src/controllers/RatingController.php';
require_once 'src/controllers/HomeController.php';
require_once 'src/controllers/AdminController.php';
require_once 'src/routing.php';
require_once 'src/controllers/helpers/url.php';
require_once 'src/controllers/OrderController.php';


//Parameter: index.php?controller=product&action=index
[$controllerName, $actionName, $id] = resolveRoute();

// Setze die Parameter in $_GET für Kompatibilität
$_GET['controller'] = $controllerName;
$_GET['action'] = $actionName;

// Setzt die Id, falls resolveRoute sie liefert
if($id !== null && $id !== '') {
    $_GET['id'] = $id;
}
else {
    unset($_GET['id']);
}

//Controller-Mapping
$routes = [
    'product'  => ProductController::class,
    'category' => CategoryController::class,
    'cart'     => CartController::class,
    'order'     => OrderController::class,
    'user'     => UserController::class,
    'rating'   => RatingController::class,
    'home'     => HomeController::class,
    'admin'    => AdminController::class
];

if (!isset($routes[$controllerName])) {
    http_response_code(404);
    die("Controller '$controllerName' nicht gefunden.");
}

$controllerClass = $routes[$controllerName];
$controller = new $controllerClass($db);

// Methode im Controller aufrufen
if (method_exists($controller, $actionName)) {

    //Sicherheitscheck: Nur öffentliche Methoden dürfen aufgerufen werden
    $ref = new ReflectionMethod($controller, $actionName);
    if(!$ref->isPublic()) {
        http_response_code(403);
        die("Aktion '$actionName' ist nicht öffentlich zugänglich.");
    }

    //Ruft Methode mit oder ohne Id-Parameter auf
    if (($id !== null && $id !== '') && $ref->getNumberOfParameters() >= 1) {
        $controller->{$actionName}($id);
    } else {
        $controller->{$actionName}();
    }

} else {
    http_response_code(404);
    die("Aktion '$actionName' existiert nicht im Controller.");
}
