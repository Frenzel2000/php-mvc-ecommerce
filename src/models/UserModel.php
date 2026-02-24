<?php

require_once 'Model.php';

class UserModel extends Model {

    function __construct($db)
    {
        parent::__construct($db);
    }

    //holt alle Benutzer 
    public function getAll() 
    {
        $sql = "SELECT * FROM Users";
        
        #prepared statement (gegen SQL injection)
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //holt Benutzer nach ID
    public function getByID($id)
    {
        $sql = "SELECT * FROM Users WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    //holt Benutzer nach email
    public function getByEmail($email)
    {
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    //legt neuen Benutzer an
    public function createUser(array $data)
    {
        $pdo=$this->conn->getConnection();

        try {


            //Transaction damit keine Inkonsistenzen zwischen Users und Addresses entstehen
            $pdo->beginTransaction();

            //Adresse anlegen
            $sqlAddress = "INSERT INTO Addresses
                        (house_number, street, zip_code)
                        VALUES
                        (:house_number, :street, :zip_code)";
            $stmtAddr = $this->conn->prepare($sqlAddress);
            $stmtAddr->bindValue(':house_number', $data['house_number'] ?? null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':street', $data['street'] ?? null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':zip_code', $data['zip_code'] ?? null, PDO::PARAM_STR);
            
            $stmtAddr->execute();

            //Adress ID aus neu angelegter Zeile holen
            $adressId = (int) $this->conn->lastInsertId();
            
            //User Anlegen
            $sqlUser = "INSERT INTO Users
                        (first_name, last_name, email, password_hash, address_id)
                        VALUES
                        (:first_name, :last_name, :email, :password_hash, :address_id)";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->bindValue(':first_name', $data['first_name'], PDO::PARAM_STR);
            $stmtUser->bindValue(':last_name',  $data['last_name'],  PDO::PARAM_STR);
            $stmtUser->bindValue(':email',      $data['email'],      PDO::PARAM_STR);
            $stmtUser->bindValue(':password_hash', $data['password_hash'], PDO::PARAM_STR);
            $stmtUser->bindValue(':address_id', $adressId, PDO::PARAM_INT);
            $stmtUser->execute();
        
           $userId = (int) $this->conn->lastInsertId();
           
            $roleName = $data['role_name'] ?? 'user';
            if ($roleName === '') $roleName = 'user';

                // Rolle zuweisen (aus Formular)
                $sqlRole = "INSERT INTO User_Roles (user_id, role_id)
                            SELECT :user_id, role_id
                            FROM Roles
                            WHERE role_name = :role_name
                            LIMIT 1";

                $stmtRole = $this->conn->prepare($sqlRole);
                $stmtRole->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $stmtRole->bindValue(':role_name', $roleName, PDO::PARAM_STR);
                $stmtRole->execute();


            $pdo->commit();
            
        } catch (PDOException $e) {
            //rollback bei Fehler
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo "Error: " . $e->getMessage();
        }
    }


    //holt Rollen eines Users
    public function getRolesByUserId($id)
    {
        $sql = "SELECT r.role_name 
        FROM Roles r JOIN User_Roles ur ON r.role_id = ur.role_id 
        WHERE ur.user_id = :id";

        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    //holt Berechtigungen eines Users

    public function getPermissionsByUserId($id)
    {
        $sql = "SELECT DISTINCT p.permission_key
        FROM Permissions p 
        JOIN Role_Permissions rp ON p.permission_id = rp.permission_id 
        JOIN User_Roles ur ON rp.role_id = ur.role_id 
        WHERE ur.user_id = :id";

        $stmt = $this->conn->prepare($sql); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    //erstellt passwort reset token für einen User
    public function createPasswordResetToken($userId, $token, $expiresAt) 
    {
        $sql = "INSERT INTO password_resets (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', $expiresAt, PDO::PARAM_STR);

        $stmt->execute();
    }

    //prüft ob der reset token gültig ist

    public function isValidResetToken($token)
    {
        $sql = "
            SELECT 1
            FROM password_resets
            WHERE token = :token
            AND expires_at > NOW()
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    //holt den User basierend auf dem reset token
    public function getUserIdByResetToken($token)
    {
        $sql = "
            SELECT user_id
            FROM password_resets
            WHERE token = :token
            AND expires_at > NOW()
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        $userId = $stmt->fetchColumn();

        return $userId !== false ? (int)$userId : null;
    }

    //ändert das Passwort eines Users in der Datebank 
    public function updatePassword($userId, $passwordHash)
    {
        $sql = "
            UPDATE Users
            SET password_hash = :password_hash
            WHERE user_id = :user_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->execute();
    }

    //löscht den reset token eines Users
    public function deleteResetToken($token)
    {
        $sql = "
            DELETE FROM password_resets
            WHERE token = :token
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
    }

    // liefert User für user_manager 
    public function getAllWithAddress(): array
    {
        $sql = "
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                a.street,
                a.house_number,
                a.zip_code,
                COALESCE(GROUP_CONCAT(r.role_name ORDER BY r.role_name SEPARATOR ', '), '') AS roles
            FROM Users u
            JOIN Addresses a ON a.address_id = u.address_id
            LEFT JOIN User_Roles ur ON ur.user_id = u.user_id
            LEFT JOIN Roles r ON r.role_id = ur.role_id
            GROUP BY 
                u.user_id, u.first_name, u.last_name, u.email,
                a.street, a.house_number, a.zip_code
            ORDER BY u.user_id ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //lädt User und seine Adresse für Form Felder des user_managers
    public function getByIDWithAddress(int $id)
    {
        $sql = "
            SELECT u.user_id, u.first_name, u.last_name, u.email, u.address_id,
                a.street, a.house_number, a.zip_code
            FROM Users u
            JOIN Addresses a ON a.address_id = u.address_id
            WHERE u.user_id = :id
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //aktualisiert den User, wenn user_manager in verändert hat
    public function updateUserWithAddress(int $userId, array $data)
    {
        $pdo = $this->conn->getConnection();
        $pdo->beginTransaction();

        try {
            $user = $this->getByID($userId);
            if (!$user) throw new Exception("User not found");

            $addressId = (int)$user['address_id'];

            $sqlUser = "
                UPDATE Users
                SET first_name = :first_name,
                    last_name  = :last_name,
                    email      = :email
                    " . (isset($data['password_hash']) ? ", password_hash = :password_hash" : "") . "
                WHERE user_id = :user_id
            ";

            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->bindValue(':first_name', $data['first_name'], PDO::PARAM_STR);
            $stmtUser->bindValue(':last_name',  $data['last_name'],  PDO::PARAM_STR);
            $stmtUser->bindValue(':email',      $data['email'],      PDO::PARAM_STR);
            $stmtUser->bindValue(':user_id',    $userId,             PDO::PARAM_INT);
            if (isset($data['password_hash'])) {
                $stmtUser->bindValue(':password_hash', $data['password_hash'], PDO::PARAM_STR);
            }
            $stmtUser->execute();

            $sqlAddr = "
                UPDATE Addresses
                SET street = :street,
                    house_number = :house_number,
                    zip_code = :zip_code
                WHERE address_id = :address_id
            ";
            $stmtAddr = $this->conn->prepare($sqlAddr);
            $stmtAddr->bindValue(':street', $data['street'] ?: null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':house_number', $data['house_number'] ?: null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':zip_code', $data['zip_code'] ?: null, PDO::PARAM_STR);
            $stmtAddr->bindValue(':address_id', $addressId, PDO::PARAM_INT);
            $stmtAddr->execute();

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    //löscht User und abhängige 
    public function deleteUser(int $userId)
    {
        $pdo = $this->conn->getConnection();
        $pdo->beginTransaction();

        try {
            $user = $this->getByID($userId);
            if (!$user) { $pdo->rollBack(); return; }
            $addressId = (int)$user['address_id'];

            // FKs aufräumen
            $stmt = $this->conn->prepare("DELETE FROM User_Roles WHERE user_id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->conn->prepare("DELETE FROM Ratings WHERE user_id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->conn->prepare("DELETE FROM Cart WHERE user_id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Orders: entkoppeln (user_id ist NULLable)
            $stmt = $this->conn->prepare("UPDATE Orders SET user_id = NULL WHERE user_id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $stmtUser = $this->conn->prepare("DELETE FROM Users WHERE user_id = :id");
            $stmtUser->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmtUser->execute();

            $stmtAddr = $this->conn->prepare("DELETE FROM Addresses WHERE address_id = :aid");
            $stmtAddr->bindValue(':aid', $addressId, PDO::PARAM_INT);
            $stmtAddr->execute();

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    //liefert alle Rollen für bspw. user_manager Formular
    public function getAllRoles(): array
    {
        $sql = "SELECT role_id, role_name FROM Roles ORDER BY role_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //setzt veränderte User Rolle 
    public function setSingleRole(int $userId, string $roleName): void
    {
        $pdo = $this->conn->getConnection();
        $pdo->beginTransaction();

        try {
            // alte Rollen entfernen
            $stmtDel = $this->conn->prepare("DELETE FROM User_Roles WHERE user_id = :uid");
            $stmtDel->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmtDel->execute();

            // neue Rolle setzen
            $stmtIns = $this->conn->prepare("
                INSERT INTO User_Roles (user_id, role_id)
                SELECT :uid, role_id
                FROM Roles
                WHERE role_name = :rname
                LIMIT 1
            ");
            $stmtIns->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmtIns->bindValue(':rname', $roleName, PDO::PARAM_STR);
            $stmtIns->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}