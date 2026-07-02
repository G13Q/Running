<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Account</title>
    <link rel="stylesheet" href="../assets/css/main.css?v=2" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
</head>
<body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="account-page">
        <h1>My Account</h1>

        <?php if (!isset($_SESSION["user_id"])): ?>
            <div class="auth-form" style="text-align:center; margin-top:20px;">
                <p style="margin:0 0 16px;">Please login to view your account.</p>
                <a href="?route=login" class="account-logout">LOGIN</a>
            </div>
        <?php else: ?>
            <?php if (isset($_GET["order_placed"])): ?>
            <div class="order-success">
                <div class="order-success__icon">&#10003;</div>
                <h2>Order Placed Successfully!</h2>
                <p>Thank you for your purchase. Your order is being processed.</p>
            </div>
            <?php endif; ?>

            <section class="account-page__info">
                <h2>Profile</h2>
                <p><strong>Name:</strong> <?= e($user["first_name"] ?? "") ?> <?= e($user["last_name"] ?? "") ?></p>
                <p><strong>Email:</strong> <?= e($user["email"] ?? "") ?></p>
                <p><strong>Role:</strong> <?= e($user["role"] ?? "") ?></p>
            </section>

            <section class="account-page__orders">
                <h2>Order History</h2>
                <?php if (empty($orders)): ?>
                    <p>No orders yet.</p>
                <?php else: ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order["id"] ?></td>
                                    <td><?= e($order["created_at"]) ?></td>
                                    <td>$<?= number_format((float)$order["subtotal"], 2) ?></td>
                                    <td><?= e($order["shipping_status"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <a href="?route=logout" class="account-logout">LOGOUT</a>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
</body>
</html>
