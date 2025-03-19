<?php
require dirname(__DIR__) . "/db.php";

if (isset($_POST['selectRoll']) && !empty($_POST['selectRoll'])) {
    $selectedRollId = $_POST['selectRoll'];

    try {
        // Fetch the selected roll details
        $stmt = $pdo->prepare("SELECT color, width, features FROM rolls WHERE id = ?");
        $stmt->execute([$selectedRollId]);
        $roll = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($roll) {
            $rollColor = $roll['color'];
            $rollWidth = $roll['width'];
            $rollFeatures = trim($roll['features']);

            // Prepare base query
            $query = "SELECT id, width, length, color, features FROM bags WHERE color = ? AND width = ?";
            $params = [$rollColor, $rollWidth];

            if (!empty($rollFeatures)) {
                // Convert comma-separated features to an array
                $featureArray = array_map('trim', explode(',', $rollFeatures));

                // Add a LIKE condition for each feature
                foreach ($featureArray as $feature) {
                    $query .= " AND features LIKE ?";
                    $params[] = "%$feature%";
                }
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            // Output matching bags as <option> elements
            while ($bag = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' 
                . htmlspecialchars($bag['id']) . '">'
                . htmlspecialchars($bag['width']). " × "
                . htmlspecialchars($bag['length']) . " - "
                . htmlspecialchars($bag['color']) . " - "
                . htmlspecialchars($bag['features']) .'</option>';
            }
        } else {
            echo '<option value="">No matching roll found</option>';
        }
    } catch (PDOException $e) {
        echo '<option value="">Error fetching bags' . htmlspecialchars($e) . '</option>';
    }
} else {
    echo '<option value="">Select a roll first</option>';
}
?>
