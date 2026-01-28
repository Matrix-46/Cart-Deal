<?php

include '../components/connect.php';

session_start();

// AUTO-MIGRATION: Check if email column exists, if not, run migration automatically
try {
   $check_email_column = $conn->query("SHOW COLUMNS FROM `admins` LIKE 'email'");

   if ($check_email_column->rowCount() == 0) {
      // Email column doesn't exist - run migration automatically

      // Add email column
      $conn->exec("ALTER TABLE `admins` ADD COLUMN `email` VARCHAR(100) NULL AFTER `name`");

      // Add is_superadmin column
      $conn->exec("ALTER TABLE `admins` ADD COLUMN `is_superadmin` TINYINT(1) DEFAULT 0 AFTER `password`");

      // Add created_at column
      $conn->exec("ALTER TABLE `admins` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `is_superadmin`");

      // Modify column sizes
      $conn->exec("ALTER TABLE `admins` MODIFY COLUMN `name` VARCHAR(50) NOT NULL");
      $conn->exec("ALTER TABLE `admins` MODIFY COLUMN `password` VARCHAR(255) NOT NULL");

      // Set default emails for existing admins
      $conn->exec("UPDATE `admins` SET `email` = CONCAT(LOWER(name), '@cartdeal.local') WHERE `email` IS NULL");

      // Make first admin superadmin
      $conn->exec("UPDATE `admins` SET `is_superadmin` = 1 WHERE `id` = 1");

      // Check if superadmin with specific email exists, if not create it
      $checkSuperAdmin = $conn->prepare("SELECT * FROM `admins` WHERE email = ?");
      $checkSuperAdmin->execute(['abhinandan@admin.com']);

      if ($checkSuperAdmin->rowCount() == 0) {
         // Create the specific superadmin
         $hashedPass = password_hash('123456', PASSWORD_DEFAULT);
         $insertSuperAdmin = $conn->prepare("INSERT INTO `admins` (name, email, password, is_superadmin) VALUES (?, ?, ?, ?)");
         $insertSuperAdmin->execute(['Abhinandan', 'abhinandan@admin.com', $hashedPass, 1]);
      }

      // Add unique constraint
      try {
         $conn->exec("ALTER TABLE `admins` ADD UNIQUE KEY `unique_email` (`email`)");
      } catch (PDOException $e) {
         // Constraint might already exist, ignore
      }

      $migration_success = true;
   }
} catch (PDOException $e) {
   // Migration failed, but continue to login page
}

// LOGIN LOGIC
if (isset($_POST['submit'])) {

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Validate email format
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $message[] = 'Invalid email format!';
   } else {
      // Check if admin exists
      $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE email = ?");
      $select_admin->execute([$email]);

      if ($select_admin->rowCount() > 0) {
         $row = $select_admin->fetch(PDO::FETCH_ASSOC);

         // Verify password (support both old SHA1 and new bcrypt)
         $pass_verify = false;
         if (strlen($row['password']) == 40) {
            // Old SHA1 hash
            $pass_verify = (sha1($pass) === $row['password']);
         } else {
            // New bcrypt hash
            $pass_verify = password_verify($pass, $row['password']);
         }

         if ($pass_verify) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['is_superadmin'] = $row['is_superadmin'];
            header('location:dashboard.php');
         } else {
            $message[] = 'Incorrect email or password!';
         }
      } else {
         $message[] = 'Incorrect email or password!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css?v=1.1">
   <link rel="stylesheet" href="../css/admin_enhancement.css?v=1.1">

</head>

<body>

   <?php
   if (isset($migration_success)) {
      echo '
      <div class="message" style="background: #d4edda; color: #155724;">
         <span>✅ Database updated successfully! You can now login.</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }

   if (isset($message)) {
      foreach ($message as $msg) {
         echo '
         <div class="message">
            <span>' . $msg . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
   ?>

   <section class="form-container">

      <form action="" method="post" id="loginForm">
         <h3>Admin Login</h3>
         <p>Welcome to Cart-Deal Admin Panel</p>
         <input type="email" name="email" required placeholder="Enter your email" class="box" id="email">
         <input type="password" name="pass" required placeholder="Enter your password" maxlength="50" class="box"
            id="password">
         <input type="submit" value="Login Now" class="btn" name="submit">
      </form>

   </section>

   <script>
      // Email validation
      document.getElementById('email').addEventListener('blur', function () {
         const email = this.value;
         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

         if (!emailRegex.test(email)) {
            this.style.borderColor = 'red';
         } else {
            this.style.borderColor = '#d90429';
         }
      });

      // Form validation
      document.getElementById('loginForm').addEventListener('submit', function (e) {
         const email = document.getElementById('email').value;
         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

         if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address');
            return false;
         }
      });
   </script>

</body>

</html>