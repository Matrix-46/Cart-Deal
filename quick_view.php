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
   <title>Quick view</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="quick-view">

      <h1 class="heading">Quick view</h1>

      <?php
      $pid = $_GET['pid'] ?? '';
      $stmt = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $stmt->execute([$pid]);
      if ($stmt->rowCount() > 0):
         while ($p = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <form method="post" class="box">
               <input type="hidden" name="pid" value="<?= htmlspecialchars($p['id']); ?>">
               <input type="hidden" name="name" value="<?= htmlspecialchars($p['name']); ?>">
               <input type="hidden" name="price" value="<?= htmlspecialchars($p['price']); ?>">
               <input type="hidden" name="image" value="<?= htmlspecialchars($p['image_01']); ?>">
               <div class="row">
                  <div class="image-container">
                     <div class="main-image">
                        <img src="uploaded_img/<?= htmlspecialchars($p['image_01']); ?>" alt="">
                     </div>
                     <div class="sub-image">
                        <img src="uploaded_img/<?= htmlspecialchars($p['image_01']); ?>" alt="">
                        <?php if (!empty($p['image_02'])): ?>
                           <img src="uploaded_img/<?= htmlspecialchars($p['image_02']); ?>" alt="">
                        <?php endif; ?>
                        <?php if (!empty($p['image_03'])): ?>
                           <img src="uploaded_img/<?= htmlspecialchars($p['image_03']); ?>" alt="">
                        <?php endif; ?>
                     </div>
                  </div>
                  <div class="content">
                     <div class="name"><?= htmlspecialchars($p['name']); ?></div>
                     <div class="flex">
                        <div class="price"><span>₹</span><?= htmlspecialchars($p['price']); ?><span>/-</span></div>
                        <input type="number" name="qty" class="qty" min="1" max="99"
                           onkeypress="if(this.value.length==2)return false;" value="1">
                     </div>
                     <div class="details"><?= htmlspecialchars($p['details']); ?></div>
                     <div class="flex-btn">
                        <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                        <input type="submit" value="add to wishlist"
                           class="option-btn <?= (in_array($p['id'], $wishlist_pids)) ? 'active' : ''; ?>"
                           name="add_to_wishlist">
                     </div>
                  </div>
               </div>
            </form>
         <?php endwhile; else: ?>
         <p class="empty">No products added yet!</p>
      <?php endif; ?>

   </section>

   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>