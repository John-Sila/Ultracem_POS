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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require dirname(__DIR__) . "/db.php"; // Ensure database connection is included

    $fields = $_POST['fields'] ?? []; // Get checked fields
    $updateData = [];

    // Mapping form fields to database columns
    $fieldsMap = [
        'company_name' => 'company_name',
        'company_phone1' => 'default_phone_number',
        'company_phone2' => 'secondary_phone_number',
        'company_postal_address' => 'postal_address',
        'company_postal_code' => 'postal_code',
        'company_city' => 'city',
        'company_town' => 'town',
        'company_kra' => 'kra'
    ];

    // Prepare data only for checked fields
    foreach ($fields as $key => $value) {
        if (!empty($_POST[$key])) {
            $updateData[$fieldsMap[$key]] = $_POST[$key];
        }
    }

    if (!empty($updateData)) {
        // Prepare SQL dynamically
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($updateData)));
        $values = array_values($updateData);
        $values[] = 1; // Append ID = 1 for WHERE condition

        $stmt = $pdo->prepare("UPDATE company_info SET $setClause WHERE id = ?");
        if ($stmt->execute($values)) {
            echo "Data updated successfully!";
            echo "<script>alert('Data updated successfully!');window.history.back()</script>";
        } else {
            echo "Error updating data.";
        }
    } else {
        echo "No fields were selected!";
    }
}
