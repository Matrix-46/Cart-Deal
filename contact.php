<?php

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

if (isset($_POST['send'])) {

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $msg = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);

   // Simplified ContactMessage using constructor property promotion (if PHP 8+)
   class ContactMessage
   {
      public function __construct(public $name, public $email, public $number, public $msg)
      {
      }
   }
   $cm = new ContactMessage($name, $email, $number, $msg);

   $stmt = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $stmt->execute([$cm->name, $cm->email, $cm->number, $cm->msg]);

   if ($stmt->rowCount()) {
      $message[] = 'Already sent message!';
   } else {
      $stmt = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
      $stmt->execute([$user_id, $cm->name, $cm->email, $cm->number, $cm->msg]);
      $message[] = 'Sent message successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhancement.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="contact">

      <form action="" method="post">
         <h3>Get in touch</h3>
         <input type="text" name="name" placeholder="Enter your name" required maxlength="20" class="box">
         <input type="email" name="email" placeholder="Enter your email" required maxlength="50" class="box">
         <input type="number" name="number" min="0" max="9999999999" placeholder="Enter your number" required
            onkeypress="if(this.value.length == 10) return false;" class="box">
         <textarea name="msg" class="box" placeholder="Enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

   </section>

   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>