<?php

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

// AUTO-MIGRATION: Ensure users table password field is long enough for Bcrypt
try {
   $check_pass_column = $conn->query("SHOW COLUMNS FROM `users` LIKE 'password'");
   $column_info = $check_pass_column->fetch(PDO::FETCH_ASSOC);

   // If the type is varchar(50) or smaller, expand it
   if ($column_info && strpos($column_info['Type'], 'varchar(50)') !== false) {
      $conn->exec("ALTER TABLE `users` MODIFY COLUMN `password` VARCHAR(255) NOT NULL");
   }
} catch (PDOException $e) {
   // Silent failure if migration fails
}

if (isset($_POST['submit'])) {
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);

   if ($row = $select_user->fetch(PDO::FETCH_ASSOC)) {
      $pass_verified = false;

      if (strlen($row['password']) == 40) {
         // Old SHA1 hash - check it
         if (sha1($pass) === $row['password']) {
            // SHA1 matched, now upgrade to bcrypt
            $new_hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_hashed_pass, $row['id']]);
            $pass_verified = true;
         }
      } else {
         // Modern bcrypt hash
         if (password_verify($pass, $row['password'])) {
            $pass_verified = true;
         }
      }

      if ($pass_verified) {
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
         exit();
      } else {
         $message[] = 'Incorrect email or password!';
      }
   } else {
      $message[] = 'Incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="form-container">

      <form action="" method="post">
         <h3>Login now</h3>
         <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="login now" class="btn" name="submit">
         <p>Don't have an account?</p>
         <a href="user_register.php" class="option-btn">Register now</a>
      </form>

   </section>

   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>