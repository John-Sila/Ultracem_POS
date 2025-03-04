<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header('Location: dash.php?page=uc_logout');
    exit();
}

if ($_SESSION["permissions"] && !in_array("user_management", $_SESSION["permissions"])) {
    header('Location: uc_logout.php');
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_username = $_POST['pref_username'] ?? '';
    $actualname = $_POST['actualname'] ?? '';
    $posted_password = $_POST['password'] ?? '';
    $permissions = $_POST['permissions'] ?? []; // Fetch selected permissions

    try {
        require 'db.php';

        // Validation flags
        $errors = [];

        // Check if full name has exactly 2 names (first and last)
        if (count(explode(' ', $actualname)) !== 2) {
            $errors[] = "Full name must contain exactly two names (first and last).";
        }

        // Check if username is a single word
        if (strpos($username, ' ') !== false) {
            $errors[] = "Username must not contain spaces.";
        }



        try {
            // Check if the 'customers' table exists
            $tableCheckQuery = "
                SELECT COUNT(*)
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'users'
            ";
            $tableExists = $pdo->query($tableCheckQuery)->fetchColumn();
        
            // If the table does not exist, create it
            if (!$tableExists) {
                $createTableQuery = "
                    CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL UNIQUE,
                    actual_name VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    rights VARCHAR(50) NOT NULL DEFAULT 'user'
                )
                ";
                $pdo->exec($createTableQuery);
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }





        
        // Check if full name already exists in the database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE actual_name = ?");
        $stmt->execute([$actualname]);
        $fullNameExists = $stmt->fetchColumn() > 0;
        
        if ($fullNameExists) {
            $errors[] = "A user with that full name already exists.";
            $errors[] = $actualname;
        }
        
        // Check if username already exists in the database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$posted_username]);
        $usernameExists = $stmt->fetchColumn() > 0;
        
        if ($usernameExists) {
            $errors[] = "This username is already taken.";
            $errors[] = $posted_username;
        }

        if (empty($errors)) {
            // Insert the new user into the users table
            $hashedPassword = password_hash($posted_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, actual_name, password) VALUES (?, ?, ?)");
            $stmt->execute([$posted_username, $actualname, $hashedPassword]);
    
            // Get the user ID of the newly created user
            $userId = $pdo->lastInsertId();
    
            // Insert the selected permissions into the permissions table
            foreach ($permissions as $page) {
                $stmt = $pdo->prepare("INSERT INTO permissions (user_id, page_name) VALUES (?, ?)");
                $stmt->execute([$userId, $page]);
            }

            echo "<script>alert('Action Successful'); window.history.back();</script>";
        
            // header('Location: add_user.php');

        } else {
            // Display errors
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
        }
    } catch (\Exception $e) {

    }
}