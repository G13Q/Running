<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Dashboard</title>
    <link rel="stylesheet" href="../assets/css/main.css?v=2" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <style>
        .delivery-page {
            max-width: 1000px;
            margin: 0 auto;
            padding: 60px 20px 100px;
        }
        .delivery-page h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .delivery-page__subtitle {
            color: rgba(33,33,33,0.5);
            font-size: 0.88rem;
            margin-bottom: 32px;
        }
        .delivery-page .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--nav-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        }
        .delivery-page .cart-table th {
            text-align: left;
            padding: 14px 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(33,33,33,0.45);
            border-bottom: 1px solid rgba(33,33,33,0.08);
            background: #fafaf8;
        }
        .delivery-page .cart-table td {
            padding: 14px 20px;
            font-size: 0.88rem;
            border-bottom: 1px solid rgba(33,33,33,0.06);
        }
        .delivery-page .cart-table tbody tr:last-child td {
            border-bottom: none;
        }
        .delivery-page .cart-table tbody tr:hover {
            background: rgba(240,238,233,0.4);
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-badge--shipped {
            background: #dbeafe;
            color: #1e40af;
        }
        .btn-deliver {
            padding: 8px 20px;
            border: none;
            border-radius: 25px;
            background: #000;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            font-family: var(--main-font);
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-deliver:hover {
            background: rgba(0,0,0,0.8);
        }
        .delivery-empty {
            text-align: center;
            padding: 80px 20px;
            color: rgba(33,33,33,0.4);
        }
        .delivery-empty__title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #222;
        }
        .delivery-empty__desc {
            font-size: 0.9rem;
        }
        @media (max-width: 720px) {
            .delivery-page {
                padding: 40px 16px 80px;
            }
            .delivery-page .cart-table th,
            .delivery-page .cart-table td {
                padding: 12px 14px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="delivery-page">
        <h1>Delivery Dashboard</h1>
        <?php if (!empty($cityName)): ?>
            <p class="delivery-page__subtitle">Delivering in <?= e($cityName) ?></p>
        <?php endif; ?>

        <?php if (empty($assignedOrders)): ?>
            <div class="delivery-empty">
                <p class="delivery-empty__title">All clear!</p>
                <p class="delivery-empty__desc">No orders ready for delivery in your city.</p>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Client</th>
                        <th>City</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignedOrders as $order): ?>
                        <tr>
                            <td>#<?= $order["id"] ?></td>
                            <td><?= e($order["client_name"] ?? "") ?></td>
                            <td><?= e($order["city_name"] ?? "") ?></td>
                            <td>$<?= number_format((float)$order["subtotal"], 2) ?></td>
                            <td><span class="status-badge status-badge--shipped">Shipped</span></td>
                            <td>
                                <form method="POST" action="?route=delivery">
                                    <input type="hidden" name="action" value="mark_delivered" />
                                    <input type="hidden" name="order_id" value="<?= $order["id"] ?>" />
                                    <button type="submit" class="btn-deliver">Mark Delivered</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
</body>
</html>
