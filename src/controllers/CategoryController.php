<?php

require_once 'Controller.php';

require_once 'src/models/CategoryModel.php';
require_once __DIR__ . '/../views/CategoryView.php';
require_once __DIR__ . '/../views/ProductView.php';


class CategoryController extends Controller
{
    private $view;
    private $model;

    public function __construct($db)
    {
        //Konstruktor wird weitergereicht, damit base Controller das Category Model laden kann
        parent::__construct($db);

        $this->model = new CategoryModel($db);
        $this->view = new CategoryView();
    }

    //lädt Kategorie Übersicht
    public function show($id)
    {
        $category = $this->model->getByID($id);
        $products = $this->model->getProductsByCategory($id);
        
        $this->view->add_js('filter/filter.js');
        $this->view->add_js('cart/cart.js');

        
        $data = $this->mergeData([
            'category' => $category,
            'products' => $products
        ]);
        $this->view->render_html('show', $data);
    }
}