<?php 

require_once 'Controller.php';
require_once 'src/models/CartModel.php';
require_once 'src/views/CartView.php';

class CartController extends Controller {

    private $model;
    private $view;

    //constructor erstellt CartModel und CartView instanzen
    public function __construct($db)
    {
        parent::__construct($db);
        $this->model = new CartModel($db);
        $this->view = new CartView();
        
        //warenkorb wird in der Session initialisiert
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    //lädt den Warenkorb eines users
    public function show()  
    {
        $this->requireLogin();

        //lädt Warenkorb aus der session
        $cartData = $this->model->resolveSessionCart($_SESSION['cart']);

        $title = 'Dein Warenkorb';
        $this->view->set_title($title);

        //fügt Cart spezifisches stylesheet hinzu
        $this->view->add_css('shopping_cart/shopping_cart.css');
        
        //fügt script in cart ein
        $this->view->add_js('cart/cart.js');

        $data = $this->mergeData([
            'cartItems' => $cartData['items'], 
            'total' => $cartData['total']
        ]);

        $this->view->render_html('cart', $data);
    }

    //Fügt Produkt zum Warenkorb hinzu
    public function add() 
    {
        header('Content-Type: application/json');
        
        $this->requireLogin();
        $this->denyAccessUnlessGranted('cart.manage');
        
        $userId = (int) $_SESSION['user']['user_id'];
        
        if ($this->isPost()) {
            $productId = $this->getBody('product_id');
            $productAmount = $this->getBody('product_amount');

            $sessionAmount = $_SESSION['cart'][$productId] ?? 0; 
            $newAmount = $sessionAmount + $productAmount;

            try {
                $this->model->checkStockLimit($productId, $newAmount);

                //aktualisiert Session
                $_SESSION['cart'][$productId] = $newAmount;

                $cartData = $this->model->resolveSessionCart($_SESSION['cart']);

                //sucht ein spezifisches Item für Einzelpreis 
                $currentItem = null;
                foreach ($cartData['items'] as $item) {
                    if ($item['product_id'] == $productId) {
                        $currentItem = $item; 
                        break; 
                    }
                }

                $itemTotal = $currentItem ? ($currentItem['price'] * $newAmount) : 0;
    

                $response = [
                    'success' => true, 
                    'message' => 'Produkt hinzugefügt', 
                    'cartAmount' => $newAmount,
                    'cartTotal' => $cartData['total'],
                    'cartItemTotal' => $itemTotal
                ];
                
                echo json_encode($response);
                exit;
            
            //behandelt StockExceededException 
            } catch (StockExceededException $e) {
                $errorMessage = $e->getMessage(); 
                $response = [
                    'sucess' => false, 
                    'message' => $errorMessage, 
                    'stockExceeded' => true, 
                    'stock' => $e->getAvailableStock()];
                echo json_encode($response);
                exit;
                
            //behandelt alle anderen Exceptions
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $response = ['success' => false, 'message' => 'Fehler beim hinzufügen: ' . $errorMessage];
                echo json_encode($response); 
                exit;
            }
        }
        else {
            echo json_encode(['success' => false, 'message' => 'Ungültiger Aufruf: POST-Anfrage erwartet.']);
            exit;        
        }
    }

    //entfernt Produkt aus dem Warenkorb
    public function remove() 
    {
        header('Content-Type: application/json');

        $this->requireLogin();
        $this->denyAccessUnlessGranted('cart.manage');

        $userId = (int) $_SESSION['user']['user_id'];
        if ($this->isPost()) {
            $productId = $this->getBody('product_id');
            try {

                $currentAmount = $_SESSION['cart'][$productId] ?? 0;

                if ($currentAmount > 0) {
                    $newAmount = $currentAmount - 1;
                    
                    if ($newAmount > 0) {
                        $_SESSION['cart'][$productId] = $newAmount;
                    } else {
                        unset($_SESSION['cart'][$productId]);
                    }
                }

                $cartData = $this->model->resolveSessionCart($_SESSION['cart']);
                $itemTotal = 0;
                $newCartAmount = 0;

                foreach ($cartData['items'] as $item) {
                    if ($item['product_id'] == $productId) {
                        $itemTotal = $item['price'] * $item['product_amount'];
                        $newCartAmount = $item['product_amount'];
                        break;
                    }
                }

                $cartEmpty = empty($_SESSION['cart']);

                $response = [
                    'success' => true, 
                    'message' => 'Produkt entfernt', 
                    'cartAmount' => $newCartAmount,
                    'cartTotal' => $cartData['total'],
                    'cartEmpty' => $cartEmpty,
                    'cartItemTotal' => $itemTotal
                ];

                echo json_encode($response);
                exit;
            } catch (\Exception $e) {
                $response = ['success' => false, 'message' => 'Fehler beim Entfernen: ' . $e->getMessage()];
                echo json_encode($response); 
                exit;
            }
        }
        else {
            echo json_encode(['success' => false, 'message' => 'Ungültiger Aufruf: POST-Anfrage erwartet.']);
            exit;        
        }
    }

    //speichert Cart tatsächlich in der Datenbank
    public function saveCart() 
    {
        $this->requireLogin();
        $userId = (int) $_SESSION['user']['user_id'];
        
        if (!empty($_SESSION['cart'])) {
            $this->model->syncCartToDatabase($userId, $_SESSION['cart']);
        }
    }
}