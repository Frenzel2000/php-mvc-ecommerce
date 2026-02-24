<?php 

require_once 'src/models/ProductModel.php';
require_once 'src/views/ProductView.php';

abstract class Controller {


    protected ?array $currentUser = null; 
    protected array $globalData = [];
    protected $productModel;


    public function __construct($db)
    {
        //prüft ob session läuft
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->currentUser = $_SESSION['user'] ?? null;
        
        //zentrales Category Model, damit man auf jeder Seite die Kategorien für den header laden kann
        $categoryModel = new CategoryModel($db);
        
        //zentrales Product Model und erlaubt Filter Nutzung auf verschiedenen Seiten
        $this->productModel = new ProductModel($db);

        $this->globalData = [
            'navigation_categories' => $categoryModel->getAllWithProducts()
        ];
    }

    //Hilfsfunktion für Kindklassen um globale Kategorie-Daten mit lokalen Daten zu mergen
    protected function mergeData($localData = []) 
    {
        return array_merge($this->globalData, $localData);
    }

    //sendet JSON payload an view
    protected function renderJson($data = [], $statusCode = 200) 
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data, 
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    //Gibt Wert aus request-body zurück
    protected function getBody($name, $default = null)
    {
        //HTML Form POST
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        
        //JSON POST (JS)
        static $jsonInput = null; 
        if ($jsonInput == null) {
            $input = file_get_contents('php://input');
            $jsonInput = json_decode($input, true) ?? [];
        }

        if ($name == null) {
            return $jsonInput;
        }

        return $jsonInput[$name] ?? $default; 
    }

    //prüft ob user eingeloggt ist
    protected function requireLogin(): void
    {
        if ($this->currentUser === null) {
            
            //JSON Antwort bei POST request durch AJAX methode
            if ($this->isPost()) {   
                
                //setzt response code, damit response in JS mit durch die Prüfung geht
                http_response_code(401);

                echo json_encode([
                    'success' => false,
                    'loggedIn' => false,
                    'message' => 'Bitte melde dich an.',
                    'redirectURL' => BASE_URL . '/user/loginForm'
                ]);
                exit; 
            }

            //normaler redirect bei GET request
            $this->redirect(BASE_URL . '/user/loginForm');
            exit; 
        }
    }

   protected function denyAccessUnlessGranted(string $permission): void
   {
        if (!hasPermission($permission)) {

            http_response_code(403);

            // AJAX / POST → JSON
            if ($this->isPost()) {
                echo json_encode([
                    'success' => false,
                    'error' => 'forbidden',
                    'message' => 'Keine Berechtigung für diese Aktion.'
                ]);
                exit;
            }

            // GET → normale Fehlermeldung oder Redirect
            echo '403 Forbidden';
            exit;
        }
   }

    //AJAX Endpoint für Filter
    public function ajaxFilter() 
    {
        
        $catId = $_GET['id'] ?? null;
        $term = $_GET['term'] ?? null;
        

        $min = $_GET['min'] ?? 0;
        $max = $_GET['max'] ?? 9999;
        $offset = $_GET['offset'] ?? 0;
        $onlyAvailable = $_GET['available'] ?? 'false';
        $sortByBestseller = $_GET['bestseller'] ?? 'false';

        $limit = 6;
        
        $filter_params = [
            'catId' => $catId, 
            'term' => $term,
            'min' => $min,
            'max' => $max,
            'onlyAvailable' => $onlyAvailable,
            'sortByBestseller' => $sortByBestseller,
            'limit' => $limit,
            'offset' => $offset
        ];      

        $products = $this->productModel->getFilteredProducts($filter_params);
        echo ProductView::renderProductGrid($products);
        exit;
    }
}