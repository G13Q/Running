<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../assets/css/main.css?v=2" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <style>
      .cart-page {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px 80px;
      }
      .cart-page h1 {
        font-size: 1.6rem;
        margin-bottom: 30px;
        font-weight: 700;
      }
      .cart-page__empty {
        text-align: center;
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 20px;
      }
      .cart-table {
        width: 100%;
        border-collapse: collapse;
      }
      .cart-table th {
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        padding-bottom: 12px;
        border-bottom: 1px solid #ddd;
      }
      .cart-table td {
        padding: 16px 0;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
      }
      .cart-item-cell {
        display: flex;
        align-items: center;
        gap: 14px;
      }
      .cart-item-cell img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 6px;
        background: #f5f3ef;
      }
      .cart-item-cell .cart-item-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
      }
      .cart-item-cell .cart-item-name {
        font-weight: 600;
        font-size: 0.9rem;
      }
      .cart-item-cell .cart-item-size {
        font-size: 0.78rem;
        color: #888;
      }
      .cart-table .cart-qty-form {
        display: inline-flex;
        align-items: center;
        gap: 6px;
      }
      .cart-table .cart-qty-form input[type="number"] {
        width: 55px;
        padding: 6px 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.85rem;
        text-align: center;
      }
      .cart-table .cart-qty-form button {
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 600;
      }
      .cart-table .cart-qty-form button:hover {
        background: #f5f5f5;
      }
      .cart-remove-btn {
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 0.78rem;
        text-decoration: underline;
      }
      .cart-remove-btn:hover {
        color: #333;
      }
      .cart-table tfoot td {
        padding-top: 16px;
        font-size: 1rem;
      }
      .cart-page__actions {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
      }
      .cart-page__checkout {
        display: block;
        width: 100%;
        max-width: 400px;
        padding: 14px 24px;
        background: #000;
        color: #fff;
        text-align: center;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: background 0.2s;
      }
      .cart-page__checkout:hover {
        background: rgba(0,0,0,0.8);
      }
      .cart-page__continue {
        font-size: 0.8rem;
        color: #666;
        text-decoration: underline;
      }
      .cart-page__continue:hover {
        color: #333;
      }
    </style>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="cart-page">
      <h1>Shopping Cart</h1>

      <?php if (empty($cartItems)): ?>
        <p class="cart-page__empty">Your cart is empty.</p>
        <a href="?route=shop-all" class="cart-page__continue">Continue Shopping</a>
      <?php else: ?>
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $grandTotal = 0; ?>
            <?php foreach ($cartItems as $item): ?>
              <?php $lineTotal = $item["price"] * $item["quantity"]; ?>
              <?php $grandTotal += $lineTotal; ?>
              <tr>
                <td>
                  <div class="cart-item-cell">
                    <?php if (!empty($item["image"])): ?>
                      <img src="<?= e($item["image"]) ?>" alt="<?= e($item["name"]) ?>" />
                    <?php else: ?>
                      <img src="../assets/images/c1.jpg" alt="" />
                    <?php endif; ?>
                    <div class="cart-item-details">
                      <span class="cart-item-name"><?= e($item["name"]) ?></span>
                      <?php if (!empty($item["size"])): ?>
                        <span class="cart-item-size">Size: <?= e($item["size"]) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>$<?= number_format((float)$item["price"], 2) ?></td>
                <td>
                  <form method="POST" action="?route=cart" class="cart-qty-form">
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="product_id" value="<?= $item["product_id"] ?>" />
                    <input type="number" name="quantity" value="<?= $item["quantity"] ?>" min="1" />
                    <button type="submit">Update</button>
                  </form>
                </td>
                <td>$<?= number_format((float)$lineTotal, 2) ?></td>
                <td>
                  <form method="POST" action="?route=cart" style="display:inline;">
                    <input type="hidden" name="action" value="remove" />
                    <input type="hidden" name="product_id" value="<?= $item["product_id"] ?>" />
                    <button type="submit" class="cart-remove-btn">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3"><strong>Subtotal</strong></td>
              <td><strong>$<?= number_format((float)$grandTotal, 2) ?></strong></td>
              <td></td>
            </tr>
          </tfoot>
        </table>

        <div class="cart-page__actions">
          <a href="?route=checkout" class="cart-page__checkout">PROCEED TO CHECKOUT</a>
          <a href="?route=shop-all" class="cart-page__continue">Continue Shopping</a>
        </div>
      <?php endif; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>
