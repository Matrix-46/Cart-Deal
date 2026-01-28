<?php
include 'components/connect.php';
session_start();
if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   header('location:user_login.php');
   exit;
}

if (isset($_POST['delete'])) {
   $conn->prepare("DELETE FROM `cart` WHERE id = ?")->execute([$_POST['cart_id']]);
}
if (isset($_GET['delete_all'])) {
   $conn->prepare("DELETE FROM `cart` WHERE user_id = ?")->execute([$user_id]);
   header('location:cart.php');
   exit;
}
if (isset($_POST['update_qty'])) {
   $qty = filter_var($_POST['qty'], FILTER_SANITIZE_STRING);
   $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?")->execute([$qty, $_POST['cart_id']]);
   $message[] = 'Cart quantity updated';
}

// Data Structure for a Cart item.
class CartItem
{
   public $id, $pid, $name, $price, $image, $quantity;
   public function __construct($data)
   {
      $this->id = $data['id'];
      $this->pid = $data['pid'];
      $this->name = $data['name'];
      $this->price = $data['price'];
      $this->image = $data['image'];
      $this->quantity = $data['quantity'];
   }
}

// Create collection of cart items.
$cartItems = [];
$stmt = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$stmt->execute([$user_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $cartItems[] = new CartItem($row);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Shopping cart</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
</head>

<body>
   <?php include 'components/user_header.php'; ?>
   <section class="products shopping-cart">
      <h3 class="heading">Shopping cart</h3>
      <div class="box-container">
         <?php
         $grand_total = 0;
         if ($cartItems) {
            foreach ($cartItems as $item) {
               $sub_total = $item->price * $item->quantity;
               ?>
               <form action="" method="post" class="box">
                  <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item->id); ?>">
                  <a href="quick_view.php?pid=<?= htmlspecialchars($item->pid); ?>" class="fas fa-eye"></a>
                  <img src="uploaded_img/<?= htmlspecialchars($item->image); ?>" alt="">
                  <div class="name"><?= htmlspecialchars($item->name); ?></div>
                  <div class="flex">
                     <div class="price">₹<?= htmlspecialchars($item->price); ?>/-</div>
                     <input type="number" name="qty" class="qty" min="1" max="99"
                        onkeypress="if(this.value.length==2)return false;" value="<?= $item->quantity; ?>">
                     <button type="submit" class="fas fa-edit" name="update_qty"></button>
                  </div>
                  <div class="sub-total"> sub total : <span>₹<?= htmlspecialchars($sub_total); ?>/-</span> </div>
                  <input type="submit" value="delete item" onclick="return confirm('Delete this from cart?');"
                     class="delete-btn" name="delete">
               </form>
               <?php
               $grand_total += $sub_total;
            }
         } else {
            echo '<p class="empty">Your cart is empty</p>';
         }
         ?>
      </div>
      <div class="cart-total">
         <p>Grand total : <span>₹<?= htmlspecialchars($grand_total); ?>/-</span></p>
         <a href="shop.php" class="option-btn">Continue shopping</a>
         <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>"
            onclick="return confirm('Delete all from cart?');">Delete all item</a>
         <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to checkout</a>
      </div>
   </section>
   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>

</html>