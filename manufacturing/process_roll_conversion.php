<?php
require dirname(__DIR__) . "/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rollId = $_POST['selectRoll'] ?? null;
    $bagId = $_POST['selectBag'] ?? null;
    $totalBags = $_POST['totalBags'] ?? 0;
    $rollDepleted = isset($_POST['rollDepleted']) ? true : false;

    if (!$rollId || !$bagId || $totalBags <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
        exit;
    }

    try {
        // Fetch roll details
        $stmt = $pdo->prepare("SELECT length, unit_meter_weight FROM rolls WHERE id = ?");
        $stmt->execute([$rollId]);
        $roll = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch bag length
        $stmt = $pdo->prepare("SELECT length FROM bags WHERE id = ?");
        $stmt->execute([$bagId]);
        $bag = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$roll || !$bag) {
            echo json_encode(["status" => "error", "message" => "Roll or bag not found"]);
            exit;
        }

        $bagLength = (float) $bag['length'] / 100;  // Length of one bag
        $currentRollLength = (float) $roll['length'];
        $unitMeterWeight = (float) $roll['unit_meter_weight'];

        // Calculate new roll length
        $cutLength = $bagLength * $totalBags;
        $newRollLength = max(0, $currentRollLength - $cutLength);

        $pdo->beginTransaction();

        if ($rollDepleted) {
            // Delete the roll if depleted
            $stmt = $pdo->prepare("DELETE FROM rolls WHERE id = ?");
            $stmt->execute([$rollId]);
        } else {
            // Update the roll length
            $stmt = $pdo->prepare("UPDATE rolls SET length = ? WHERE id = ?");
            $stmt->execute([$newRollLength, $rollId]);
        }

        // Increment bag stock
        $stmt = $pdo->prepare("UPDATE bags SET amount = amount + ? WHERE id = ?");
        $stmt->execute([$totalBags, $bagId]);

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Conversion successful"]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
