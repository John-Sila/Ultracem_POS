<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

// $current_page = isset($_GET['page']) ?? $_GET['page']; // Default to 'sales' if not set

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// echo "<script>alert('" . addslashes($current_page) . "');</script>";

// Check if the user has permission for this page
if (!in_array("items_and_inventory", $_SESSION['permissions'])) {
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
    <title>Items & Inventory</title>
</head>
<body>
    <div class="itemsMainDiv">

        <div class="itemsDiv1">
            <h3>Inquiries & Reports</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <a href="">Inventory Item Movements</a>
                    <a href="">Inventory Item Status</a>
                    <a href="">Customer Transaction Inquiry</a>
                </div>
                <div class="rightDiv">
                    <a href="">Inventory Reports</a>
                </div>
            </div>
        </div>
        <div class="itemsDiv2">
            <h3>Inquiries & Reports</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <a href="dash.php?page=inventory/items">Items</a>
                    <a href="dash.php?page=inventory/bags_registry">Bags Registry</a>
                    <a href="dash.php?page=inventory/bags_entry">Bags Entry</a>
                </div>
                <div class="rightDiv">
                    <a href="dash.php?page=inventory/roll_entry">Rolls Entry</a>
                    <a href="">Weight Formula</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>