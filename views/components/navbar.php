  <header>
      <div class="menu">
          <div class="menuContent">
              <div class="tab-content" data-tab="men"><?php require __DIR__ . "/../tabs/men.php" ?></div>
              <div class="tab-content" data-tab="women"><?php require __DIR__ . "/../tabs/women.php" ?></div>
              <div class="tab-content" data-tab="sale"><?php require __DIR__ . "/../tabs/sale.php" ?></div>
          </div>
      </div>
     <div class="sale-announcement">
         <button class="ann-arrow ann-arrow--left" aria-label="Previous announcement">&#8249;</button>
         <div class="ann-track">
             <div class="ann-slide active">30% Off Your Order When You Spend $140. Discount Automatically Applied at Checkout.</div>
             <div class="ann-slide">Free Shipping on All Orders Over $100. Limited Time Offer!</div>
             <div class="ann-slide">New Summer Collection Just Dropped. Shop Now Before It's Gone!</div>
         </div>
         <button class="ann-arrow ann-arrow--right" aria-label="Next announcement">&#8250;</button>
     </div>

     <nav>
         <a href="?route=home">
             <h1 class="logo">Tor1</h1>
         </a>

         <?php $navItems = [
                ["label" => "NEW ARRIVALS", "route" => "new-arrivals"],
                ["label" => "SHOP ALL",    "route" => "shop-all"],
                ["label" => "MEN",         "route" => "mens",         "tab" => "men"],
                ["label" => "WOMEN",       "route" => "womens",       "tab" => "women"],
                ["label" => "SALE",        "route" => "sale",         "tab" => "sale"],
            ]; ?>
         <ul>
             <?php foreach ($navItems as $item): ?>
                 <li class="nav-item" <?= isset($item["tab"]) ? " data-tab=\"{$item["tab"]}\"" : "" ?>>
                     <a href="?route=<?= $item["route"] ?>"><?= $item["label"] ?></a>
                 </li>
             <?php endforeach; ?>
         </ul>

         <div class="nav-right">

             <a href="?route=search" aria-label="Search">
                 <svg
                     xmlns="http://www.w3.org/2000/svg"
                     height="24px"
                     viewBox="0 -960 960 960"
                     width="24px"
                     fill="#1f1f1f">
                     <path
                         d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                 </svg>
             </a>
             <a href="?route=login">
                 <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                     <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                     <g id="SVGRepo_iconCarrier">
                         <path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                     </g>
                 </svg>
             </a>
             <svg class="cartOpen"
                 xmlns="http://www.w3.org/2000/svg"
                 height="24px"
                 viewBox="0 -960 960 960"
                 width="24px"
                 fill="#1f1f1f">
                 <path
                     d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm0-80h480v-480h-80v80q0 17-11.5 28.5T600-520q-17 0-28.5-11.5T560-560v-80H400v80q0 17-11.5 28.5T360-520q-17 0-28.5-11.5T320-560v-80h-80v480Zm160-560h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720ZM240-160v-480 480Z" />
             </svg>

         </div>

         <!-- <div class="menu-bar">
          <ul>
            <li>[Running]</li>
            <li>MEN'S</li>
            <li>WOMAN'S</li>
            <li>NEW ARRIVALS</li>
            <li>BESTSELLERS</li>
          </ul>
        </div>-->
     </nav>
 </header>
 <!-- cart------ -->
 <?php
 $navCart = $_SESSION["cart"] ?? [];
 $navCartCount = 0;
 $navCartTotal = 0;
 foreach ($navCart as $pid => &$item) {
     if (empty($item["name"])) {
         require_once __DIR__ . "/../../config/database.php";
         require_once __DIR__ . "/../../models/product.php";
         require_once __DIR__ . "/../../models/product_variant.php";
         $p = (new Product($pdo))->findById((int) $pid);
         if ($p) {
             $vs = (new ProductVariant($pdo))->findByProduct((int) $pid);
             $fv = $vs[0] ?? [];
             $item["name"] = $p["name"];
             $item["price"] = $p["base_price"];
             $item["image"] = $fv["thumbnail"] ?? "";
         }
     }
     $navCartCount += $item["quantity"] ?? 1;
     $navCartTotal += ($item["price"] ?? 0) * ($item["quantity"] ?? 1);
 }
 unset($item);
 $_SESSION["cart"] = $navCart;
 $freeShippingThreshold = 100;
 $progressPercent = min(100, ($navCartTotal / $freeShippingThreshold) * 100);
 $remaining = max(0, $freeShippingThreshold - $navCartTotal);
 ?>
 <div class="cartContainer"></div>
 <div class="cart">
     <div class="header">
         <h2>CART (<?= $navCartCount ?>)</h2>
         <?php if ($remaining > 0): ?>
              <p class="info">Spend $<?= number_format($remaining, 2) ?> more for free shipping!</p>
         <?php else: ?>
             <p class="info">You've earned free shipping!</p>
         <?php endif; ?>
         <p class="closeCart">
             <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                 <path fill="currentColor" d="M5.293 5.293a1 1 0 0 1 1.414 0L12 10.586l5.293-5.293a1 1 0 1 1 1.414 1.414L13.414 12l5.293 5.293a1 1 0 0 1-1.414 1.414L12 13.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L10.586 12L5.293 6.707a1 1 0 0 1 0-1.414" />
             </svg>
         </p>
     </div>
     <div class="progress">
         <div class="progressFill" style="width: <?= $progressPercent ?>%"></div>
     </div>

     <?php if (empty($navCart)): ?>
     <div class="noItemsInCart">
         <h1>Your cart is empty. Start shopping!</h1>
         <div class="btnGroup">
             <a href="?route=womens"><button>SHOP WOMENS</button></a>
             <a href="?route=mens"><button>SHOP MENS</button></a>
             <a href="?route=shop-all"><button>SHOP ALL</button></a>
             <a href="?route=sale"><button>SHOP WOMENS SALE</button></a>
             <a href="?route=sale"><button>SHOP MENS SALE</button></a>
         </div>
     </div>
     <?php else: ?>
     <div class="cartItems">
         <?php foreach ($navCart as $pid => $item): ?>
         <div class="cartItem">
             <?php if (!empty($item["image"])): ?>
             <img class="cartItemImg" src="<?= e($item["image"]) ?>" alt="<?= e($item["name"]) ?>" />
             <?php else: ?>
             <div class="cartItemImg cartItemImg--placeholder"></div>
             <?php endif; ?>
             <div class="cartItemInfo">
                 <p class="cartItemName"><?= e($item["name"]) ?></p>
                 <?php if (!empty($item["size"])): ?>
                 <p class="cartItemMeta">Size: <?= e($item["size"]) ?></p>
                 <?php endif; ?>
                 <p class="cartItemMeta">Qty: <?= $item["quantity"] ?></p>
                 <p class="cartItemPrice">$<?= number_format((float)$item["price"], 2) ?></p>
             </div>
             <form method="POST" action="?route=cart" class="cartItemRemoveForm">
                 <input type="hidden" name="action" value="remove" />
                 <input type="hidden" name="product_id" value="<?= $pid ?>" />
                 <button type="submit" class="cartItemRemove" aria-label="Remove item">&times;</button>
             </form>
         </div>
         <?php endforeach; ?>
     </div>
     <div class="cartFooter">
         <div class="cartTotal">
             <span>Total</span>
             <span>$<?= number_format($navCartTotal, 2) ?></span>
         </div>
         <a href="?route=checkout" class="cartCheckout">CHECKOUT</a>
         <a href="?route=cart" class="cartViewAll">VIEW CART</a>
     </div>
     <?php endif; ?>
 </div>