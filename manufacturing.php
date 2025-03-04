<?php
if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=login");
    exit();
}

$current_page = isset($_GET['page']) ?? $_GET['page']; // Default to 'sales' if not set

// echo "<pre>";
// echo ($current_page);
// echo "</pre>";
// echo "<pre>";
// print_r($_SESSION['permissions']);
// echo "</pre>";

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// echo "<script>alert('" . addslashes($current_page) . "');</script>";

// Check if the user has permission for this page
if (!in_array("manufacturing", $_SESSION['permissions'])) {
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
    <div class="manufacturingMainDiv">
        <div class="manufacturingDiv1">
            <h3>Transactions</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <a href="">Outstanding Work Orders</a>
                </div>
                <div class="rightDiv"></div>
            </div>
        </div>
        <div class="manufacturingDiv2">
            <h3>Inquiries & Reports</h3>
            <div class="innerDiv">
                <div class="leftDiv">
                    <a href="">Costed Bill Of Material Inquiry</a>
                    <a href="">Inventory Item Where Used Inquiry</a>
                    <a href="">Work Order Inquiry</a>
                    
                </div>
                <div class="rightDiv">
                    <a href="">Manufacturing Reports</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>