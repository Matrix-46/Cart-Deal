<?php

include 'components/connect.php';

session_start();
$user_id = $_SESSION['user_id'] ?? '';

// fetch profile
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
   header('location:user_login.php');
   exit();
}

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

   // update name and email
   $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?")
      ->execute([$name, $email, $user_id]);
   $message[] = 'Profile updated successfully!';

   $old_pass_input = filter_var($_POST['old_pass'], FILTER_SANITIZE_STRING);
   $new_pass = filter_var($_POST['new_pass'], FILTER_SANITIZE_STRING);
   $cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);

   if (!empty($old_pass_input)) {
      $pass_verified = false;
      if (strlen($fetch_profile['password']) == 40) {
         if (sha1($old_pass_input) === $fetch_profile['password'])
            $pass_verified = true;
      } else {
         if (password_verify($old_pass_input, $fetch_profile['password']))
            $pass_verified = true;
      }

      if (!$pass_verified) {
         $message[] = 'Old password not matched!';
      } elseif (empty($new_pass)) {
         $message[] = 'Please enter a new password!';
      } elseif ($new_pass != $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         $hashed_pass = password_hash($cpass, PASSWORD_DEFAULT);
         $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?")
            ->execute([$hashed_pass, $user_id]);
         $message[] = 'Password updated successfully!';
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
   <title>Register</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">
</head>

<body>
   <?php include 'components/user_header.php'; ?>
   <section class="form-container">
      <form action="" method="post">
         <h3>Update now</h3>
         <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box"
            value="<?= $fetch_profile["name"]; ?>">
         <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')" value="<?= $fetch_profile["email"]; ?>">
         <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="cpass" placeholder="Confirm your new password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="update now" class="btn" name="submit">
      </form>
   </section>
   <?php include 'components/footer.php'; ?>
   <script src="js/script.js"></script>
</body>

</html>