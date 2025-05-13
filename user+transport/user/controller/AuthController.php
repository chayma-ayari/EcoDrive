<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Handle registration
    public function register($data) {
        $nom = trim($data['nom']);
        $prenom = trim($data['prenom']);
        $telephone = trim($data['telephone']);
        $email = trim($data['email']);
        $adresse = trim($data['adresse']);
        $is_admin = isset($data['is_admin']) ? 1 : 0;

        // Hash the password
        $hashedPw = password_hash($data['pw'], PASSWORD_DEFAULT);

        // Appel à la méthode de création
        $query = "INSERT INTO user (nom, prenom, telephone, email, adresse, pw, is_admin) VALUES (:nom, :prenom, :telephone, :email, :adresse, :pw, :is_admin)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':pw', $hashedPw);
        $stmt->bindParam(':is_admin', $is_admin);

        if ($stmt->execute()) {
            $_SESSION['signup_success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            header('Location: /user/view/front/login.php');
            exit();
        } else {
            $_SESSION['signup_error'] = 'Erreur lors de l\'inscription.';
            header('Location: /user/view/front/signup.php');
            exit();
        }
    }

    // Handle login
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Veuillez remplir tous les champs.';
            header('Location: /user/view/front/login.php');
            exit();
        }

        try {
            $user = $this->getUserByEmail($email);

            if ($user && password_verify($password, $user['pw'])) {
                // Clear any existing error messages
                unset($_SESSION['login_error']);

                // Use History model to log login event
                require_once __DIR__ . '/../model/History.php';
                $historyModel = new History($this->conn);
                $logResult = $historyModel->logLogin($user['id'], $user['email']);
                if (!$logResult) {
                    error_log("Login history insert failed for user ID: " . $user['id']);
                }

                // Set user session data
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'profile_picture' => !empty($user['profile_picture']) ? $user['profile_picture'] : '/user/view/back/assets/img/find_user.png',
                    'is_admin' => (bool)$user['is_admin']
                ];

                // Redirect based on user role
                // Redirect all users to user_dashboard.php regardless of admin status
                header('Location: /user/view/back/user_dashboard.php');
                exit();
            } else {
                $_SESSION['login_error'] = 'Email ou mot de passe invalide.';
                header('Location: /user/view/front/login.php');
                exit();
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['login_error'] = 'Une erreur est survenue. Veuillez réessayer.';
            header('Location: /user/view/front/login.php');
            exit();
        }
    }

    // Read a user by email
    public function getUserByEmail($email) {
        try {
            $query = "SELECT * FROM user WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get user by email error: " . $e->getMessage());
            return false;
        }
    }

    // Lister tous les utilisateurs
    public function listUser() {
        try {
            $query = "SELECT * FROM user ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("List users error: " . $e->getMessage());
            return [];
        }
    }

    public function listUserByLetter($letter) {
        $query = "SELECT * FROM user WHERE nom LIKE :letter ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':letter', $letter . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserById($id) {
        $query = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteUser($userId) {
        try {
            // Vérifier si l'utilisateur existe
            $checkQuery = "SELECT id FROM user WHERE id = :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() === 0) {
                return false; // L'utilisateur n'existe pas
            }
            
            // Supprimer l'utilisateur
            $deleteQuery = "DELETE FROM user WHERE id = :id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindValue(':id', $userId, PDO::PARAM_INT);
            
            return $deleteStmt->execute();
            
        } catch(PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    public function editUser($id, $nom, $prenom, $telephone, $email, $adresse) {
        try {
            $query = "UPDATE user SET 
                nom = :nom,
                prenom = :prenom,
                telephone = :telephone,
                email = :email,
                adresse = :adresse
                WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":nom", $nom);
            $stmt->bindParam(":prenom", $prenom);
            $stmt->bindParam(":telephone", $telephone);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":adresse", $adresse);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Edit user error: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentUsers() {
        try {
            $query = "SELECT * FROM user ORDER BY id DESC LIMIT 5";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get recent users error: " . $e->getMessage());
            return [];
        }
    }

    public function searchUser($term) {
        try {
            $query = "SELECT * FROM user WHERE nom LIKE :term OR prenom LIKE :term OR email LIKE :term";
            $stmt = $this->conn->prepare($query);
            $likeTerm = '%' . $term . '%';
            $stmt->bindParam(":term", $likeTerm, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Search user error: " . $e->getMessage());
            return [];
        }
    }

    public function listUserByIdAsc() {
        try {
            $query = "SELECT * FROM user ORDER BY id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("List users by ID ascending error: " . $e->getMessage());
            return [];
        }
    }

    public function updateProfilePicture($userId, $imagePath) {
        try {
            $query = "UPDATE user SET profile_picture = :image WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":image", $imagePath);
            $stmt->bindParam(":id", $userId);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update profile picture error: " . $e->getMessage());
            return false;
        }
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    // Check if user is admin
    public function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['is_admin'];
    }

    // Logout user
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /user/view/front/login.php');
        exit();
    }

    public function getTotalUsers() {
        try {
            $query = "SELECT COUNT(*) as total FROM user";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Get total users error: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalAdmins() {
        try {
            $query = "SELECT COUNT(*) as total FROM user WHERE is_admin = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Get total admins error: " . $e->getMessage());
            return 0;
        }
    }

    public function getUsersWithPhone() {
        try {
            $query = "SELECT COUNT(*) as total FROM user WHERE telephone IS NOT NULL AND telephone != ''";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Get users with phone error: " . $e->getMessage());
            return 0;
        }
    }

    public function getUsersWithAddress() {
        try {
            $query = "SELECT COUNT(*) as total FROM user WHERE adresse IS NOT NULL AND adresse != ''";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Get users with address error: " . $e->getMessage());
            return 0;
        }
    }

    public function updateAdminPassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        try {
            // Vérifier si l'utilisateur existe et est un admin
            $user = $this->findUserById($userId);
            if (!$user || !$user['is_admin']) {
                return false;
            }

            // Vérifier le mot de passe actuel
            if (!password_verify($currentPassword, $user['pw'])) {
                return false;
            }

            // Vérifier que les nouveaux mots de passe correspondent
            if ($newPassword !== $confirmPassword) {
                return false;
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET pw = :password WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'password' => $hashedPassword,
                'id' => $userId
            ]);

        } catch(PDOException $e) {
            error_log("Update admin password error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $query = "SELECT * FROM user WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return null;
        }
    }

    public function updateUser($userData) {
        $id = $userData['id'];
        $fields = [];
        $params = [];

        // Construire la requête dynamiquement
        foreach (['nom', 'prenom', 'email', 'telephone', 'adresse'] as $field) {
            if (isset($userData[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $userData[$field];
            }
        }

        // Gérer le mot de passe séparément
        if (isset($userData['pw'])) {
            $fields[] = "pw = :pw";
            $params['pw'] = password_hash($userData['pw'], PASSWORD_DEFAULT);
        }

        $params['id'] = $id;

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE user SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($params);
    }

    // Add updatePassword method to support password reset
    public function updatePassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query = "UPDATE user SET pw = :pw WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':pw', $hashedPassword);
            $stmt->bindParam(':id', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update password error: " . $e->getMessage());
            return false;
        }
    }
}
?>
