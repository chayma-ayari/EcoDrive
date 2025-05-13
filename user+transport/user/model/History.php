<?php
class History {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function logLogin($userId, $email) {
        try {
            $query = "INSERT INTO history (user_id, email, login_time) VALUES (:user_id, :email, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':email' => $email
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Failed to log login: " . $e->getMessage());
            return false;
        }
    }

    public function getLoginHistory($limit = 100) {
        try {
            $query = "SELECT * FROM history ORDER BY login_time DESC LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to get login history: " . $e->getMessage());
            return [];
        }
    }

    public function getLoginHistoryCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM history";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Failed to get login history count: " . $e->getMessage());
            return 0;
        }
    }

    public function getLoginHistoryPaginated($limit, $offset) {
        try {
            $query = "SELECT * FROM history ORDER BY login_time DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to get paginated login history: " . $e->getMessage());
            return [];
        }
    }
}
?>
