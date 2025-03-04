<?php
session_start();

require 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $userName = $_SESSION['username'];

    try {
        // Verify the current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$userName]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            echo "<script>alert('Incorrect current password.'); window.history.back();</script>";
            exit;
        }

        // Update full name if confirmed
        if (isset($_POST['fullNameChecked']) && !empty($_POST['desiredFullName'])) {
            $desiredFullName = trim($_POST['desiredFullName']);
            $stmt = $pdo->prepare("UPDATE users SET actual_name = ? WHERE username = ?");
            $stmt->execute([$desiredFullName, $userName]);
        }

        // Update username if confirmed
        if (isset($_POST['userNameChecked']) && !empty($_POST['desiredUserName'])) {
            $desiredUserName = trim($_POST['desiredUserName']);
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE username = ?");
            $stmt->execute([$desiredUserName, $userName]);
        }

        // Update password if confirmed
        if (isset($_POST['passwordChecked']) && !empty($_POST['desiredPassword'])) {
            $desiredPassword = password_hash(trim($_POST['desiredPassword']), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$desiredPassword, $userName]);
        }

        // Redirect to a success page
        echo "<script>alert('Action Completed. You have been logged out.'); window.location.href='uc_logout.php';</script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }
}
?>