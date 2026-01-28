<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

// fetch profile
$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
   header('location:admin_login.php');
   exit();
}

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $update_profile_name = $conn->prepare("UPDATE `admins` SET name = ? WHERE id = ?");
   $update_profile_name->execute([$name, $admin_id]);
   $message[] = 'Name updated successfully!';

   $old_pass_input = filter_var($_POST['old_pass'], FILTER_SANITIZE_STRING);
   $new_pass = filter_var($_POST['new_pass'], FILTER_SANITIZE_STRING);
   $confirm_pass = filter_var($_POST['confirm_pass'], FILTER_SANITIZE_STRING);

   if (!empty($old_pass_input)) {
      $pass_verified = false;
      if (strlen($fetch_profile['password']) == 40) { // Check if it's a SHA1 hash (length 40)
         if (sha1($old_pass_input) === $fetch_profile['password'])
            $pass_verified = true;
      } else { // Assume it's a bcrypt hash
         if (password_verify($old_pass_input, $fetch_profile['password']))
            $pass_verified = true;
      }

      if (!$pass_verified) {
         $message[] = 'Old password not matched!';
      } elseif (empty($new_pass)) {
         $message[] = 'Please enter a new password!';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = 'Confirm password not matched!';
      } else {
         $hashed_pass = password_hash($confirm_pass, PASSWORD_DEFAULT);
         $update_admin_pass = $conn->prepare("UPDATE `admins` SET password = ? WHERE id = ?");
         $update_admin_pass->execute([$hashed_pass, $admin_id]);
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
   <title>Update profile</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css?v=1.1">
   <link rel="stylesheet" href="../css/admin_enhancement.css?v=1.1">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="form-container">

      <form action="" method="post">
         <h3>Update profile</h3>
         <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" required
            placeholder="enter your username" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="old_pass" placeholder="Enter old password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="new_pass" placeholder="Enter new password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="confirm_pass" placeholder="Confirm new password" maxlength="20" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="update now" class="btn" name="submit">
      </form>

   </section>

   <script src="../js/admin_script.js"></script>

</body>

</html>