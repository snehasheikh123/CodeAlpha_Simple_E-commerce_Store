<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$user_id = (int)$_SESSION['user_id'];

// Fetch cart + product details
$stmt = $conn->prepare("
  SELECT
    ac.id        AS cart_id,
    ac.quantity,
    p.id         AS product_id,
    p.name,
    p.description,
    p.price,
    p.image
  FROM add_cart AS ac
  JOIN products AS p ON ac.product_id = p.id
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

// Cart rules
$delivery = 0.00;
$discount = 3.00;
$total    = $subtotal + $delivery - $discount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Minishop - Free Bootstrap 4 Template by Colorlib</title>
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
    <?php include 'header.php'?>

    <div class="hero-wrap hero-bread" style="background-image: url('images/bg_6.jpg');">
      <div class="container text-center">
        <p class="breadcrumbs">
          <span class="mr-2"><a href="index.php">Home</a></span>
          <span>Cart</span>
        </p>
        <h1 class="mb-0 bread">My Cart</h1>
      </div>
    </div>

    <section class="ftco-section ftco-cart">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <div class="cart-list">
              <table class="table">
                <thead class="thead-primary">
                  <tr class="text-center">
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($items) > 0): ?>
                    <?php foreach ($items as $item): ?>
                      <tr class="text-center">
                        <td class="product-remove">
                          <a href="remove_cart.php?cart_id=<?= $item['cart_id'] ?>"><span class="ion-ios-close"></span></a>
                        </td>
                        <td class="image-prod">
                          <div class="img" style="background-image:url('<?= htmlspecialchars($item['image']) ?>');"></div>
                        </td>
                        <td class="product-name">
                          <h3><?= htmlspecialchars($item['name']) ?></h3>
                          <p><?= htmlspecialchars($item['description']) ?></p>
                        </td>
                        <td class="price">$<?= number_format($item['price'], 2) ?></td>
                        <td class="quantity">
                          <input 
                            type="number" 
                            class="quantity form-control input-number" 
                            value="<?= $item['quantity'] ?>" 
                            min="1" max="100"
                          >
                        </td>
                        <td class="total">$<?= number_format($item['total'], 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center">Your cart is empty</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <div class="row justify-content-start">
          <div class="col-lg-5 col-md-6 mt-5 cart-wrap ftco-animate">
            <div class="cart-total mb-3">
              <h3>Cart Totals</h3>
              <p class="d-flex">
                <span>Subtotal</span>
                <span id="subtotal-amount">$<?= number_format($subtotal,2) ?></span>
              </p>
              <p class="d-flex">
                <span>Delivery</span>
                <span id="delivery-amount">$<?= number_format($delivery,2) ?></span>
              </p>
              <p class="d-flex">
                <span>Discount</span>
                <span id="discount-amount">$<?= number_format($discount,2) ?></span>
              </p>
              <hr>
              <p class="d-flex total-price">
                <span>Total</span>
                <span id="total-amount">$<?= number_format($total,2) ?></span>
              </p>
            </div>
            <p class="text-center">
              <a href="checkout.php" class="btn btn-primary py-3 px-4">Proceed to Checkout</a>
            </p>
          </div>
        </div>
      </div>
    </section>

    <?php include 'footer.php'?>
    <?php include 'script.php'?>

    <script>
      $(document).ready(function() {
        // grab fixed values
        const delivery = parseFloat($('#delivery-amount').text().replace('$',''));
        const discount = parseFloat($('#discount-amount').text().replace('$',''));

        function recalcCart() {
          let subtotal = 0;
          $('tbody tr').each(function() {
            const price = parseFloat($(this).find('.price').text().replace('$',''));
            const qty   = parseInt($(this).find('input.quantity').val());
            const rowTotal = price * qty;
            $(this).find('.total').text('$' + rowTotal.toFixed(2));
            subtotal += rowTotal;
          });

          $('#subtotal-amount').text('$' + subtotal.toFixed(2));
          const total = subtotal + delivery - discount;
          $('#total-amount').text('$' + total.toFixed(2));
        }

        // watch for quantity changes
        $('body').on('change', 'input.quantity', recalcCart);
      });
    </script>
  </body>
</html>
