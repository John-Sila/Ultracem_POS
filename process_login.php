<?php
session_start();  // Start the session at the beginning of the script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_username = $_POST['login_username'] ?? '';
    $login_password = $_POST['login_password'] ?? '';
    $company = strtolower($_POST['selectCompany'] ?? '');

    $host = 'localhost';
    $port = '3306';
    $rootUser = 'root';
    $rootPassword = '';

    try {
        // Create a PDO instance with the root user to manage databases
        $pdo = new PDO("mysql:host=$host;port=$port", $rootUser, $rootPassword);
        // $pdo = new PDO("mysql:host=$host;port=$port;dbname=$company;charset=utf8", $rootUser, $rootPassword);
    
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '$company'");
        $dbExists = $stmt->fetch();

        if (!$dbExists) {
            // echo('<script>alert("db not exist")</script>');
            // Create database if it does not exist
            $pdo->exec("CREATE DATABASE `$company`");
            echo "Database `$company` created successfully.";

            // Use the specified database
            $pdo->exec("USE `$company`");
    
            // Create users table if it does not exist
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL UNIQUE,
                    actual_name VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    rights VARCHAR(50) NOT NULL DEFAULT 'user',
                    created_on DATETIME NOT NULL
                )
            ");
    
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS permissions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    page_name VARCHAR(255) NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS bags (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    width INT NOT NULL,
                    length INT NOT NULL,
                    color VARCHAR(50) NOT NULL,
                    features TEXT,
                    price DECIMAL(10, 2) NOT NULL,
                    amount INT NOT NULL,
                    last_updated DATETIME NOT NULL
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS rolls (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    width FLOAT NOT NULL,
                    length FLOAT NOT NULL,
                    weight FLOAT NOT NULL,
                    color VARCHAR(50) NOT NULL,
                    unit_meter_weight FLOAT NOT NULL,
                    cost_per_kg FLOAT NOT NULL,
                    features TEXT,
                    price DECIMAL(10, 2) NOT NULL,
                    date_added DATETIME NOT NULL,
                    last_modified DATETIME NOT NULL
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS s_q_terms (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    term TEXT NOT NULL
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS s_i_terms (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    term TEXT NOT NULL
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS handles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    color VARCHAR(50) NOT NULL,
                    rolls INT NOT NULL
                )
            ");
                    
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS liners (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    width FLOAT NOT NULL,
                    length FLOAT NOT NULL,
                    amount INT NOT NULL,
                    last_modified DATETIME NOT NULL
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS bag_sales (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    bag_id INT NOT NULL,
                    total_price DECIMAL(10, 2) NOT NULL,
                    date DATE NOT NULL,
                    FOREIGN KEY (bag_id) REFERENCES bags(id)
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS roll_sales (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    roll_id INT NOT NULL,
                    total_price DECIMAL(10, 2) NOT NULL,
                    date DATE NOT NULL,
                    FOREIGN KEY (roll_id) REFERENCES rolls(id)
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS customers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    phone_number1 VARCHAR(20) NOT NULL UNIQUE,
                    phone_number2 VARCHAR(20) NOT NULL UNIQUE,
                    postal_address VARCHAR(255) NOT NULL,
                    postal_code VARCHAR(255) NOT NULL,
                    city VARCHAR(255) NOT NULL,
                    town VARCHAR(255) NOT NULL
                )
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS company_info (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    company_name VARCHAR(255) UNIQUE,
                    default_phone_number VARCHAR(20),
                    secondary_phone_number VARCHAR(20),
                    postal_address VARCHAR(255),
                    postal_code VARCHAR(255),
                    city VARCHAR(255),
                    town VARCHAR(255),
                    kra VARCHAR(255)
                )
            ");
            // **Insert initial row if it doesn't exist**
            $pdo->exec("
            INSERT INTO company_info (id, company_name, default_phone_number, secondary_phone_number, postal_address, postal_code, city, town, kra)
            SELECT 1, '--', '--', '--', '--', '--', '--', '--', '--'
            WHERE NOT EXISTS (SELECT 1 FROM company_info WHERE id = 1)
            ");

            

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS other_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    valuation DECIMAL(10, 2) NOT NULL
                )
            ");
    
            $adminPassword = password_hash('ADMIN_PASSWORD', PASSWORD_BCRYPT);
            // Create the Admin user in the 'users' table
            $pdo->exec("
                INSERT INTO users (username, actual_name, password, rights, created_on)
                VALUES ('Admin', 'Administrator', '$adminPassword', 'admin', NOW())
            ");
            // Fetch the user ID for the admin user
            $adminUserId = $pdo->lastInsertId();
            
            // Insert permissions for the admin user (granting access to all pages)
            $permissions = ['uc_sales', 'items_and_inventory', 'manufacturing', 'customer_management', 'user_management','accounts',  'inside_information', 'database_management', 'suppliers', 'human_resource', 'uc_logout'];

            foreach ($permissions as $page) {
                $stmt = $pdo->prepare("INSERT INTO permissions (user_id, page_name) VALUES (?, ?)");
                $stmt->execute([$adminUserId, $page]);
            }

            echo "Admin user created with full rights.";

        } else {
            echo "Ultracem:";
        }

        // Use the specified database
        $pdo->exec("USE `$company`");

        // Verify user credentials NOW
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$login_username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($login_password, $user['password'])) {
            // echo "Login successful! Welcome, $username.";
            $_SESSION['username'] = $login_username;  // Store the username in the session

            // Fetch user permissions from the permissions table
            $stmt = $pdo->prepare("SELECT page_name FROM permissions WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch only the `page_name` column
            $_SESSION['permissions'] = $permissions;

            // Fetch the actual name of the user
            $stmt = $pdo->prepare("SELECT actual_name FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $actualName = $stmt->fetch(PDO::FETCH_COLUMN); // Fetch the single column value
            $_SESSION['actualName'] = $actualName;
            $_SESSION['db'] = $company;


            header('Location: dash.php');
        } else {
            echo "Invalid username or password. Contact your administrator for further instructions.";
        }



        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
