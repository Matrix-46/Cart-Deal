<?php

include 'components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
   header('location:user_login.php');
   exit;
}
$user_id = $_SESSION['user_id'];

if (isset($_POST['order'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = filter_var('flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);
   if ($check_cart->rowCount() > 0) {
      $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price) VALUES (?,?,?,?,?,?,?,?)")
         ->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);
      $conn->prepare("DELETE FROM `cart` WHERE user_id = ?")->execute([$user_id]);
      $message[] = 'Order placed successfully!';
   } else {
      $message[] = 'Your cart is empty';
   }
}

class CartItem
{
   public $name, $price, $quantity;
   public function __construct($data)
   {
      $this->name = $data['name'];
      $this->price = $data['price'];
      $this->quantity = $data['quantity'];
   }
   public function __toString()
   {
      return "$this->name ($this->price x $this->quantity) - ";
   }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">
</head>

<body>
   <?php include 'components/user_header.php'; ?>
   <section class="checkout-orders">
      <form action="" method="POST">
         <h3>Your orders</h3>
         <div class="display-orders">
            <?php
            $grand_total = 0;
            $cartItems = [];
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($row = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $item = new CartItem($row);
                  $cartItems[] = $item;
                  $grand_total += $row['price'] * $row['quantity'];
                  echo '<p>' . htmlspecialchars($item->name) . ' <span>(₹' . htmlspecialchars($item->price) . '/- x ' . htmlspecialchars($item->quantity) . ')</span></p>';
               }
               $total_products = implode('', $cartItems);
            } else {
               echo '<p class="empty">Your cart is empty!</p>';
               $total_products = '';
            }
            ?>
            <input type="hidden" name="total_products" value="<?= htmlspecialchars($total_products); ?>">
            <input type="hidden" name="total_price" value="<?= htmlspecialchars($grand_total); ?>">
            <div class="grand-total">grand total : <span>₹<?= htmlspecialchars($grand_total); ?>/-</span></div>
         </div>
         <h3>Place your orders</h3>
         <div class="flex">
            <div class="inputBox">
               <span>Your name :</span>
               <input type="text" name="name" placeholder="Enter your name" class="box" maxlength="20" required>
            </div>
            <div class="inputBox">
               <span>Your number :</span>
               <input type="number" name="number" placeholder="Enter your number" class="box" min="0" max="9999999999"
                  onkeypress="if(this.value.length == 10)return false;" required>
            </div>
            <div class="inputBox">
               <span>Your email :</span>
               <input type="email" name="email" placeholder="Enter your email" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>Payment method :</span>
               <select name="method" class="box" required>
                  <option value="cash on delivery">Cash on delivery</option>
                  <option value="credit card">Credit card</option>
                  <option value="paytm">Paytm</option>
               </select>
            </div>
            <div class="inputBox">
               <span>Address line 01 :</span>
               <input type="text" name="flat" placeholder="e.g. flat number" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>Address line 02 :</span>
               <input type="text" name="street" placeholder="e.g. street name" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>City :</span>
               <input type="text" name="city" placeholder="e.g. bangalore" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>State :</span>
               <input type="text" name="state" placeholder="e.g. karnataka" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>Country :</span>
               <input type="text" name="country" placeholder="e.g. India" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>Pin code :</span>
               <input type="number" name="pin_code" placeholder="e.g. 123456" min="0" max="999999"
                  onkeypress="if(this.value.length == 6)return false;" class="box" required>
            </div>
         </div>
         <input type="submit" name="order" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>" value="place order">
      </form>
   </section>
   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>

</html>