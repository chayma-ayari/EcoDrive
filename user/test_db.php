<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

try {
    // Test database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "Database connection successful!\n";
        
        // Test user table
        $query = "SHOW TABLES LIKE 'user'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "User table exists!\n";
            
            // Add is_admin column if it doesn't exist
            $query = "SHOW COLUMNS FROM user LIKE 'is_admin'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                $query = "ALTER TABLE user ADD COLUMN is_admin BOOLEAN DEFAULT FALSE";
                $stmt = $conn->prepare($query);
                if ($stmt->execute()) {
                    echo "Added is_admin column successfully!\n";
                }
            }
            
            // Update table structure if needed
            $query = "ALTER TABLE user 
                     MODIFY COLUMN nom VARCHAR(100) NOT NULL,
                     MODIFY COLUMN prenom VARCHAR(100) NOT NULL,
                     MODIFY COLUMN telephone VARCHAR(20),
                     MODIFY COLUMN email VARCHAR(200) UNIQUE NOT NULL,
                     MODIFY COLUMN adresse TEXT,
                     MODIFY COLUMN pw VARCHAR(255) NOT NULL";
            
            $stmt = $conn->prepare($query);
            if ($stmt->execute()) {
                echo "Updated table structure successfully!\n";
            }
            
            // Check table structure
            $query = "DESCRIBE user";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "\nTable structure:\n";
            foreach ($columns as $column) {
                echo "{$column['Field']} - {$column['Type']}\n";
            }
            
            // Check if any users exist
            $query = "SELECT COUNT(*) as count FROM user";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "\nTotal users in database: {$result['count']}\n";
        } else {
            echo "User table does not exist!\n";
            
            // Create user table if it doesn't exist
            $query = "CREATE TABLE user (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20),
                email VARCHAR(200) UNIQUE NOT NULL,
                adresse TEXT,
                pw VARCHAR(255) NOT NULL,
                is_admin BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $stmt = $conn->prepare($query);
            if ($stmt->execute()) {
                echo "User table created successfully!\n";
                
                // Create default admin user
                $adminEmail = "admin@example.com";
                $adminPassword = password_hash("admin123", PASSWORD_BCRYPT);
                $query = "INSERT INTO user (nom, prenom, email, pw, is_admin) 
                         VALUES ('Admin', 'User', :email, :pw, TRUE)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':email', $adminEmail);
                $stmt->bindParam(':pw', $adminPassword);
                if ($stmt->execute()) {
                    echo "Default admin user created successfully!\n";
                }
            }
        }
    } else {
        echo "Database connection failed!\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 