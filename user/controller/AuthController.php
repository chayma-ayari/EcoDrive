<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Handle registration
    public function register($data) {
        $nom = trim($data['nom']);
        $prenom = trim($data['prenom']);
        $telephone = trim($data['telephone']);
        $email = trim($data['email']);
        $adresse = trim($data['adresse']);
        $pw = trim($data['pw']);

        // Vérifie si l'utilisateur existe déjà
        $existingUser = $this->getUserByEmail($email);
        if ($existingUser) {
            $_SESSION['signup_error'] = 'Email déjà utilisé !';
            header('Location: /user/view/front/signup.php');
            exit();
        }

        $hashedPw = password_hash($pw, PASSWORD_BCRYPT);

        // Appel à la méthode de création
        $query = "INSERT INTO user (nom, prenom, telephone, email, adresse, pw) VALUES (:nom, :prenom, :telephone, :email, :adresse, :pw)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':pw', $hashedPw);

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
            $user = $this->readUserByEmail($email);
            
            if ($user && password_verify($password, $user['pw'])) {
                // Clear any existing error messages
                unset($_SESSION['login_error']);
                
                // Set user session data
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'is_admin' => (bool)$user['is_admin']
                ];
                
                // Redirect based on user role
                if ($user['is_admin']) {
                    header('Location: /user/view/back/admin_dashboard.php');
                } else {
                    header('Location: /user/view/back/user_dashboard.php');
                }
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

    // Read a user by email (accessible via une méthode publique)
    public function getUserByEmail($email) {
        return $this->readUserByEmail($email);
    }

    // Méthode privée pour lire un utilisateur par email
    public function readUserByEmail($email) {
        $query = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lister tous les utilisateurs
    public function listUser() {
        $query = "SELECT * FROM user ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listUserByLetter($letter) {
        $query = "SELECT * FROM user WHERE nom LIKE :letter ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':letter', $letter . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($userId) {
        $query = "DELETE FROM user WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function editUser($userId, $nom, $prenom, $telephone, $email, $adresse, $pw) {
        $query = "UPDATE user SET nom = :nom, prenom = :prenom, telephone = :telephone, email = :email, adresse = :adresse";

        if (!empty($pw)) {
            $hashedPw = password_hash($pw, PASSWORD_BCRYPT);
            $query .= ", pw = :pw";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        if (!empty($pw)) {
            $stmt->bindParam(':pw', $hashedPw);
        }
        $stmt->bindParam(':id', $userId);

        return $stmt->execute();
    }

    public function getRecentUser() {
        $query = "SELECT * FROM user ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchUser($term) {
        $query = "SELECT * FROM user WHERE nom LIKE :term OR prenom LIKE :term OR email LIKE :term ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':term', '%' . $term . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProfilePicture($userId, $imagePath) {
        $query = "UPDATE user SET profile_picture = :profile_picture WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':profile_picture', $imagePath);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    // Check if user is admin
    public static function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['is_admin'];
    }

    // Logout user
    public static function logout() {
        session_destroy();
        header('Location: /user/view/front/login.php');
        exit();
    }

    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM user";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTotalAdmins() {
        $sql = "SELECT COUNT(*) as total FROM user WHERE is_admin = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getUsersWithPhone() {
        $sql = "SELECT COUNT(*) as total FROM user WHERE telephone IS NOT NULL AND telephone != ''";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getUsersWithAddress() {
        $sql = "SELECT COUNT(*) as total FROM user WHERE adresse IS NOT NULL AND adresse != ''";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updateAdminPassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        // Verify current password
        $user = $this->getUserById($userId);
        if (!$user || !password_verify($currentPassword, $user['pw'])) {
            return false;
        }

        // Validate new password
        if ($newPassword !== $confirmPassword || strlen($newPassword) < 8) {
            return false;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET pw = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
