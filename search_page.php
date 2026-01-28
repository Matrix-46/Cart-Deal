<?php

include 'components/connect.php';
include 'trie.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

include 'components/wishlist_cart.php';

$trie = new Trie();
foreach ($conn->query("SELECT id, name FROM products") as $row) {
   $trie->insert($row['name'], $row['id']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">
</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="search-form">
      <form method="post">
         <input type="text" name="search_box" id="search_box" placeholder="Search here..." maxlength="100" class="box"
            required oninput="debouncedSearch()">
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>
   </section>

   <section class="products" style="padding-top: 0; min-height:100vh;">

      <div class="box-container" id="search-results">
         <?php
         if (!empty($_POST['search_box'])) {
            $search_box = $_POST['search_box'];
            $ids = $trie->search($search_box);
            if ($ids) {
               $placeholders = implode(',', array_fill(0, count($ids), '?'));
               $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
               $stmt->execute($ids);
               if ($stmt->rowCount() > 0) {
                  while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                     ?>
                     <form method="post" class="box">
                        <input type="hidden" name="pid" value="<?= htmlspecialchars($product['id']); ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="price" value="<?= htmlspecialchars($product['price']); ?>">
                        <input type="hidden" name="image" value="<?= htmlspecialchars($product['image_01']); ?>">
                        <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                        <a href="quick_view.php?pid=<?= htmlspecialchars($product['id']); ?>" class="fas fa-eye"></a>
                        <img src="uploaded_img/<?= htmlspecialchars($product['image_01']); ?>" alt="">
                        <div class="name"><?= htmlspecialchars($product['name']); ?></div>
                        <div class="flex">
                           <div class="price"><span>₹</span><?= htmlspecialchars($product['price']); ?><span>/-</span></div>
                           <input type="number" name="qty" class="qty" min="1" max="99"
                              onkeypress="if(this.value.length == 2) return false;" value="1">
                        </div>
                        <input type="submit" value="Add to Cart" class="btn" name="add_to_cart">
                     </form>
                     <?php
                  }
               } else {
                  echo '<p class="empty">No products found!</p>';
               }
            } else {
               echo '<p class="empty">No products found!</p>';
            }
         }
         ?>
      </div>

   </section>

   <?php include 'components/footer.php'; ?>

   <script>
      let debounceTimeout;
      function debouncedSearch() {
         clearTimeout(debounceTimeout);
         debounceTimeout = setTimeout(() => {
            const query = document.getElementById('search_box').value.trim();
            if (query) fetchSearchResults(query);
         }, 300);
      }

      async function fetchSearchResults(query) {
         const response = await fetch('ajax_search.php?query=' + encodeURIComponent(query));
         document.getElementById('search-results').innerHTML = await response.text();
      }
   </script>

   <script src="js/script.js"></script>

</body>

</html>