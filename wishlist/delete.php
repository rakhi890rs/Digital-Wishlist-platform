<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../db.php';

// Check if ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view.php?error=Invalid request");
    exit();
}

$item_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // First, verify the item exists and belongs to the user
    $verify_query = "SELECT id FROM wishlist WHERE id = ? AND user_id = ?";
    $verify_stmt = $conn->prepare($verify_query);
    $verify_stmt->bind_param("ii", $item_id, $user_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: view.php?error=Item not found or you don't have permission to delete it");
        exit();
    }

    // If verification passes, proceed with deletion
    $delete_query = "DELETE FROM wishlist WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $item_id, $user_id);

    if ($delete_stmt->execute()) {
        // Log the deletion (optional)
        $log_query = "INSERT INTO activity_log (user_id, action, item_id, created_at) VALUES (?, 'delete', ?, NOW())";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("ii", $user_id, $item_id);
        $log_stmt->execute();

        header("Location: view.php?success=Item deleted successfully");
        exit();
    } else {
        throw new Exception("Failed to delete item");
    }
} catch (Exception $e) {
    header("Location: view.php?error=" . urlencode($e->getMessage()));
    exit();
} finally {
    // Close all statements
    if (isset($verify_stmt)) $verify_stmt->close();
    if (isset($delete_stmt)) $delete_stmt->close();
    if (isset($log_stmt)) $log_stmt->close();
    $conn->close();
}
?> 