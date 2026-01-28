<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '
         <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="../admin/dashboard.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="../admin/dashboard.php">Home</a>
         <a href="../admin/products.php">Products</a>
         <a href="../admin/placed_orders.php">Orders</a>
         <a href="../admin/admin_accounts.php">Admins</a>
         <a href="../admin/users_accounts.php">Users</a>
         <a href="../admin/messages.php">Messages</a>
         <?php if (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1): ?>
            <a href="../admin/manage_admins.php" style="color: #d90429;">
               <i class="fas fa-users-cog"></i> Manage Admins
            </a>
         <?php endif; ?>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
         $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
         $select_profile->execute([$admin_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
         <?php if (isset($fetch_profile['email'])): ?>
            <p style="font-size: 1.4rem; color: #666;"><?= htmlspecialchars($fetch_profile['email']); ?></p>
         <?php endif; ?>
         <?php if (isset($fetch_profile['is_superadmin']) && $fetch_profile['is_superadmin'] == 1): ?>
            <p
               style="font-size: 1.4rem; background: gold; color: #333; padding: 0.5rem; border-radius: 0.5rem; margin: 0.5rem 0;">
               <i class="fas fa-crown"></i> Superadmin
            </p>
         <?php endif; ?>
         <a href="../admin/update_profile.php" class="btn">Update profile</a>
         <div class="flex-btn">
            <a href="../admin/admin_login.php" class="option-btn">Login</a>
         </div>
         <a href="../components/admin_logout.php" class="delete-btn"
            onclick="return confirm('Logout from the website?');">Logout</a>
      </div>

   </section>

</header>