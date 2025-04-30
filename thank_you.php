<?php
session_start();
require 'db_connect.php'; // defines $conn = new mysqli(...)

// 1. Validate order_id
if (empty($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}
$order_id = (int)$_GET['order_id'];

// 2. Fetch order from DB
$stmt = $conn->prepare("
    SELECT 
      id,
      first_name,
      last_name,
      email,
      phone,
      payment_method,
      total_amount,
      created_at
    FROM orders
    WHERE id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<p>Invalid order ID.</p>";
    exit;
}
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Thank You – Minishop</title>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="py-1 bg-black">

  <?php include 'header.php'; ?>

  
  <div class="hero-wrap hero-bread" style="background-image:url('images/bg_6.jpg');">
        <div class="container text-center">
            <p class="breadcrumbs">
                <span class="mr-2"><a href="index.php">Home</a></span> 
                <span>Your Detail</span>
            </p>
            <h1 class="mb-0 bread">Thank You for Your Order!</h1>
            
        </div>
    </div>

  <section class="ftco-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 text-center">
          <h3>Order Confirmation</h3>
          <p>Your order has been placed successfully. 
            <br>Here are your order details:</p>
          <ul class="order-summary"<strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?><br>
            <strong>Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?><br>
        <strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?><br>
        <strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']) ?><br>
        <strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?><br>
          </ul>
          <p>We’ll send you a confirmation email shortly. If you have any questions, <a href="contact.php">contact us</a>.</p>
          <p><a href="index.php" class="btn btn-primary">Return to Home</a></p>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/scrollax.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
