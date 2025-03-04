<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=login");
    exit();
}

$current_page = isset($_GET['page']) ? $_GET['page'] : 'sales'; // Default to 'sales' if not set

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// echo "<script>alert('" . addslashes($current_page) . "');</script>";

// Check if the user has permission for this page
if (!in_array("suppliers", $_SESSION['permissions'])) {
    // If no permission, redirect to sales page
    header("Location: dash.php?page=uc_sales");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Suppliers</h1>
</body>
</html>