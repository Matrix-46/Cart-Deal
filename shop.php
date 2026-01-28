<?php

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

include 'components/wishlist_cart.php';

$wishlist_pids = [];
if ($user_id != '') {
   $stmt = $conn->prepare("SELECT pid FROM `wishlist` WHERE user_id = ?");
   $stmt->execute([$user_id]);
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $wishlist_pids[] = $row['pid'];
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="products">

      <h1 class="heading">Latest products</h1>

      <div class="box-container">

         <?php
         $stmt = $conn->prepare("SELECT * FROM `products`");
         $stmt->execute();
         if ($stmt->rowCount() > 0):
            while ($p = $stmt->fetch(PDO::FETCH_ASSOC)):
               ?>
               <form action="" method="post" class="box">
                  <input type="hidden" name="pid" value="<?= htmlspecialchars($p['id']) ?>">
                  <input type="hidden" name="name" value="<?= htmlspecialchars($p['name']) ?>">
                  <input type="hidden" name="price" value="<?= htmlspecialchars($p['price']) ?>">
                  <input type="hidden" name="image" value="<?= htmlspecialchars($p['image_01']) ?>">
                  <?php
                  $is_in_wishlist = in_array($p['id'], $wishlist_pids) ? 'active' : '';
                  ?>
                  <button class="fas fa-heart <?= $is_in_wishlist; ?>" type="submit" name="add_to_wishlist"></button>
                  <a href="quick_view.php?pid=<?= htmlspecialchars($p['id']) ?>" class="fas fa-eye"></a>
                  <img src="uploaded_img/<?= htmlspecialchars($p['image_01']) ?>" alt="">
                  <div class="name"><?= htmlspecialchars($p['name']) ?></div>
                  <div class="flex">
                     <div class="price"><span>₹</span><?= htmlspecialchars($p['price']) ?><span>/-</span></div>
                     <input type="number" name="qty" class="qty" min="1" max="99"
                        onkeypress="if(this.value.length==2)return false;" value="1">
                  </div>
                  <input type="submit" value="add to cart" class="btn" name="add_to_cart">
               </form>
            <?php endwhile; else: ?>
            <p class="empty">No products found!</p>
         <?php endif; ?>
      </div>

   </section>

   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>