<?php

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';
$message = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
    $cpass = filter_input(INPUT_POST, 'cpass', FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$email]);

    if ($select_user->rowCount() > 0) {
        $message[] = 'Email already exists!';
    } elseif ($pass !== $cpass) {
        $message[] = 'Confirm password not matched!';
    } else {
        $hashed_pass = password_hash($cpass, PASSWORD_DEFAULT);
        $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES (?,?,?)");
        $insert_user->execute([$name, $email, $hashed_pass]);
        $message[] = 'Registered successfully, login now please!';
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
            <h3>Register now</h3>
            <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box">
            <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required placeholder="Confirm your password" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="register now" class="btn" name="submit">
            <p>already have an account?</p>
            <a href="user_login.php" class="option-btn">Login now</a>
        </form>
    </section>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>

</html>