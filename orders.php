<?php

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

// Using PHP 8 constructor property promotion for brevity.
class Order
{
   public function __construct(
      public $placed_on,
      public $name,
      public $email,
      public $number,
      public $address,
      public $method,
      public $total_products,
      public $total_price,
      public $payment_status
   ) {
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">
</head>

<body>
   <?php include 'components/user_header.php'; ?>
   <section class="orders">
      <h1 class="heading">Placed orders</h1>
      <div class="box-container">
         <?php if (!$user_id): ?>
            <p class="empty">Please login to see your orders</p>
         <?php else:
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
            $select_orders->execute([$user_id]);
            if ($select_orders->rowCount() > 0):
               while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)):
                  // Spread the indexed values so that they match the constructor order.
                  $order = new Order(...array_values($fetch_orders));
                  ?>
                  <div class="box">
                     <p>Placed on : <span><?= htmlspecialchars($order->placed_on) ?></span></p>
                     <p>Name : <span><?= htmlspecialchars($order->name) ?></span></p>
                     <p>Email : <span><?= htmlspecialchars($order->email) ?></span></p>
                     <p>Number : <span><?= htmlspecialchars($order->number) ?></span></p>
                     <p>Address : <span><?= htmlspecialchars($order->address) ?></span></p>
                     <p>Payment method : <span><?= htmlspecialchars($order->method) ?></span></p>
                     <p>Your orders : <span><?= htmlspecialchars($order->total_products) ?></span></p>
                     <p>Total price : <span>₹<?= htmlspecialchars($order->total_price) ?>/-</span></p>
                     <p>Payment status : <span style="color:<?= ($order->payment_status === 'pending' ? 'red' : 'green') ?>">
                           <?= htmlspecialchars($order->payment_status) ?>
                        </span></p>
                  </div>
               <?php endwhile; else: ?>
               <p class="empty">No orders placed yet!</p>
            <?php endif; endif; ?>
      </div>
   </section>
   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>

</html>