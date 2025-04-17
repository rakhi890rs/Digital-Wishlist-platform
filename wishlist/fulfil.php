<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $sql = "UPDATE wishes SET fulfilled = 1 WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: view.php"); // Redirect back to wishlist
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
