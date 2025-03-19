<?php
// session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS bags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            width INT NOT NULL,
            length INT NOT NULL,
            color VARCHAR(50) NOT NULL,
            features TEXT DEFAULT NULL,
            price DECIMAL(10,2) NOT NULL,
            amount INT NOT NULL DEFAULT 0,
            last_updated DATETIME NOT NULL
        )");

        

        // Retrieve form data
        $width = $_POST['bag_width'];
        $length = $_POST['bag_length'];
        $bagColor1 = $_POST['bagColor1'];
        $isPanel = isset($_POST['bagIsPanel']); // Check if panel checkbox is checked
        $bagColor2 = $isPanel ? $_POST['bagColor2'] : "";
        $price = $_POST['bag_price'];

        // Determine the color based on whether the bag is a panel
        if ($isPanel) {
            $color = trim("$bagColor1 $bagColor2 Panel");
        } else {
            $color = $bagColor1;
        }

        $features = [];

        if (isset($_POST['gussette_feature'])) $features[] = "Gussetted";
        if (isset($_POST['handles_feature'])) $features[] = "Handles";
        if (isset($_POST['hemming_feature'])) $features[] = "Hemmed";
        if (isset($_POST['lamination_feature'])) $features[] = "Laminated";
        if (isset($_POST['lining_feature'])) $features[] = "Lined";
        if (isset($_POST['printing_feature'])) $features[] = "Printed";
        if (isset($_POST['punch_hole_feature'])) $features[] = "Punched";

        // Convert array to a comma-separated string
        $featuresString = implode(", ", $features);

        // die($featuresString); // This will correctly output "Gussetted Handles" when both are checked





        // Set default values for amount and last_updated
        $amount = 0;
        $lastUpdated = date("Y-m-d H:i:s");

        // Check if the same bag already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bags WHERE width = ? AND length = ? AND color = ? AND features = ? AND price = ?");
        $stmt->execute([$width, $length, $color, $featuresString, $price]);
        $bagExists = $stmt->fetchColumn();

        if ($bagExists > 0) {
            // Redirect to inventory page with an error message
            echo ("<script>alert('It looks like this bag already exists!'); window.history.back();</script>");
            // header("Location: dash.php?page=items_and_inventory&error=exists");
            exit();
        }

        // Prepare SQL query
        $stmt = $pdo->prepare("INSERT INTO bags (width, length, color, features, price, amount, last_updated)
                               VALUES (:width, :length, :color, :features, :price, :amount, NOW())");

        // Execute query
        $stmt->execute([
            ':width' => $width,
            ':length' => $length,
            ':color' => $color,
            ':features' => $featuresString,
            ':price' => $price,
            ':amount' => $amount
        ]);

        // Redirect to success page or inventory page
        echo ("<script>alert('Bag successfully registered!'); window.history.back();</script>");
        // header("Location: dash.php?page=inventory/bags_registry");
        exit();
    } catch (PDOException $e) {
        // Handle errors
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect if accessed directly
    header("Location: dash.php?page=inventory");
    exit();
}
