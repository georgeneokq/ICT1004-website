<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification success!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-size: 18px;
        }
        
        p {
            text-align: center;
        }

        section[role=main] {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <section role="main">
        <p>
            Thank you for verifying your email, <?php echo $user->first_name ?>!
            You will be redirected to the main page in <span id="counter"></span> seconds.
        </p>
        <br>
        <p><a href="/">Click here to go to main page immediately</a></p>
    </section>

    <script>
        let counterTime = 5; // seconds
        let counterEl = document.getElementById('counter');
        counterEl.innerText = counterTime;
        window.setInterval(() => {
            counterEl.innerText = --counterTime;
            if (counterTime == 0)
                window.location.href = '/';
        }, 1000);
    </script>
</body>

</html>