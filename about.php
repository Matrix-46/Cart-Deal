<?php
include 'components/connect.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About</title>
  <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <?php include 'components/user_header.php'; ?>

  <section class="about">
    <div class="row">
      <div class="image"><img src="images/About1.jpg" alt="" /></div>
      <div class="content">
        <h3>Why choose us?</h3>
        <p>
          We are immensely happy in rendering services to a wide range of clients who believe in our spotless and most
          reliable services. Be it small or large industry, startups or big corporates, today everyone needs digital
          marketing services to harvest maximum benefits.
        </p>
        <p>
          Why Choose Us? Best SEO Services from us for maximum remunerations in the digital world, be it branding,
          marketing strategies or any major objectives of company. Our strategy is designed in a way that engages
          maximum growth and strong online branding.
        </p>
        <a href="contact.php" class="btn">contact us</a>
      </div>
    </div>
  </section>

  <section class="reviews">
    <h1 class="heading">Client's reviews</h1>
    <div class="swiper reviews-slider">
      <div class="swiper-wrapper">
        <?php
        $reviews = [
          ['img' => 'images/R1.jpg', 'review' => 'I have been ordering in this for three years now and i am satisfied with the quality of the Spirulina and the price of course is the best I have seen in the market.', 'rating' => 4.5, 'name' => 'Aromal.S'],
          ['img' => 'images/R2.jpg', 'review' => 'Very good service and good collection in stock. Almost all varities shown about daily need products. So afordable. We are satisfied.', 'rating' => 4.5, 'name' => 'Naveen Kumar.J'],
          ['img' => 'images/R3.jpg', 'review' => 'There is always room for improvement, but it will get the job done', 'rating' => 4.5, 'name' => 'Akshay.P'],
          ['img' => 'images/R4.jpg', 'review' => 'This application works good as a searching engine or tool, it opens fast .', 'rating' => 4.5, 'name' => 'Harsha.G.S']
        ];
        foreach ($reviews as $review): ?>
          <div class="swiper-slide slide">
            <img src="<?= htmlspecialchars($review['img']); ?>" alt="" />
            <p><?= htmlspecialchars($review['review']); ?></p>
            <div class="stars">
              <?php
              $fullStars = floor($review['rating']);
              for ($i = 0; $i < $fullStars; $i++)
                echo '<i class="fas fa-star"></i>';
              if (($review['rating'] - $fullStars) >= 0.5)
                echo '<i class="fas fa-star-half-alt"></i>';
              ?>
            </div>
            <h3><?= htmlspecialchars($review['name']); ?></h3>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </section>

  <?php include 'components/footer.php'; ?>

  <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
  <script src="js/script.js"></script>
  <script>
    var swiper = new Swiper(".reviews-slider", {
      loop: true,
      spaceBetween: 15,
      pagination: { el: ".swiper-pagination", clickable: true },
      breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 } }
    });
  </script>
</body>

</html>