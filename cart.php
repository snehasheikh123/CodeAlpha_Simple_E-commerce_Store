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
  <div class="py-1 bg-black">

	<?php include 'header.php'?>

    <div class="hero-wrap hero-bread" style="background-image: url('images/bg_6.jpg');">
      <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>Cart</span></p>
            <h1 class="mb-0 bread">My Wishlist</h1>
          </div>
        </div>
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
                                <?php
                                if (count($items) > 0) {
                                    foreach ($items as $item) {
                                        $total_price = $item['price'] * $item['quantity'];
                                ?>
                                    <tr class="text-center">
                                        <td class="product-remove"><a href="remove_cart.php?cart_id=<?php echo $item['cart_id']; ?>"><span class="ion-ios-close"></span></a></td>
                                        <td class="image-prod">
                                            <div class="img" style="background-image:url(<?php echo $item['image']; ?>);"></div>
                                        </td>
                                        <td class="product-name">
                                            <h3><?php echo $item['name']; ?></h3>
                                            <p><?php echo $item['description']; ?></p>
                                        </td>
                                        <td class="price"><?php echo "$" . number_format($item['price'], 2); ?></td>
                                        <td class="quantity">
                                            <div class="input-group mb-3">
                                                <input type="number" name="quantity" class="quantity form-control input-number" value="<?php echo $item['quantity']; ?>" min="1" max="100">
                                            </div>
                                        </td>
                                        <td class="total"><?php echo "$" . number_format($total_price, 2); ?></td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Your cart is empty</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

          <div class="row justify-content-start">
  <div class="col col-lg-5 col-md-6 mt-5 cart-wrap ftco-animate">
    <div class="cart-total mb-3">
      <h3>Cart Totals</h3>
      <?php
        $delivery = 0.00;
        $discount = 3.00;
        $total = $subtotal - $discount;

        function displayRow($label, $amount, $isTotal = false) {
          $amountFormatted = "$" . number_format($amount, 2);
          $class = $isTotal ? 'd-flex total-price' : 'd-flex';
          echo "<p class=\"$class\">
                  <span>$label</span>
                  <span>$amountFormatted</span>
                </p>";
        }

        displayRow("Subtotal", $subtotal);
        displayRow("Delivery", $delivery);
        displayRow("Discount", $discount);
        echo "<hr>";
        displayRow("Total", $total, true);
      ?>
    </div>
    <p class="text-center"><a href="checkout.php" class="btn btn-primary py-3 px-4">Proceed to Checkout</a></p>
  </div>
</div>

        </div>
    </section>

    <?php include 'footer.php'; ?>


   
    
  

    <?php include 'script.php'; ?>


  
  <script>
  $(document).ready(function() {
    // Quantity change ke time par calculation karna
    $('.quantity input').on('change', function() {
      var quantity = parseInt($(this).val());
      if (quantity < 1) quantity = 1;
      
      var row = $(this).closest('tr');
      var priceText = row.find('.price').text().replace('$', '');
      var price = parseFloat(priceText);
      var total = quantity * price;
      
      row.find('.total').text('$' + total.toFixed(2));

      // Subtotal update
      var subtotal = 0;
      $('.total').each(function() {
        subtotal += parseFloat($(this).text().replace('$', ''));
      });

      var discount = 3.00;
      $('.cart-total .d-flex span:nth-child(2)').eq(0).text('$' + subtotal.toFixed(2));
      $('.cart-total .d-flex span:nth-child(2)').eq(2).text('$' + (subtotal - discount).toFixed(2));
    });
  });
</script>


  </body>
</html>