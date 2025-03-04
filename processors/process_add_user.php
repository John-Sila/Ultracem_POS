<?php

if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

$current_page = isset($_GET['page']) ?? $_GET['page']; // Default to 'sales' if not set

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// echo "<script>alert('" . addslashes($current_page) . "');</script>";

// Check if the user has permission for this page
if (!in_array($current_page, $_SESSION['permissions'])) {
    // If no permission, redirect to sales page
    header("Location: dash.php?page=uc_sales");
    exit();
}

require dirname(__DIR__) . "/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['new_user_full_name']);
    $username = trim($_POST['new_user_username']);
    $password = trim($_POST['new_user_pwd']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing

    try {

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL UNIQUE,
                    actual_name VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    rights VARCHAR(50) NOT NULL DEFAULT 'user',
                    created_on DATETIME NOT NULL
                )
        ");



        // Check if the username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username already exists!";
            echo "<script>alert('Username already exists'); window.history.back();</script>";
            // header("Location: dash.php?page=user_management");
            exit();
        }

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, actual_name, password, rights, created_on) VALUES (?, ?, ?, 'user', NOW())");
        $stmt->execute([$username, $full_name, $hashed_password]);

        // Get the inserted user's ID
        $user_id = $pdo->lastInsertId();

        // Default permission (Sales is always granted)
        $permissions = [];

        // Check other selected permissions
        $possible_permissions = [
            'sales_right' => 'uc_sales',
            'items_and_inventory_right' => 'items_and_inventory',
            'manufacturing_right' => 'manufacturing',
            'customer_management_right' => 'customer_management',
            'user_management_right' => 'user_management',
            'accounts_right' => 'accounts',
            'inside_information_right' => 'inside_information',
            'db_management_right' => 'database_management',
            'suppliers_right' => 'suppliers',
            'human_resource_right' => 'human_resource'
        ];

        foreach ($possible_permissions as $field => $page_name) {
            if (isset($_POST[$field])) {
                $permissions[] = $page_name;
            }
        }

        // Insert permissions in bulk
        $permStmt = $pdo->prepare("INSERT INTO permissions (user_id, page_name) VALUES (?, ?)");
        foreach ($permissions as $permission) {
            $permStmt->execute([$user_id, $permission]);
        }

        $_SESSION['success'] = "User created successfully!";
        header("Location: dash.php?page=user_management");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: dash.php?page=user_management");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: ../dash.php?page=user_management");
    exit();
}
?>
