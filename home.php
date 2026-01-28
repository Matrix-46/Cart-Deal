<?php
include 'components/connect.php';
session_start();
$user_id = $_SESSION['user_id'] ?? '';
include 'components/wishlist_cart.php';

class HomeProduct
{
   public function __construct(public $id, public $name, public $price, public $image)
   {
   }
}

$latestProducts = [];
$stmt = $conn->prepare("SELECT * FROM `products` LIMIT 6");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $latestProducts[] = new HomeProduct($row['id'], $row['name'], $row['price'], $row['image_01']);
}

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
   <title>Home</title>
   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">
</head>

<body>
   <?php include 'components/user_header.php'; ?>
   <div class="home-bg">
      <section class="home">
         <div class="swiper home-slider">
            <div class="swiper-wrapper">
               <div class="swiper-slide slide">
                  <div class="image"><img src="images/Iphone.png" alt=""></div>
                  <div class="content"><span>Upto 30% off</span>
                     <h3>Latest smartphones</h3><a href="shop.php" class="btn">Shop now</a>
                  </div>
               </div>
               <div class="swiper-slide slide">
                  <div class="image"><img src="images/Smartwatch.png" alt=""></div>
                  <div class="content"><span>Upto 50% off</span>
                     <h3>Latest Watches & Smartwatches</h3><a href="shop.php" class="btn">Shop now</a>
                  </div>
               </div>
               <div class="swiper-slide slide">
                  <div class="image"><img src="images/Earbuds.png" alt=""></div>
                  <div class="content"><span>Upto 63% off</span>
                     <h3>Latest Earbuds & Head</h3><a href="shop.php" class="btn">Shop now</a>
                  </div>
               </div>
            </div>
            <div class="swiper-pagination"></div>
         </div>
      </section>
   </div>
   <section class="category">
      <h1 class="heading">Shop by category</h1>
      <div class="swiper category-slider">
         <div class="swiper-wrapper">
            <a href="category.php?category=laptop" class="swiper-slide slide"><img src="images/icon-1.svg?v=1.3" alt="">
               <h3>Laptop</h3>
            </a>
            <a href="category.php?category=tv" class="swiper-slide slide"><img src="images/icon-2.svg?v=1.3" alt="">
               <h3>TV</h3>
            </a>
            <a href="category.php?category=camera" class="swiper-slide slide"><img src="images/icon-3.svg?v=1.3" alt="">
               <h3>Camera</h3>
            </a>
            <a href="category.php?category=mouse" class="swiper-slide slide"><img src="images/icon-4.svg?v=1.3" alt="">
               <h3>Mouse</h3>
            </a>
            <a href="category.php?category=fridge" class="swiper-slide slide"><img src="images/icon-5.svg?v=1.3" alt="">
               <h3>Fridge</h3>
            </a>
            <a href="category.php?category=washing" class="swiper-slide slide"><img src="images/icon-6.svg?v=1.3"
                  alt="">
               <h3>Washing machine</h3>
            </a>
            <a href="category.php?category=smartphone" class="swiper-slide slide"><img src="images/icon-7.svg?v=1.3"
                  alt="">
               <h3>Smartphone</h3>
            </a>
            <a href="category.php?category=watch" class="swiper-slide slide"><img src="images/icon-8.svg?v=1.3" alt="">
               <h3>Watch</h3>
            </a>
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>
   <section class="home-products">
      <h1 class="heading">Latest products</h1>
      <div class="swiper products-slider">
         <div class="swiper-wrapper">
            <?php if ($latestProducts):
               foreach ($latestProducts as $p): ?>
                  <form action="" method="post" class="swiper-slide slide">
                     <input type="hidden" name="pid" value="<?= htmlspecialchars($p->id); ?>">
                     <input type="hidden" name="name" value="<?= htmlspecialchars($p->name); ?>">
                     <input type="hidden" name="price" value="<?= htmlspecialchars($p->price); ?>">
                     <input type="hidden" name="image" value="<?= htmlspecialchars($p->image); ?>">
                     <?php
                     $is_in_wishlist = in_array($p->id, $wishlist_pids) ? 'active' : '';
                     ?>
                     <button class="fas fa-heart <?= $is_in_wishlist; ?>" type="submit" name="add_to_wishlist"></button>
                     <a href="quick_view.php?pid=<?= $p->id; ?>" class="fas fa-eye"></a>
                     <img src="uploaded_img/<?= htmlspecialchars($p->image); ?>" alt="">
                     <div class="name"><?= htmlspecialchars($p->name); ?></div>
                     <div class="flex">
                        <div class="price"><span>₹</span><?= htmlspecialchars($p->price); ?><span>/-</span></div>
                        <input type="number" name="qty" class="qty" min="1" max="99"
                           onkeypress="if(this.value.length==2)return false;" value="1">
                     </div>
                     <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                  </form>
               <?php endforeach; else:
               echo '<p class="empty">No products added yet!</p>';
            endif; ?>
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>
   <?php include 'components/footer.php'; ?>
   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
   <script src="js/script.js"></script>
   <script>
      var swiper1 = new Swiper(".home-slider", { loop: false, spaceBetween: 15, pagination: { el: ".swiper-pagination", clickable: true } });
      var swiper2 = new Swiper(".category-slider", { loop: false, spaceBetween: 15, pagination: { el: ".swiper-pagination", clickable: true }, breakpoints: { 0: { slidesPerView: 2 }, 650: { slidesPerView: 3 }, 768: { slidesPerView: 4 }, 1024: { slidesPerView: 5 } } });
      var swiper3 = new Swiper(".products-slider", { loop: false, spaceBetween: 20, pagination: { el: ".swiper-pagination", clickable: true }, breakpoints: { 550: { slidesPerView: 2 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } } });
   </script>
</body>

</html>