<?php

if (!isset($_SESSION['username'])) {
    header("Location: dash.php?page=uc_logout");
    exit();
}

// Check if permissions are set
if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    // If no permissions are found, redirect to sales page
    header("Location: dash.php?page=uc_logout");
    exit();
}
// Check if the user has permission for this page
if (!in_array("items_and_inventory", $_SESSION['permissions'])) {
    // If no permission, redirect to sales page
    header("Location: dash.php?page=uc_sales");
    exit();
}


require dirname(__DIR__) . "/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["bag_id"])) {
    $bagId = intval($_POST["bag_id"]);
    $stmt = $pdo->prepare("DELETE FROM bags WHERE id = ?");
    if ($stmt->execute([$bagId])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
