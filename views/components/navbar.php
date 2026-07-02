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

             <!-- <svg viewBox="0 0 512 512" height="40" xmlns="http://www.w3.org/2000/svg">
                 <path class="hummingbird" fill="#c4a4a4" d="M139.7 23.52c-9.1 30.54-16.5 61.64-12.7 91.58 4.2 32.7 21 64.9 65.7 95.7-53.6 74.8-86.1 204.4-59.3 277.7 10.9-54 14.2-97.8 53.5-144.6 77.5-25.6 123.9-37.6 140.3-125.7 6.2-14.7 12.6-19.3 31.9-24.7 10.6-2.9 22.2-7.5 22.1-19.2-.2-49.3-28.3-68.4-57.6-67.9-29.4.5-60 20.6-65.4 49.8-6 1.8-11.9 4.5-17.7 8-62.9-43.7-82.1-85.86-100.8-140.68zM32.03 107c10.8 27.2 26.44 54.6 49.2 76.1 24.27 22.9 56.47 39.3 100.87 42.2-34.5-24.2-54.8-50.3-65.2-77.2-29.4-10.9-56.47-25-84.87-41.1zm300.07 26.3a12.24 12.24 0 0 1 12.2 12.2 12.24 12.24 0 0 1-12.2 12.2 12.24 12.24 0 0 1-12.2-12.2 12.24 12.24 0 0 1 12.2-12.2zm60 56.1c-3.5 5.1-7.1 10.2-16.1 13.2 33.9 25.3 79.1 76.5 104 105-11.2-33.2-55.8-88.6-87.9-118.2z" />
             </svg> -->
             <svg viewBox="-4.8 -4.8 57.60 57.60" height="50" xmlns="http://www.w3.org/2000/svg">
                 <path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" d="M29.6922,33.92l-8.7887-9.46V40.5627a1.1064,1.1064,0,0,0,1.917.7531Z" />
                 <polygon fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" points="28.96 15.788 37.749 25.248 37.749 6.329 28.96 15.788" />
                 <path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" d="M20.9035,24.46,5.798,8.2015A1.1065,1.1065,0,0,1,6.6086,6.342H19.22a2.2126,2.2126,0,0,1,1.6211.7067L37.7491,25.2478,29.6922,33.92" />
                 <path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" d="M42.3369,10.9165,37.7491,6.3287v5.5322h4.1966A.5532.5532,0,0,0,42.3369,10.9165Z" />
             </svg>
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
 <div class="cartContainer">

 </div>
 <div class="cart">
     <div class="header">
         <h2>CART (0)</h2>
         <p class="info">Spend $100 more to earn free shipping!</p>

         <p class="closeCart">
             <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from Mono Icons by Mono - https://github.com/mono-company/mono-icons/blob/master/LICENSE.md -->
                 <path fill="currentColor" d="M5.293 5.293a1 1 0 0 1 1.414 0L12 10.586l5.293-5.293a1 1 0 1 1 1.414 1.414L13.414 12l5.293 5.293a1 1 0 0 1-1.414 1.414L12 13.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L10.586 12L5.293 6.707a1 1 0 0 1 0-1.414" />
             </svg>
         </p>

     </div>
     <div class="progress">

     </div>

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
 </div>