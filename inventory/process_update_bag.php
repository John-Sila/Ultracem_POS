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


ob_start();
require dirname(__DIR__) . "/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["bag_id"]) && isset($_POST["received_amount"])) {
    $bagId = intval($_POST["bag_id"]);
    $receivedAmount = intval($_POST["received_amount"]);

    if ($receivedAmount > 0) {
        $stmt = $pdo->prepare("UPDATE bags SET amount = amount + ?, last_updated = NOW() WHERE id = ?");
        if ($stmt->execute([$receivedAmount, $bagId])) {
            ob_end_clean();
            die("success");
        } else {
            ob_end_clean();
            die("db_error");
        }
    } else {
        ob_end_clean();
        die("db_error");
    }
} else {
    ob_end_clean();
    die("db_error");
}
?>
