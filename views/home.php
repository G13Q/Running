<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Running Shoes</title>

    <link rel="stylesheet" href="./assets/css/main.css?v=2" />

    <link rel="stylesheet" href="<link rel=" preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet" />

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=help,person" />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap"
        <link href="https://fonts.googleapis.com/css2?family=Edu+NSW+ACT+Hand:wght@400..700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script type="module" src="assets/js/index.js" defer></script>
    <script
        src="https://code.jquery.com/jquery-4.0.0.js"
        integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U="
        crossorigin="anonymous"></script>
</head>

<body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <section class="hero-container">
        <div id="hero-section">
            <div class="hero-txt">
                <h2>The New Collection</h2>
                <div class="hero-btns">
                    <a href="?route=mens"><button>SHOP MEN</button></a>
                    <a href="?route=womens"><button>SHOP WOMEN</button></a>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section id="category">
            <div>
                <span>
                    <h2>NEW ARRIVALS</h2>
                    <a href="?route=new-arrivals"><button>SHOP NEW ARRIVALS</button></a>
                </span>
            </div>
            <div>
                <span>
                    <h2>MENS</h2>
                    <a href="?route=mens"><button>SHOP MEN</button></a>
                </span>
            </div>
            <div>
                <span>
                    <h2>WOMENS</h2>
                    <a href="?route=womens"><button>SHOP WOMEN</button></a>
                </span>
            </div>
            <div>
                <span>
                    <h2>BEST SELLERS</h2>
                    <a href="?route=shop-all"><button>SHOP ALL</button></a>
                </span>
            </div>
        </section>

        <section id="new-arrivals">
            <h2 class="title">NEW ARRIVALS</h2>
            <div class="arrow">
                ->
            </div>
            <span class="leftControl"></span>
            <span class="rightControl">
            </span>
            <div class="newAriv1content">
                <?php foreach (array_slice($items, 0, 20) as $item): ?>
                    <div data-name="<?= htmlspecialchars($item["name"]) ?>" data-price="<?= $item["price"] ?>"><img src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>"></div>
                <?php endforeach; ?>
            </div>
            <div class="info">
                <h2 class="collName">June's Collection</h2>
                <p class="pName"><?= $items[0]["name"] ?? "Product" ?> - $<?= number_format($items[0]["price"] ?? 0) ?></p>
                <div>
                    <a href="?route=new-arrivals"><button>SHOP NEW ARRIVALS</button></a>
                </div>
            </div>
        </section>

        <section class="specialCollectionSection">
            <div class="main">
                <h1>Bold By Nature</h1>
                <p>Show your true colors .</p>
                <a href="?route=shop-all"><button>SHOP NOW</button></a>
            </div>
            <div></div>
            <div></div>
            <div></div>
        </section>

        <section class="newArrivals2">
            <div class="header">
                <h2 class="title">NEW ARRIVALS </h2>
                <div class="arrows">
                    <div class="left">⇠</div>
                    <div class="right">⇢</div>
                </div>
            </div>
            <div class="content">
                <?php foreach (array_slice($items, 20) as $item): ?>
                    <a href="<?= $item["url"] ?>" class="card">
                        <?php require __DIR__ . "/components/product-card-swatch.php"; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="collection-categories" aria-label="Shop by category">
            <article class="collection-category">
                <img src="assets/images/c3.jpg" alt="Road Running" loading="lazy" />
                <div class="collection-category__content">
                    <h2>Road Running</h2>
                    <a href="?route=mens">SHOP MEN</a>
                </div>
            </article>
            <article class="collection-category">
                <img src="assets/images/c1.jpg" alt="Trail Running" loading="lazy" />
                <div class="collection-category__content">
                    <h2>Trail Running</h2>
                    <a href="?route=shop-all">EXPLORE</a>
                </div>
            </article>
            <article class="collection-category">
                <img src="assets/images/c2.jpg" alt="Marathon" loading="lazy" />
                <div class="collection-category__content">
                    <h2>Marathon</h2>
                    <a href="?route=womens">SHOP WOMEN</a>
                </div>
            </article>
        </section>

        <?php require_once __DIR__ . "/components/trust-cards.php"; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
    <script>
      $(".newArrivals2 .content").on("click", ".hue", function (e) {
        e.preventDefault();
        const thumb = $(this).data("thumb");
        $(this).closest(".card").find("img").attr("src", thumb);
        $(this).closest(".swatches").find(".hue").removeClass("hue--active");
        $(this).addClass("hue--active");
      });
    </script>
</body>
</html>