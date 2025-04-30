<?php
session_start();
require 'db_connect.php'; // defines $conn = new mysqli(...)

// 1. Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// delivery & discount rules
$delivery = 0.00;
$discount = 3.00;

// 2. Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize
    $first   = $conn->real_escape_string($_POST['firstname']);
    $last    = $conn->real_escape_string($_POST['lastname']);
    $country = $conn->real_escape_string($_POST['country']);
    $addr1   = $conn->real_escape_string($_POST['address1']);
    $addr2   = $conn->real_escape_string($_POST['address2']);
    $city    = $conn->real_escape_string($_POST['city']);
    $zip     = $conn->real_escape_string($_POST['zip']);
    $phone   = $conn->real_escape_string($_POST['phone']);
    $email   = $conn->real_escape_string($_POST['email']);
    $payment = $conn->real_escape_string($_POST['payment_method']);

    // Compute subtotal
    $stmt = $conn->prepare("
        SELECT SUM(ac.quantity * p.price) 
        FROM add_cart ac 
        JOIN products p ON ac.product_id = p.id 
        WHERE ac.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($subtotal);
    $stmt->fetch();
    $stmt->close();

    $subtotal = (float)$subtotal;
    $total_amount = $subtotal + $delivery - $discount;

    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders 
          (user_id, first_name, last_name, country, address1, address2, city, zip, phone, email, payment_method, total_amount)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ");
    $stmt->bind_param(
        "issssssssdss",
        $user_id, $first, $last, $country, $addr1, $addr2, $city, $zip, $phone, $email, $payment, $total_amount
    );
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // TODO: you could also write out order_items here, or clear add_cart
    // $conn->query("DELETE FROM add_cart WHERE user_id = $user_id");

    header("Location: thank_you.php?order_id=$order_id");
    exit;
}

// 3. On GET: pull cart items for display
$stmt = $conn->prepare("
    SELECT 
      ac.quantity,
      p.id       AS product_id,
      p.name,
      p.description,
      p.price,
      p.image
    FROM add_cart ac 
    JOIN products p ON ac.product_id = p.id
    WHERE ac.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items    = [];
$subtotal = 0.0;
while ($r = $result->fetch_assoc()) {
    $r['total']  = $r['price'] * $r['quantity'];
    $subtotal   += $r['total'];
    $items[]     = $r;
}
$stmt->close();
$conn->close();

$total = $subtotal + $delivery - $discount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Minishop - Checkout</title>
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
<body class="goto-here">
<div class="py-1 bg-black">


    <?php include 'header.php'; ?>

    <div class="hero-wrap hero-bread" style="background-image:url('images/bg_6.jpg');">
        <div class="container text-center">
            <p class="breadcrumbs">
                <span class="mr-2"><a href="index.php">Home</a></span> 
                <span>Checkout</span>
            </p>
            <h1 class="mb-0 bread">Checkout</h1>
        </div>
    </div>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 ftco-animate">
                    <form action="" method="POST" class="billing-form">
                        <h3 class="mb-4 billing-heading">Billing Details</h3>
                        <div class="row align-items-end">
                            <!-- First & Last Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="firstname" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="lastname" class="form-control" required>
                                </div>
                            </div>
                            <!-- Country -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Country</label>
                                    <div class="select-wrap">
                                        <select name="country" class="form-control" required>
                                            <option>India</option>
                                            <option>France</option>
                                            <option>Italy</option>
                                            <option>Philippines</option>
                                            <option>South Korea</option>
                                            <option>Hongkong</option>
                                            <option>Japan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="address1" class="form-control" placeholder="House number and street name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="address2" class="form-control" placeholder="Apartment, suite, unit (optional)">
                                </div>
                            </div>
                            <!-- City & Zip -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Town / City</label>
                                    <input type="text" name="city" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Postcode / ZIP</label>
                                    <input type="text" name="zip" class="form-control" required>
                                </div>
                            </div>
                            <!-- Phone & Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Summary & Payment -->
                        <div class="row mt-5 pt-3 d-flex">
                            <!-- Cart Totals -->
                            <div class="col-md-6 d-flex">
                                <div class="cart-detail cart-total bg-light p-3 p-md-4">
                                    <h3 class="billing-heading mb-4">Cart Total</h3>
                                    <p class="d-flex"><span>Subtotal</span><span>$<?=number_format($subtotal, 2)?></span></p>
                                    <p class="d-flex"><span>Delivery</span><span>$<?=number_format($delivery, 2)?></span></p>
                                    <p class="d-flex"><span>Discount</span><span>$<?=number_format($discount, 2)?></span></p>
                                    <hr>
                                    <p class="d-flex total-price"><span>Total</span><span>$<?=number_format($total, 2)?></span></p>
                                </div>
                            </div>
                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <div class="cart-detail bg-light p-3 p-md-4">
                                    <h3 class="billing-heading mb-4">Payment Method</h3>
                                    <div class="form-group">
                                        <label><input type="radio" name="payment_method" value="bank" required> Direct Bank Transfer</label><br>
                                        <label><input type="radio" name="payment_method" value="check"> Cash on Delivery</label><br>
                                        <label><input type="radio" name="payment_method" value="check"> Check Payment</label><br>
                                        <label><input type="radio" name="payment_method" value="paypal"> Paypal</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" name="terms" required> I accept <a href="#">terms &amp; conditions</a></label>
                                    </div>
                                    <p><button type="submit" class="btn btn-primary py-3 px-4">Place Order</button></p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
<?php include 'script.php'; ?>
</body>
</html>
