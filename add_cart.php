<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['product_id'])) {
  $product_id = (int)$_POST['product_id'];
  // use posted quantity (defaults to 1 if invalid)
  $quantity   = isset($_POST['quantity']) && (int)$_POST['quantity'] > 0
                ? (int)$_POST['quantity']
                : 1;

  // Check existing
  $check = $conn->prepare("
    SELECT id, quantity 
    FROM add_cart 
    WHERE user_id = ? AND product_id = ?
  ");
  $check->bind_param("ii", $user_id, $product_id);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows) {
    $row    = $res->fetch_assoc();
    $newQty = $row['quantity'] + $quantity;
    $upd    = $conn->prepare("
      UPDATE add_cart 
      SET quantity = ?, added_on = NOW() 
      WHERE id = ?
    ");
    $upd->bind_param("ii", $newQty, $row['id']);
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

// If we reach here, it's not a valid POST
echo "Invalid request.";
