<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($product["name"] ?? "Product") ?> | Allbirds Clone</title>
    <link rel="stylesheet" href="../assets/css/main.css?v=2" />
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
        <nav class="pdp-breadcrumb">
          <a href="?route=home">Home</a>
          <span>/</span>
          <?php $gender = $product["gender"] ?? "unisex"; ?>
          <?php if ($gender === "men"): ?>
            <a href="?route=mens">Men</a>
          <?php elseif ($gender === "women"): ?>
            <a href="?route=womens">Women</a>
          <?php else: ?>
            <a href="?route=shop-all">Shop All</a>
          <?php endif; ?>
          <span>/</span>
          <span class="pdp-breadcrumb__current"><?= e($product["name"]) ?></span>
        </nav>

        <div class="pdp-layout">
          <div class="pdp-gallery" data-gallery>
            <?php if ($badge): ?>
            <span class="card__badge card__badge--<?= str_replace(' ', '_', strtolower($badge)) ?>"><?= $badge ?></span>
            <?php endif; ?>
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

          <div class="pdp-info" data-product-id="<?= (int)($product["id"] ?? 0) ?>"
               data-color-images='<?= e(json_encode($colorImagesMap)) ?>'>
            <h1 class="pdp-title"><?= e($product["name"]) ?></h1>
            <div class="pdp-price">$<?= $displayPrice ?></div>

            <div class="pdp-color-section">
              <p class="pdp-color-label">
                COLOR
                <span class="pdp-color-name">
                  <?php
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
                  $colorDisabled = empty($colorInStock[$c]);
                  ?>
                  <?php $colorImgList = $colorImagesMap[$c] ?? $galleryImages; ?>
                  <button
                    class="pdp-swatch<?= $c === $uniqueColors[0] && !$colorDisabled ? " active" : "" ?><?= $colorDisabled ? " pdp-swatch--oos" : "" ?>"
                    style="background-color: <?= $hex ?>"
                    title="<?= $colorDisabled ? "Out of stock" : e($clean) ?>"
                    data-color="<?= e($c) ?>"
                    data-images='<?= e(json_encode($colorImgList)) ?>'
                    <?= $colorDisabled ? "disabled" : "" ?>
                    <?php if (strtolower($clean) === "white" || strtolower($clean) === "blizzard" || strtolower($clean) === "warm white"): ?>
                      data-white="true"
                    <?php endif; ?>
                  >
                    <span class="pdp-swatch__ring"></span>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="pdp-size-section">
              <div class="pdp-size-header">
                <span>EU SIZES</span>
              </div>
              <div class="pdp-size-grid" data-sizes>
                <?php foreach ($uniqueSizes as $s): ?>
                  <?php $sizeDisabled = empty($sizeInStock[$s]); ?>
                  <button class="pdp-size-btn<?= $sizeDisabled ? " pdp-size-btn--oos" : "" ?>" data-size="<?= e($s) ?>" <?= $sizeDisabled ? "disabled" : "" ?>><?= e($s) ?></button>
                <?php endforeach; ?>
              </div>
              <p class="pdp-size-guide">These fit true-to-size for most customers.
                <a href="?route=shop-all" class="pdp-fit-link">Shop All</a>
              </p>
            </div>

            <button class="pdp-atb" data-atb disabled>SELECT A SIZE</button>
            <p class="pdp-shipping">Free Shipping on Orders over $100</p>

          </div>
        </div>

        <section class="pdp-tech-section">
          <h2 class="pdp-tech-section__heading">Breathable By Nature</h2>
          <div class="pdp-tech">
            <svg class="pdp-tech__circles" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="400" cy="400" r="396" stroke="rgba(0,0,0,0.15)" stroke-width="0.8" />
              <circle cx="400" cy="400" r="350" stroke="rgba(0,0,0,0.15)" stroke-width="0.8" />
              <circle cx="400" cy="400" r="300" stroke="rgba(0,0,0,0.15)" stroke-width="1" />
              <circle cx="400" cy="400" r="250" stroke="rgba(0,0,0,0.15)" stroke-width="0.8" />
              <circle cx="400" cy="400" r="200" stroke="rgba(0,0,0,0.15)" stroke-width="0.8" />
              <circle cx="400" cy="400" r="150" stroke="rgba(0,0,0,0.15)" stroke-width="0.8" />
            </svg>
            <div class="pdp-tech__shoe">
              <img src="<?= $showLocalGallery ? $allbirdsLocal[0] : e(imageUrl($mainImage)) ?>" alt="Shoe" loading="lazy" />
            </div>
            <div class="pdp-tech__labels">
              <div class="pdp-tech__label" style="top: 60px; left: 60px;">
                <span class="pdp-tech__label-title">BREATHABLE</span>
                <p class="pdp-tech__label-desc">A lightweight natural fiber that keeps you cool and comfortable all day.</p>
              </div>
              <div class="pdp-tech__label" style="top: 60px; right: 60px;">
                <span class="pdp-tech__label-title">LIGHTWEIGHT</span>
                <p class="pdp-tech__label-desc">Engineered for all-day comfort without the extra weight on your feet.</p>
              </div>
              <div class="pdp-tech__label" style="bottom: 60px; left: 60px;">
                <span class="pdp-tech__label-title">RESPONSIBLY SOURCED</span>
                <p class="pdp-tech__label-desc">Made with renewable materials from farms committed to sustainability.</p>
              </div>
              <div class="pdp-tech__label" style="bottom: 60px; right: 60px;">
                <span class="pdp-tech__label-title">PLUSH FEATHERBED&trade;</span>
                <p class="pdp-tech__label-desc">Dual-density insole with memory foam that molds to your foot for lasting comfort.</p>
              </div>
            </div>
          </div>
        </section>

        <section class="pdp-related-new">
          <h3 class="pdp-related-new__heading">You May Also Like</h3>
          <div class="collection-grid">
            <?php foreach ($relatedCardProducts as $item): ?>
              <a href="<?= $item["url"] ?>" class="card">
                <?php require __DIR__ . "/components/product-card-swatch.php"; ?>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="pdp-testimonials">
          <h2 class="pdp-testimonials__heading">What Our Customers Say</h2>
          <div class="pdp-testimonials__grid">
            <?php if (empty($reviews)): ?>
              <p class="pdp-testimonials__empty">No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
              <?php foreach (array_slice($reviews, 0, 6) as $review): ?>
                <div class="pdp-testimonial-card">
                  <div class="pdp-testimonial-card__stars">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                      <span class="pdp-testimonial-card__star <?= $i < $review["rating"] ? "filled" : "" ?>">★</span>
                    <?php endfor; ?>
                  </div>
                  <p class="pdp-testimonial-card__text"><?= e($review["comment"]) ?></p>
                  <div class="pdp-testimonial-card__author">
                    <span>— <?= e($review["first_name"] ?? "Verified Customer") ?></span>
                    <?php if ($review["verified_purchase"]): ?>
                      <span class="pdp-testimonial-card__verified">Verified Purchase</span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>

        <section class="pdp-sustainability">
          <div class="pdp-sus__banner">
            <div class="pdp-sus__image">
              <img src="../assets/images/c1.jpg" alt="Sustainability" loading="lazy" />
            </div>
            <div class="pdp-sus__overlay"></div>
            <svg class="pdp-sus__contours" viewBox="0 0 800 450" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="60" y="30" width="680" height="390" rx="40" stroke="rgba(255,255,255,0.4)" stroke-width="1" />
              <rect x="90" y="55" width="620" height="340" rx="34" stroke="rgba(255,255,255,0.3)" stroke-width="1" />
              <rect x="120" y="80" width="560" height="290" rx="28" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
              <rect x="150" y="105" width="500" height="240" rx="22" stroke="rgba(255,255,255,0.15)" stroke-width="1" />
            </svg>
            <div class="pdp-sus__content">
              <h2>Better Things in a Better Way</h2>
              <p>Looking to the world's greatest innovator — Nature</p>
              <a href="?route=shop-all" class="pdp-sus__cta">SHOP NOW</a>
            </div>
            <div class="pdp-sus__labels">
              <span class="pdp-sus__pill" style="top: 12%; left: 11%;">Responsible Energy</span>
              <span class="pdp-sus__pill" style="top: 8%; right: 7%;">Renewable Materials</span>
              <span class="pdp-sus__pill" style="bottom: 30%; left: 15%;">Regenerative Agriculture</span>
            </div>
          </div>
        </section>

      </div>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
    <script>
      $(".pdp-related-new .collection-grid").on("click", ".hue", function (e) {
        e.preventDefault();
        const thumb = $(this).data("thumb");
        $(this).closest(".card").find("img").attr("src", thumb);
        $(this).closest(".swatches").find(".hue").removeClass("hue--active");
        $(this).addClass("hue--active");
      });
    </script>
  </body>
</html>
