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

        // deal with the features





        
        if (isset($_POST['features']) && is_array($_POST['features'])) {
            // Clean out any accidental empty values
            $features = array_filter($_POST['features']);
    
            // Combine into a string if needed
            $featuresString = implode(', ', $features);
    
            // Debug to see the values
            var_dump($features); // Array of selected features
            die($featuresString); // String like: "Gussetted, Handles"
        } else {
            // No features selected
            $features = [];
            $featuresString = null;
    
            die('No features selected');
        }

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
