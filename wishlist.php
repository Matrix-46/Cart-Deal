<?php
include 'components/connect.php';
session_start();

// Redirect to login if user_id is missing
if (empty($_SESSION['user_id'])) {
   header('location:user_login.php');
   exit;
}
$user_id = $_SESSION['user_id'];

include 'components/wishlist_cart.php';

// Delete individual wishlist item
if (isset($_POST['delete'])) {
   $stmt = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $stmt->execute([$_POST['wishlist_id']]);
}

// Delete all wishlist items
if (isset($_GET['delete_all'])) {
   $stmt = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $stmt->execute([$user_id]);
   header('location:wishlist.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Wishlist</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">
</head>

<body>
   <?php include 'components/user_header.php'; ?>

   <section class="products">
      <h3 class="heading">Your wishlist</h3>
      <div class="box-container">
         <?php
         $grand_total = 0;
         $stmt = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
         $stmt->execute([$user_id]);
         if ($stmt->rowCount()):
            while ($item = $stmt->fetch(PDO::FETCH_ASSOC)):
               $grand_total += $item['price'];
               ?>
               <form action="" method="post" class="box">
                  <input type="hidden" name="pid" value="<?= htmlspecialchars($item['pid']); ?>">
                  <input type="hidden" name="wishlist_id" value="<?= htmlspecialchars($item['id']); ?>">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']); ?>">
                  <input type="hidden" name="price" value="<?= htmlspecialchars($item['price']); ?>">
                  <input type="hidden" name="image" value="<?= htmlspecialchars($item['image']); ?>">
                  <a href="quick_view.php?pid=<?= htmlspecialchars($item['pid']); ?>" class="fas fa-eye"></a>
                  <img src="uploaded_img/<?= htmlspecialchars($item['image']); ?>" alt="">
                  <div class="name"><?= htmlspecialchars($item['name']); ?></div>
                  <div class="flex">
                     <div class="price">₹<?= htmlspecialchars($item['price']); ?>/-</div>
                     <input type="number" name="qty" class="qty" min="1" max="99"
                        onkeypress="if(this.value.length == 2)return false;" value="1">
                  </div>
                  <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                  <input type="submit" value="delete item" onclick="return confirm('delete this from wishlist?');"
                     class="delete-btn" name="delete">
               </form>
            <?php endwhile; else: ?>
            <p class="empty">Your wishlist is empty</p>
         <?php endif; ?>
      </div>

      <div class="wishlist-total">
         <p>grand total : <span>₹<?= htmlspecialchars($grand_total); ?>/-</span></p>
         <a href="shop.php" class="option-btn">Continue shopping</a>
         <a href="wishlist.php?delete_all" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>"
            onclick="return confirm('delete all from wishlist?');">delete all item</a>
      </div>
   </section>

   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>

</html>