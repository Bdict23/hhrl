<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Thank You!</h4>
                    <p>Your order has been successfully placed. Your order number is:
                        <strong>#{{ $order->id }}</strong>
                    </p>
                    <p>We appreciate your business and look forward to serving you again.</p>
                    <hr>
                    <p class="mb-0">You will be redirected to the home page in <span id="countdown">3</span> seconds.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        let countdownNumber = 3;
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            countdownNumber--;
            countdownElement.textContent = countdownNumber;
            if (countdownNumber <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "/order_menu";
            }
        }, 1000);
    </script>
</body>

</html>
