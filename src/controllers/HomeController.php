<?php

require_once 'Controller.php';
require_once 'src/models/ProductModel.php';
require_once 'src/models/CategoryModel.php';
require_once 'src/views/HomeView.php';

class HomeController extends Controller {

    private $categoryModel;
    private $view;

    public function __construct($db) {
        parent::__construct($db);

        $this->categoryModel = new CategoryModel($db);

        $this->view = new HomeView();
    }

    //lädt home-Seite
    public function index() {
        $title = "Home PowerPure";
      
        $this->view->set_title($title);
        $this->view->add_js('cart/cart.js');

        $data = $this->mergeData([
            'bestsellers' => $this->productModel->getBestsellers(4),
            'categories'  => $this->categoryModel->getAll()
        ]);

        $this->view->render_html('index', $data);
    }
}