<?php

require_once 'Controller.php';
require_once 'src/models/ProductModel.php';
require_once 'src/views/ProductView.php';
require_once 'src/models/CategoryModel.php';
require_once 'src/models/RatingModel.php';
class ProductController extends Controller {

    private $categoryModel;
    private $ratingModel;
    private $view;

    public function __construct($db) {
        
        parent::__construct($db); 
        
        //lädt Models
        $this->categoryModel = new CategoryModel($db);
        $this->ratingModel = new RatingModel($db);
        
        //lädt View
        $this->view = new ProductView();
        $this->view->add_js('cart/cart.js');

    }

    //lädt Daten aus request-body und legt neues Produkt an
    public function store()
    {
        //Berechtigung prüfen
        $this->denyAccessUnlessGranted('product.create');

        //Daten aus dem Request-Body holen (HTML-Formular ODER JSON)
        $data = [
            'product_name'        => $this->getBody('product_name'),
            'price'       => $this->getBody('price'),
            'category_id' => $this->getBody('category_id'),
            'inventory'   => $this->getBody('inventory'),
            'flavour'     => $this->getBody('flavour'),
            'size'        => $this->getBody('size'),
            'description_short' => $this->getBody('description_short'),
            'description_long' => $this->getBody('description_long'),
            'asset_path'  => $this->getBody('asset_path')
        ];

        //Neues Produkt in der Datenbank anlegen
        $this->productModel->create($data);
    }

    //entfernt Produkt
    public function remove()
    {

        $this->denyAccessUnlessGranted('product.delete');

        $productId = $this->getBody('product_id');

        $this->productModel->remove($productId);
    }

    //empfängt ein search keyword und gibt Suchergebnisse aus Datenbank zurück
    public function processSearch() 
    {
        $rawKeyword = $_GET['term'];
        $keyword = trim($rawKeyword);

        if (!$keyword) {
            $response = ['productsFound' => false]; 
            exit;
        }

        $data = $this->productModel->searchProducts($keyword);
        $response = ['productsFound' => true, 'data' => $data];
        echo json_encode($response); 
        exit;
    }


    public function show()
    {
        //Produkt validieren
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die('Ungültige Produkt-ID');
        }
        $productId = (int) $_GET['id'];

        $product = $this->productModel->getByID($productId);
        $ratingScore = $this->ratingModel->getRatingScore($productId);
        $ratingList = $this->ratingModel->getFullRating($productId);

        if ($product === false) {
            die('Produkt nicht gefunden');
        }

        $title = 'Produkt: ' . $product['product_name'];
        $this->view->set_title($title);
        
        //lädt eigenes Stylesheet für die Produktseite
        $this->view->add_css('review_product/review_product.css');

        //merged Daten mit Header-Daten
        $data = $this->mergeData([
            'product' => $product,
            'rating_score' => $ratingScore,
            'rating_list'  => $ratingList
        ]);

        $this->view->render_html('detail', $data);
    }

    //zeigt die Suchergebnisse auf eigener Seite an
    public function showSearch()
    {
        $this->view->add_js('filter/filter.js');

        $term = $_GET['term'] ?? '';
        $products = $this->productModel->searchProductsFull($term);
        
        $title = empty($term) ? 'Alle Produkte' : 'Suche: ' . $term;
        $this->view->set_title($title);

        $data = $this->mergeData([
            'products' => $products,
            'searchTerm' => $term
        ]);

        $this->view->render_html('searchResults', $data);
    }
}