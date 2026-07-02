<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Dashboard</title>
    <style>
        .delivery-page {
            padding-top: 120px;
            min-height: 100vh;
            background: #f0eee9;
            color: #212121;
            max-width: 1200px;
            margin: 0 auto;
            padding-inline: 20px;
        }

        .delivery-page h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .delivery-page__subtitle {
            color: rgba(33, 33, 33, 0.55);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
        }

        .cart-table th {
            text-align: left;
            padding: 16px 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(33, 33, 33, 0.5);
            border-bottom: 1px solid rgba(33, 33, 33, 0.08);
        }

        .cart-table td {
            padding: 16px 20px;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(33, 33, 33, 0.06);
        }

        .cart-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cart-table tbody tr:hover {
            background: rgba(240, 238, 233, 0.4);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-badge--shipped {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-deliver {
            padding: 8px 20px;
            border: none;
            border-radius: 999px;
            background: #065f46;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-deliver:hover {
            background: #047857;
        }

        .delivery-empty {
            text-align: center;
            padding: 80px 20px;
            color: rgba(33, 33, 33, 0.45);
        }

        .delivery-empty__title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .delivery-empty__desc {
            font-size: 0.9rem;
        }

        @media (max-width: 720px) {
            .delivery-page {
                padding-top: 105px;
                padding-inline: 12px;
            }

            .cart-table th,
            .cart-table td {
                padding: 12px 14px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
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
                            <td>$<?= number_format($order["subtotal"]) ?></td>
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
</body>

</html>