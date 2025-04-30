<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Check if cart_id is provided
if (isset($_GET['cart_id'])) {
    $cart_id = (int)$_GET['cart_id'];

    // Only delete if the cart item belongs to the logged-in user
    $stmt = $conn->prepare("DELETE FROM add_cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();

    // Optional: you can check $stmt->affected_rows to confirm if something was deleted
    $stmt->close();
}

// Redirect back to cart
header("Location: cart.php");
exit;
