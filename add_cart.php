<?php
session_start();
require 'db_connect.php';  // sets up $conn

// 1) Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$user_id = (int)$_SESSION['user_id'];

// 2) Make sure this is a POST with product_id
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['product_id'])) {
  $product_id = (int)$_POST['product_id'];
  $quantity   = max(1, (int)($_POST['quantity'] ?? 1));

  // 3) If item already in cart, update qty; otherwise insert new
  $check = $conn->prepare("
    SELECT id, quantity 
    FROM add_cart 
    WHERE user_id = ? AND product_id = ?
  ");
  $check->bind_param("ii", $user_id, $product_id);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows) {
    $row = $res->fetch_assoc();
    $new_qty = $row['quantity'] + $quantity;
    $upd = $conn->prepare("
      UPDATE add_cart 
      SET quantity = ?, added_on = NOW() 
      WHERE id = ?
    ");
    $upd->bind_param("ii", $new_qty, $row['id']);
    $upd->execute();
    $upd->close();
  } else {
    $ins = $conn->prepare("
      INSERT INTO add_cart (user_id, product_id, quantity, added_on)
      VALUES (?, ?, ?, NOW())
    ");
    $ins->bind_param("iii", $user_id, $product_id, $quantity);
    $ins->execute();
    $ins->close();
  }

  $check->close();
  header('Location: cart.php');
  exit;
}

// if we get here, something was wrong
echo "Invalid request.";
