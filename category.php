<?php
include 'components/connect.php';
session_start();
$user_id = $_SESSION['user_id'] ?? '';
include 'components/wishlist_cart.php';
$current_category = $_GET['category'] ?? '';

// Product class to encapsulate product data
class Product
{
   public $id, $name, $price, $image, $category;
   public function __construct($data)
   {
      $this->id = $data['id'];
      $this->name = $data['name'];
      $this->price = $data['price'];
      $this->image = $data['image_01'];
      $this->category = $data['category'];
   }
}

$productsList = [];
if ($current_category) {
   try {
      $stmt = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
      $stmt->execute([$current_category]);
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $productsList[] = new Product($row);
      }
   } catch (PDOException $e) {
      // Error handled by showing empty message below
   }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Category</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="css/style.css" />
</head>

<body>
   <?php include 'components/user_header.php'; ?>
   <section class="products">
      <h1 class="heading">Category: <?= htmlspecialchars($current_category) ?></h1>
      <div class="box-container">
         <?php if ($current_category) {
            if (count($productsList) > 0) {
               foreach ($productsList as $product) { ?>
                  <form action="" method="post" class="box">
                     <input type="hidden" name="pid" value="<?= htmlspecialchars($product->id); ?>">
                     <input type="hidden" name="name" value="<?= htmlspecialchars($product->name); ?>">
                     <input type="hidden" name="price" value="<?= htmlspecialchars($product->price); ?>">
                     <input type="hidden" name="image" value="<?= htmlspecialchars($product->image); ?>">
                     <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                     <a href="quick_view.php?pid=<?= htmlspecialchars($product->id); ?>" class="fas fa-eye"></a>
                     <img src="uploaded_img/<?= htmlspecialchars($product->image); ?>" alt="">
                     <div class="name"><?= htmlspecialchars($product->name); ?></div>
                     <div class="flex">
                        <div class="price"><span>₹</span><?= htmlspecialchars($product->price); ?><span>/-</span></div>
                        <input type="number" name="qty" class="qty" min="1" max="99" value="1">
                     </div>
                     <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                  </form>
               <?php }
            } else {
               echo '<p class="empty">No products found in this category.</p>';
            }
         } else {
            echo '<p class="empty">No products found in this category.</p>';
         } ?>
      </div>
   </section>
   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>

</html>