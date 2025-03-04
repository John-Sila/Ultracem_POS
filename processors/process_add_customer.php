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
// Check if the user has permission for this page
if (!in_array($current_page, $_SESSION['permissions'])) {
    // If no permission, redirect to sales page
    header("Location: dash.php?page=uc_sales");
    exit();
}


require dirname(__DIR__) . "/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customerName = trim($_POST['customer_name']);
    $fullContact1 = trim($_POST['fullNumber1']);
    $fullContact2 = trim($_POST['fullNumber2']);
    $postalAddress = trim($_POST['customer_postal_address']);
    $postalCode = trim($_POST['customer_postal_code']);
    $customerCity = trim($_POST['customer_city']);
    $customerTown = trim($_POST['customer_town']);

    try {
        // Check if the customer name or phone number already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE name = ? OR phone_number1 = ?");
        $stmt->execute([$customerName, $fullContact1]);
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            // If customer or phone number exists, return an error message
            echo "<script>alert('Customer name or phone number already exists.'); window.history.back();</script>";
            exit;
        }

        // Insert new customer if no duplicates are found
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone_number1, phone_number2, postal_address, postal_code, city, town) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customerName, $fullContact1, $fullContact2, $postalAddress, $postalCode, $customerCity, $customerTown]);

        
        echo "<script>alert('Customer added successfully!');</script>";
        header("Location: dash.php?page=customer_management");
        
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}
?>
