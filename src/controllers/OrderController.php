<?php 

require_once 'Controller.php';
require_once 'src/models/OrderModel.php';
require_once 'src/models/UserModel.php';
require_once 'src/models/CartModel.php';
require_once 'src/views/OrderView.php';
require_once 'src/Core/Exceptions/StockExceededException.php';

class OrderController extends Controller {
    
    private $model; 
    private $userModel;
    private $cartModel; 
    private $view; 
    
    public function __construct($db)
    {
        parent::__construct($db);
        $this->model = new OrderModel($db);
        $this->userModel = new UserModel($db);
        $this->cartModel = new CartModel($db);

        $this->view = new OrderView();
    }

    //lädt die Checkout Seite mit allen wichtigen Informationen
    public function checkout() {
        
        $this->view->add_css('shopping_cart/checkout.css');

        //prüft ob der Warenkorb überhaupt Produkte enthält
        $sessionCart = $_SESSION['cart'] ?? [];
        if (empty($sessionCart)) {
            $this->redirect(BASE_URL . '/cart/show');
            return;
        }

        //holt Warenkorb Daten aus Session
        $cartData = $this->cartModel->resolveSessionCart($sessionCart);
        
        //Formular placeholder Array
        $prefillData = [
            'first_name' => '', 'last_name' => '', 'email' => '',
            'street' => '', 'house_number' => '', 'zip_code' => ''
        ];

        //holt Daten zum User, wenn angemeldet 
        if ($this->currentUser) {
            $userData = $this->userModel->getByIDWithAddress($this->currentUser['user_id']);
            
            if ($userData) {
                $prefillData = $userData; 
            }
        }
    
        //rendert die checkout Seite
        $this->view->set_title("Checkout");
        $this->view->render_html('render_checkout', [
            'cartItems' => $cartData['items'],
            'cartTotal' => $cartData['total'],
            'user' => $prefillData,
            'loggedIn' => ($this->currentUser !== null)
        ]);
    }

    //macht die Bestellung wirksam 
    public function placeOrder() {


        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/order/checkout');
            return;
        }

        $input = $_POST;;

        //prüft ob der Warenkorb überhaupt Produkte enthält
        $sessionCart = $_SESSION['cart'] ?? [];
        if (empty($sessionCart)) {
            $this->redirect(BASE_URL . '/cart/show');
            return;
        }

        $cartData = $this->cartModel->resolveSessionCart($sessionCart);
        $userId = $this->currentUser['user_id'] ?? null;

        //Baut die Daten-Arrays für das Model

        $userData = [
            'user_id'    => $userId,
            'email'      => $input['email'], 
            'first_name' => $input['first_name'],
            'last_name'  => $input['last_name']
        ];

        $addressData = [
            'street'       => $input['street'],
            'house_number' => $input['house_number'],
            'zip_code'     => $input['zip_code']
        ];

        //verschachteltes Array, was alle Informationen an das Model weitergibt
        $orderData = [
            'user_data'    => $userData,
            'address_data' => $addressData,
            'cart_items'   => $cartData['items']
        ];

        try {
            //orderID für redirect
            $orderId = $this->model->createOrder($orderData);
            
            //leert Session Warenkorb
            $_SESSION['cart'] = [];

            //redirect zur sucess Seite
            $this->redirect(BASE_URL . '/order/success/' . $orderId);

        } catch (StockExceededException $e) {
            $productName = urlencode($e->getMessage());

            //Produkte, die Lagerbestand überschreiten werden in Session gespeichert
            $_SESSION['stock_errors'] = $e->getFailedProducts();

            //redirect zur Fehlerseite mit spezifischem Fehler
            $this->redirect(BASE_URL . '/order/error?reason=stock');

        } catch (Exception $e) {

            //redirect zur Fehlerseite mit allgemeinem Fehler
            $this->redirect(BASE_URL . '/order/error?reason=general');
        }
    }

    //lädt success Seite
    public function success() {

        $this->view->add_css('shopping_cart/order_success.css');

        $id = $_GET['id'] ?? 0;
        $this->view->set_title("Bestellung erfolgreich");
        $this->view->render_html('render_success', ['order_id' => $id]);
    }

    //lädt Fehlerseite
    public function error() {

        $this->view->add_css('shopping_cart/checkout_error.css');
        $this->view->set_title('Fehler'); 

        $reason = htmlspecialchars($_GET['reason'] ?? 'general');
        $failedProducts = $_SESSION['stock_errors'] ?? [];

        $errorData = [
            'reason' => $reason,
            'failed_products' => $failedProducts, 
            'user_id' => $this->currentUser['user_id'],
        ];

        $this->view->render_html('render_error', $errorData);
    }
}