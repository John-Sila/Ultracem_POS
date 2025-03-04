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
        $pdo->exec("CREATE TABLE IF NOT EXISTS rolls (
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
                )");

        

        // Retrieve form data
        $width = $_POST['roll_width'];
        $length = $_POST['roll_length'];
        $weight = $_POST['roll_weight'];

        $roll_weight = isset($_POST['roll_weight']) ? floatval($_POST['roll_weight']) : 0;
        $roll_length = isset($_POST['roll_length']) ? floatval($_POST['roll_length']) : 1; // Avoid division by zero
        $roll_price = isset($_POST['roll_price']) ? floatval($_POST['roll_price']) : 1; // Avoid division by zero

        $unit_meter_weight = $roll_weight / $roll_length;
        $cost_per_kg = $roll_price / $roll_weight;

        $rollColor1 = $_POST['rollColor1'];
        $rollColor2 = isset($_POST['rollColor2']) ? $_POST['rollColor2'] : "";
        $isPanel = isset($_POST['rollIsPanel']); // Check if panel checkbox is checked
        $price = $_POST['roll_price'];

        // Determine the color based on whether the roll is a panel
        if ($isPanel) {
            $color = trim("$rollColor1 $rollColor2 Panel");
        } else {
            $color = $rollColor1;
        }

        // Collect selected features as a comma-separated string
        $features = [];
        $featureKeys = [
            'lamination_feature', 'gussette_feature', 'printing_feature',
        ];

        foreach ($featureKeys as $key) {
            if (isset($_POST[$key])) {
                $features[] = $_POST[$key];
            }
        }
        $featuresString = empty($features) ? null : implode(', ', $features);

        // Prepare SQL query
        $stmt = $pdo->prepare("INSERT INTO rolls (width, length, weight, color, unit_meter_weight, cost_per_kg, features, price, date_added, last_modified)
                               VALUES (:width, :length, :weight, :color, :unit_meter_weight, :cost_per_kg, :features, :price, NOW(), NOW())");

        // Execute query
        $stmt->execute([
            ':width' => $width,
            ':length' => $length,
            ':weight' => $weight,
            ':color' => $color,
            ':unit_meter_weight' => $unit_meter_weight * 1000,
            ':cost_per_kg' => $cost_per_kg,
            ':features' => $featuresString,
            ':price' => $price,
        ]);

        // Redirect to success page or inventory page
        echo ("<script>alert('Roll successfully entered!'); window.history.back();</script>");
        
        exit();
    } catch (PDOException $e) {
        // Handle errors
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect if accessed directly
    header("Location: dash.php?page=items_and_inventory");
    exit();
}
