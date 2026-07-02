<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($product["name"] ?? "Product") ?> | Allbirds Clone</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <script type="module" src="../assets/js/shared/productDetail.js" defer></script>
  </head>
  <body>
    <?php
    require_once __DIR__ . "/components/navbar.php";

    $galleryImages = [];
    foreach ($images as $img) {
      $src = $img["thumbnail"] ?? $img["top_view"] ?? $img["side_view"] ?? $img["pair_view"] ?? "";
      if ($src && !in_array($src, $galleryImages)) {
        $galleryImages[] = $src;
      }
    }
    if (empty($galleryImages)) {
      $seen = [];
      foreach ($variants as $v) {
        if (!empty($v["thumbnail"]) && !in_array($v["thumbnail"], $seen)) {
          $galleryImages[] = $v["thumbnail"];
          $seen[] = $v["thumbnail"];
        }
      }
    }

    $uniqueColors = [];
    $seenColors = [];
    foreach ($variants as $v) {
      $color = trim($v["color"]);
      if ($color && !in_array($color, $seenColors)) {
        $seenColors[] = $color;
        $uniqueColors[] = $color;
      }
    }

    $uniqueSizes = [];
    $seenSizes = [];
    foreach ($variants as $v) {
      $size = trim((string)$v["size"]);
      if ($size !== "" && !in_array($size, $seenSizes)) {
        $seenSizes[] = $size;
        $uniqueSizes[] = $size;
      }
    }

    $mainImage = $galleryImages[0] ?? "";
    $showLocalGallery = empty($galleryImages);
    $allbirdsLocal = [
      "../assets/images/allbirds-pdp-left.png",
      "../assets/images/allbirds-pdp-back.png",
      "../assets/images/allbirds-pdp-td.png",
      "../assets/images/allbirds-pdp-sole.png",
      "../assets/images/allbirds-pdp-pair.png",
    ];
    $colorName = $uniqueColors[0] ?? "Deep Navy";
    $price = $salePrice ?: ($product["base_price"] ?? 7500);
    $displayPrice = number_format((float)$price, 2);
    $origPrice = $product["base_price"] ?? 0;
    ?>

    <main class="pdp-page">
      <div class="pdp-container">
        <!-- Breadcrumbs -->
        <nav class="pdp-breadcrumb">
          <a href="?route=home">Home</a>
          <span>/</span>
          <a href="?route=womens">Womens</a>
          <span>/</span>
          <span class="pdp-breadcrumb__current"><?= e($product["name"]) ?></span>
        </nav>

        <div class="pdp-layout">
          <!-- LEFT: Gallery -->
          <div class="pdp-gallery" data-gallery>
            <span class="pdp-badge">NEW</span>
            <div class="pdp-gallery__main">
              <img
                id="pdpMainImage"
                src="<?= $showLocalGallery ? $allbirdsLocal[0] : e(imageUrl($mainImage)) ?>"
                alt="<?= e($product["name"]) ?>"
              />
            </div>
            <div class="pdp-gallery__thumbs" data-thumbs>
              <?php if ($showLocalGallery): ?>
                <?php foreach ($allbirdsLocal as $i => $local): ?>
                  <button class="pdp-thumb<?= $i === 0 ? " active" : "" ?>" data-index="<?= $i ?>">
                    <img src="<?= $local ?>" alt="" loading="lazy" />
                  </button>
                <?php endforeach; ?>
              <?php else: ?>
                <?php foreach ($galleryImages as $i => $src): ?>
                  <button class="pdp-thumb<?= $i === 0 ? " active" : "" ?>" data-index="<?= $i ?>">
                    <img src="<?= e(imageUrl($src)) ?>" alt="" loading="lazy" />
                  </button>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- RIGHT: Info -->
          <div class="pdp-info" data-product-id="<?= (int)($product["id"] ?? 0) ?>">
            <h1 class="pdp-title"><?= e($product["name"]) ?></h1>
            <div class="pdp-price">$<?= $displayPrice ?></div>

            <!-- Color -->
            <div class="pdp-color-section">
              <p class="pdp-color-label">
                COLOR
                <span class="pdp-color-name">
                  <?php
                  // Use the first unique color, strip parenthetical suffixes
                  $cleanColor = stripColorSole($colorName);
                  echo e($cleanColor);
                  ?>
                </span>
              </p>
              <div class="pdp-swatches" data-swatches>
                <?php foreach ($uniqueColors as $c): ?>
                  <?php
                  $hex = filterColorHex($c);
                  $clean = stripColorSole($c);
                  ?>
                  <button
                    class="pdp-swatch<?= $c === $uniqueColors[0] ? " active" : "" ?>"
                    style="background-color: <?= $hex ?>"
                    title="<?= e($clean) ?>"
                    data-color="<?= e($c) ?>"
                    <?php if (strtolower($clean) === "white" || strtolower($clean) === "blizzard" || strtolower($clean) === "warm white"): ?>
                      data-white="true"
                    <?php endif; ?>
                  >
                    <span class="pdp-swatch__ring"></span>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Sizes -->
            <div class="pdp-size-section">
              <div class="pdp-size-header">
                <span>WOMEN'S SIZES</span>
                <a href="#" class="pdp-size-convert">MEN'S SIZES</a>
              </div>
              <div class="pdp-size-grid" data-sizes>
                <?php foreach ($uniqueSizes as $s): ?>
                  <button class="pdp-size-btn" data-size="<?= e($s) ?>"><?= e($s) ?></button>
                <?php endforeach; ?>
              </div>
              <p class="pdp-size-guide">The Cruiser Slip On Canvas fits true-to-size for most customers.
                <a href="#" class="pdp-fit-link">Fit Guide</a>
              </p>
            </div>

            <!-- Add to Bag -->
            <button class="pdp-atb" data-atb disabled>SELECT A SIZE</button>
            <p class="pdp-shipping">Free Shipping on Orders over $100</p>

            <!-- Accordion / Details -->
            <div class="pdp-accordion">
              <details class="pdp-details" open>
                <summary>THE DETAILS</summary>
                <div class="pdp-details__content">
                  <p><?= e($product["description"] ?? "Slip in and go. A breathable blend of premium materials makes it light, comfortable, and ready for warm-weather days on repeat.") ?></p>
                </div>
              </details>

              <details class="pdp-details">
                <summary>MATERIALLY BETTER</summary>
                <div class="pdp-details__content">
                  <p>An ultra-soft blend of premium materials keeps things light and breathable. Inside, our dual-density insole adds plush memory foam cushioning—so comfort stays with you from morning to whenever.</p>
                </div>
              </details>

              <details class="pdp-details">
                <summary>WASH &amp; CARE</summary>
                <div class="pdp-details__content">
                  <p>Yes, they're machine washable. Remove the insoles, hand wash those separately, and let everything air dry. Fresh again.</p>
                </div>
              </details>
            </div>
          </div>
        </div>

        <!-- Feature Cards -->
        <section class="pdp-features">
          <?php $features = [
            [
              "title" => "Breathable By Nature",
              "text" => "Canvas upper made from a breathable blend of premium materials—light, airy, and easy to wear all day.",
              "icon" => "wind",
            ],
            [
              "title" => "Lightweight Comfort",
              "text" => "Light, airy, and easy to wear all day with plush cushioning that doesn't quit.",
              "icon" => "feather",
            ],
            [
              "title" => "Responsibly Sourced",
              "text" => "Materials sourced to meet high standards of animal welfare, environmental care, and social responsibility.",
              "icon" => "leaf",
            ],
            [
              "title" => "Plush Featherbed™",
              "text" => "Dual-density memory foam insole adds extra softness and comfort that doesn't quit.",
              "icon" => "cloud",
            ],
            [
              "title" => "Built to Bounce Back",
              "text" => "SweetFoam® cushioning delivers comfort and energy return with every step.",
              "icon" => "zap",
            ],
          ]; ?>
          <?php foreach ($features as $i => $f): ?>
            <div class="pdp-feature-card">
              <div class="pdp-feature-icon">
                <?php if ($f["icon"] === "wind"): ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1014 16H2m15.73-8.27A2.5 2.5 0 1119.5 12H2"/></svg>
                <?php elseif ($f["icon"] === "feather"): ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.24 12.24a6 6 0 00-8.49-8.49L5 10.5V19h8.5l6.74-6.76z"/><path d="M16 8L2 22"/><path d="M17.5 15H9"/></svg>
                <?php elseif ($f["icon"] === "leaf"): ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 20A7 7 0 019.8 6.9C15.5 4.9 17 3.5 19 2c1 2 2 4.5 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                <?php elseif ($f["icon"] === "cloud"): ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17.5 19H9a7 7 0 116.65-9.6A5.5 5.5 0 1117.5 19z"/></svg>
                <?php elseif ($f["icon"] === "zap"): ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                <?php endif; ?>
              </div>
              <h3 class="pdp-feature-title"><?= $f["title"] ?></h3>
              <p class="pdp-feature-text"><?= $f["text"] ?></p>
            </div>
          <?php endforeach; ?>
        </section>

        <!-- Editorial Image -->
        <section class="pdp-editorial">
          <img src="../assets/images/heroBg.webp" alt="Lifestyle" loading="lazy" />
        </section>

        <!-- You May Also Like -->
        <section class="pdp-related">
          <h2 class="pdp-section-title">You May Also Like</h2>
          <div class="pdp-related-grid">
            <?php
            $relatedProducts = [];
            if (class_exists('Product') && isset($pdo)) {
              try {
                $rp = (new Product($pdo))->getTopSellers(4);
                foreach ($rp as $r) {
                  if ((int)($r["id"] ?? 0) !== (int)($product["id"] ?? 0)) {
                    $relatedProducts[] = $r;
                  }
                }
              } catch (\Throwable $e) {}
            }
            // Fallback cards if no related
            $fallback = [
              ["name" => "Women's Runner Luxe",  "price" => "100.00", "img" => "../assets/images/c1.jpg"],
              ["name" => "Women's Trail Runner",  "price" => "120.00", "img" => "../assets/images/c2.jpg"],
              ["name" => "Women's Cloud sneaker", "price" => "95.00",  "img" => "../assets/images/c3.jpg"],
              ["name" => "Women's Pace Knit",     "price" => "110.00", "img" => "../assets/images/grid4.jpg"],
            ];
            $displayRelated = !empty($relatedProducts) ? $relatedProducts : $fallback;
            foreach ($displayRelated as $i => $rp):
              $rpName = $rp["name"] ?? "Product";
              $rpPrice = $rp["base_price"] ?? $rp["price"] ?? "0";
              $rpPrice = number_format((float)$rpPrice, 2);
              $rpImg = $rp["img"] ?? $fallback[$i % 4]["img"] ?? "../assets/images/c1.jpg";
            ?>
              <a href="?route=product&id=<?= $rp["id"] ?? $i + 1 ?>" class="pdp-related-card">
                <div class="pdp-related-img">
                  <img src="<?= e($rpImg) ?>" alt="<?= e($rpName) ?>" loading="lazy" />
                </div>
                <div class="pdp-related-info">
                  <p class="pdp-related-name"><?= e($rpName) ?></p>
                  <p class="pdp-related-price">$<?= $rpPrice ?></p>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- Reviews -->
        <section class="pdp-reviews">
          <h2 class="pdp-section-title">Reviews</h2>
          <div class="pdp-reviews-summary">
            <div class="pdp-reviews-stars" data-stars="4.5">
              <span class="star" data-score="1">★</span>
              <span class="star" data-score="2">★</span>
              <span class="star" data-score="3">★</span>
              <span class="star" data-score="4">★</span>
              <span class="star" data-score="5">★</span>
            </div>
            <span class="pdp-reviews-count">4.5 out of 5 (128 reviews)</span>
          </div>
          <div class="pdp-reviews-list">
            <div class="pdp-review">
              <div class="pdp-review-stars">★★★★★</div>
              <p class="pdp-review-title">So comfortable!</p>
              <p class="pdp-review-text">These are the most comfortable slip-ons I've ever worn. Lightweight, breathable, and perfect for summer.</p>
              <p class="pdp-review-author">– Sarah M.</p>
            </div>
            <div class="pdp-review">
              <div class="pdp-review-stars">★★★★☆</div>
              <p class="pdp-review-title">Great fit, nice style</p>
              <p class="pdp-review-text">Really like the look and feel. Went true to size and they fit perfectly. Would recommend.</p>
              <p class="pdp-review-author">– Jessica K.</p>
            </div>
            <div class="pdp-review">
              <div class="pdp-review-stars">★★★★★</div>
              <p class="pdp-review-title">Perfect everyday shoe</p>
              <p class="pdp-review-text">I wear them everywhere. Great for walking, running errands, or just lounging around.</p>
              <p class="pdp-review-author">– Amanda R.</p>
            </div>
          </div>
        </section>

        <!-- Better Things -->
        <section class="pdp-better">
          <div class="pdp-better-content">
            <h2>Better Things in a Better Way</h2>
            <p>Looking to the world's greatest innovator — Nature</p>
            <a href="?route=home" class="pdp-better-cta">LEARN MORE</a>
            <div class="pdp-better-grid">
              <div class="pdp-better-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                <span>Responsible Energy</span>
              </div>
              <div class="pdp-better-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                <span>Renewable Materials</span>
              </div>
              <div class="pdp-better-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                <span>Regenerative Agriculture</span>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>
