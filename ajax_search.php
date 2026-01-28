<?php
include 'components/connect.php';
include 'trie.php';

// Get the search query from the URL parameters
$query = $_GET['query'] ?? '';
if (!$query) {
  echo '';
  exit;
}

// Initialize the Trie data structure
$trie = new Trie();

// Populate the Trie with product names and their IDs from the database
$products = $conn->query("SELECT id, name FROM products");
while ($row = $products->fetch(PDO::FETCH_ASSOC)) {
  $trie->insert($row['name'], $row['id']);
}

// Search for matching product IDs using the Trie
$productIds = $trie->search($query);
if (!empty($productIds)) {
  // Prepare a statement with as many placeholders as the number of ids found
  $placeholders = implode(',', array_fill(0, count($productIds), '?'));
  $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
  $stmt->execute($productIds);
  if ($stmt->rowCount() > 0) {
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <form action="" method="post" class="box">
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
          <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;"
            value="1">
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
?>