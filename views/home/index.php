<!DOCTYPE html>
<html lang="ja">
<head>
    <title>geo-slim-php</title>

    <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/home.css">

    <script src="/js/libs/popper.min.js"></script>
    <script src="js/libs/jquery-3.4.1.js"></script>
    <script src="js/libs/bootstrap.js"></script>
    <script src="js/libs/Http.js"></script>
    <script src="js/libs/sweetalert2.all.min.js"></script>

    <style>
        .main-container {
            word-wrap: break-word;
            padding: 5%;
        }
    </style>

</head>
<body>
    <div class="main-container" style="text-align:center">
        <h1><?php echo $title; ?></h1>

        <br>
        <h2 style="text-decoration:underline;text-align:center;">User1's news feed (Total Count: <?php print(count($user1_news_feed)); ?>)</h2>
        <?php foreach($user1_news_feed as $post): ?>
        <span><?php echo $post; ?></span>
        <?php foreach($post->images as $image): ?>
        <img src="<?php echo $image->post_image_url; ?>" alt="An image" height="300px;">
        <?php endforeach; ?>
        <br>
        <?php endforeach; ?>
    

        <h2 style="text-decoration:underline;text-align:center;">All posts (Total Count: <?php print(count($posts)); ?>)</h2>
        <?php foreach($posts as $post): ?>
        <span><?php echo $post; ?></span>
        <?php foreach($post->images as $image): ?>
        <img src="<?php echo $image->post_image_url; ?>" alt="An image" height="300px;">
        <?php endforeach; ?>
        <br>
        <?php endforeach; ?>

        <h2 style="text-decoration:underline;text-align:center;">Users (Total Count: <?php print(count($users)); ?>)</h2>
        <?php foreach($users as $user): ?>
            <span><?php echo $user; ?></span>
            <br>
        <?php endforeach; ?>
    </div>

    <script>
    </script>
</body>
</html>
