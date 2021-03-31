<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- For the infinite scrolling posts section -->
    <link rel="stylesheet" href="css/posts-section.css">

    <!-- Custom stylesheet for this page -->
    <link rel="stylesheet" href="css/index.css">

    <!-- Javascript -->
    <script src="js/util.js"></script>
    <script src="js/myutil.js"></script>
    <script src="js/index.js" defer></script>
</head>
<body>
    <nav id="navbar">
        <div class="left">
            <span><strong>Home</strong></span>
        </div>
        <div class="right">

        </div>
    </nav>

    <!-- Main content below navbar -->
    <section role="main">
        <div id="main-container" class="container">
            <!-- Infinite scrolling section -->
            <div id="posts"></div>
        </div>
    </section>
</body>
</html>