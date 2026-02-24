<?php

require_once 'src/models/UserModel.php';
require_once 'src/models/OrderModel.php';
require_once 'src/views/mainView.php';
require_once 'src/views/UserView.php';
require_once 'src/models/CartModel.php';
require_once __DIR__.'/helpers/Auth.php';


class UserController extends Controller
{
    private $userModel;
    private $cartModel; 
    private $view;
    private $orderModel;
    private $ratingModel;

    //constructor erstellt UserModel und UserView instanzen
    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new UserModel($db);

        //Cart Model Instanz um Warenkorb beim Logout zu synchronisieren
        $this->cartModel = new CartModel($db);

        $this->view = new UserView();
        $this->orderModel = new OrderModel($db);
        $this->ratingModel = new RatingModel($db);
    }

    //rendert login Formular
    public function loginForm()
    {
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->view->render_html('login', $this->mergeData([
            'error' => $error
        ]));
    }


    //render registration Formular
    public function registerForm()
    {
        $this->view->add_js('user/register_password_match.js');
        $this->view->render_html('register');
    }
    //render forgot password form
    public function forgotPasswordForm()
    {
        $this->view->render_html('forgot_password');
    }


    /*login-Logik
    TODO: AJAX Funktionalität für bessere User Experience einfügen
    */
    public function login()
    {
        $email = $this->getBody('email');
        $password = $this->getBody('password');

        $user = $this->userModel->getByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            
            unset($_SESSION['flash_error']);

            //Permissions des Benutzers holen (Array)
            $permissionsRaw = $this->userModel->getPermissionsByUserId($user['user_id']);

            $permissions = array_column($permissionsRaw, 'permission_key');
            //login erfolgreich
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email']
            ];

            //Permissions speichern (wichtig)
            $_SESSION['permissions'] = $permissions;

            //lädt Warenkorb aus der Datenbank
            $savedCart = $this->cartModel->loadCartFromDatabase($user['user_id']);

            //holt session Warenkorb
            $currentSessionCart = $_SESSION['cart'] ?? [];

            //fügt session Warenkorb und Warenkorb aus der Datenbank zusammen
            foreach ($savedCart as $productId => $amount) {
                if (isset($currentSessionCart[$productId])) {
                    $currentSessionCart[$productId] += $amount;
                } else {
                    $currentSessionCart[$productId] = $amount;
                }
            }

            //updated Session Warenkorb
            $_SESSION['cart'] = $currentSessionCart;

            //redirect zur Homepage 
            $this->redirect(BASE_URL );
            return;
        } 
        //wenn Passwort oder Email falsch, speichern wir eine error Nachricht, die wir dann ausgeben
        $_SESSION['flash_error'] = "Email oder Passwort ist falsch";
        $this->redirect(BASE_URL . '/user/loginForm');
    }

    /*Registrierungs-Logik
    TODO: AJAX Funktionalität für bessere User Experience einfügen
    (Email already registered abfangen)
    */
    public function register()
    {
        $firstName = $this->getBody('first_name');
        $lastName = $this->getBody('last_name');
        $email = $this->getBody('email');

        $password = $this->getBody('password');
        $repeatPassword = $this->getBody('repeat_password');

        $addressStreet = $this->getBody('street');
        $addressHouseNumber = $this->getBody('house_number');
        $addressZipCode = $this->getBody('zip_code');

        //Prüft, ob bereits ein Benutzer diese Email hat 
        if ($this->userModel->getByEmail($email)) {
            $this->redirect(BASE_URL . '/user/registerForm?error=email_exists');
            return;
        }

        if (empty($password) || empty($repeatPassword) || $password !== $repeatPassword) {
            $this->redirect(BASE_URL . '/user/registerForm?error=pw_mismatch');
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        //bereitet Daten für Model vor
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password_hash' => $passwordHash,
            'street' => $addressStreet,
            'house_number' => $addressHouseNumber,
            'zip_code' => $addressZipCode
        ];

        //legt Nutzer an und leitet zum Login weiter
        $this->userModel->createUser($data);
        $this->redirect(BASE_URL . '/user/loginForm');
    }

    //beendet die Session des Nutzers
    public function logout()
    {
        $userId = $_SESSION['user']['user_id'] ?? null;
        $sessionCart = $_SESSION['cart'] ?? [];
        
        //synchronisiert Warenkorb mit der Datenbank bevor session zerstört wird
        $this->cartModel->syncCartToDatabase($userId, $sessionCart);
        
        session_unset();
        session_destroy(); 
        $this->redirect(BASE_URL . '/user/loginForm');
    }

    //verarbeitet Passwort vergessen Anfrage
    public function forgotPassword()
    {
        $email = $this->getBody('email');

        $user = $this->userModel->getByEmail($email);
        
        if ($user) {
            //Token und Ablaufzeit erstellen
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            //Token in der Datenbank speichern
            $this->userModel->createPasswordResetToken(
                $user['user_id'],
                $token,
                $expiresAt
                );

            // Redirect auf Reset-Formular mit Token in der URL 
            // (In echten Systemen wäre das ein Link in einer Email)
            $this->redirect(BASE_URL . "/user/resetPasswordForm?token=$token");
    }

    // falls E-Mail nicht existiert → zurück zum Login
    $this->redirect(BASE_URL . '/user/loginForm');
    }

    //zeigt Formular "Neues Passwort setzen" an
    public function resetPasswordForm()
    {
        //Token aus URL holen
        $token = $_GET['token'] ?? null;

        //Token validieren
        if (!$this->userModel->isValidResetToken($token)) {
            echo 'Ungültiger oder abgelaufener Token';
            exit;
        }
        //Formular rendern
        $this->view->render_html('reset_password', ['token' => $token]);
    }
    //verarbeitet Formular "Neues Passwort setzen"
    public function resetPassword()
    {
        //Token aus Body holen 
        $token = $this->getBody('token');
        //Neues Passwort aus Body holen
        $newPassword = $this->getBody('password');
        $repeat = $this->getBody('repeat_password');

        //Token validieren
        $userId = $this->userModel->getUserIdByResetToken($token);
        if (!$userId) {
            $this->renderJson(['error' => 'Invalid token'], 400);
            return;
            }

        if ($newPassword !== $repeat) {
        $this->redirect(BASE_URL . '/user/resetPasswordForm?token=' . urlencode($token) . '&error=pw_mismatch');
        return;
    }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        //Passwort in DB aktualiseren und Token löschen
        $this->userModel->updatePassword($userId, $hash);
        $this->userModel->deleteResetToken($token);

        $this->redirect(BASE_URL . '/user/loginForm');
    }
    public function profile()
    {
        $this->requireLogin();
        $this->view->add_css(BASE_URL . '/static/css/user/profile.css');
        if(!isset($_SESSION['user'])){
            $this->redirect(BASE_URL . '/user/loginForm');
            return;
        }
        $userId = $_SESSION['user']['user_id'];


        $userData = $this->userModel->getByIDWithAddress($userId);
        $ratings = $this->ratingModel->getRatingsByUserId($userId);
        $orders = $this->orderModel->getOrderByUserId($userId);

        $this->view->render_html('profile', $this->mergeData([
            'userData' => $userData,
            'ratings' => $ratings,
            'orders' => $orders,
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null
        ]));
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }
    public function updateUser()
    {
        $this->requireLogin();
        $userId = $_SESSION['user']['user_id'] ?? null;
        if (!$userId) return;

        $data = [
            'first_name' => $this->getBody('first_name'),
            'last_name'  => $this->getBody('last_name'),
            'email'      => $this->getBody('email'),
            'street'     => $this->getBody('street'),
            'house_number' => $this->getBody('house_number'),
            'zip_code'   => $this->getBody('zip_code')
        ];

        try {
            $this->userModel->updateUserWithAddress($userId, $data);
            $_SESSION['flash_success'] = "Profil erfolgreich aktualisiert.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Fehler beim Aktualisieren." ;
        }

        $this->redirect(BASE_URL . '/user/profile');
    }

    public function deleteAccount()
    {
        $this->requireLogin();
        $userId = $_SESSION['user']['user_id'] ?? null;
        if (!$userId) return;

        // Nutzt deine bestehende deleteUser Logik, die auch Adressen und Rollen aufräumt
        $this->userModel->deleteUser($userId);

        // Nach dem Löschen ausloggen
        session_destroy();
        $this->redirect(BASE_URL . '/');
    }
}