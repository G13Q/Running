<?php

class ProductDetailController
{
    public function index(): void
    {
        $id = (int) ($_GET["id"] ?? 0);
        if ($id <= 0) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../utils/helpers.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";
        require_once __DIR__ . "/../models/product_img.php";
        require_once __DIR__ . "/../models/review.php";

        $product = (new Product($pdo))->findById($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        $variants = (new ProductVariant($pdo))->findByProduct($id);
        $images = (new ProductImg($pdo))->findByVariantProduct($id);

        $discount = computeVariantDiscount($variants, (float)$product["base_price"]);
        $salePrice = $discount["sale_price"] ?? null;

        $imageLookup = [];
        foreach ($images as $img) {
            $imageLookup[(int)$img["id"]] = $img;
        }
        $colorImagesMap = [];
        foreach ($variants as $v) {
            $color = trim($v["color"]);
            if ($color === '' || isset($colorImagesMap[$color])) continue;
            $imgId = (int)($v["product_img_id"] ?? 0);
            if ($imgId > 0 && isset($imageLookup[$imgId])) {
                $entry = $imageLookup[$imgId];
                $urls = array_values(array_filter([
                    imageUrl($entry["thumbnail"] ?? ''),
                    imageUrl($entry["top_view"] ?? ''),
                    imageUrl($entry["bottom_view"] ?? ''),
                    imageUrl($entry["side_view"] ?? ''),
                    imageUrl($entry["pair_view"] ?? ''),
                ]));
                if (!empty($urls)) {
                    $colorImagesMap[$color] = $urls;
                }
            }
        }

        $sizeInStock = [];
        $colorInStock = [];
        foreach ($variants as $v) {
            $sz = trim((string)$v["size"]);
            $cl = trim($v["color"]);
            $qty = (int)($v["stock_quantity"] ?? 0);
            if ($sz !== '' && $qty > 0) $sizeInStock[$sz] = true;
            if ($cl !== '' && $qty > 0) $colorInStock[$cl] = true;
        }

        $badge = null;
        $totalStock = 0;
        foreach ($variants as $v) {
            $totalStock += (int)($v["stock_quantity"] ?? 0);
        }
        $sales = (int)($product["sales"] ?? 0);
        if ($sales >= 400) {
            $badge = "BESTSELLER";
        } elseif ($totalStock <= 400) {
            $badge = "LAST FEW";
        } else {
            $createdAt = strtotime($product["created_at"]);
            if ($createdAt && (time() - $createdAt) < 30 * 24 * 60 * 60) {
                $badge = "NEW";
            }
        }

        $relatedProducts = (new Product($pdo))->findRandom($id, rand(1, 5));
        $relatedCardProducts = [];
        foreach ($relatedProducts as $rp) {
            $v = (new ProductVariant($pdo))->findByProduct($rp["id"]);
            $first = $v[0] ?? [];
            $thumb = imageUrl($first["thumbnail"] ?? "");

            $totalStock = 0;
            foreach ($v as $vv) {
                $totalStock += (int)($vv["stock_quantity"] ?? 0);
            }
            $sales = (int)($rp["sales"] ?? 0);
            $badge = null;
            if ($sales >= 400) {
                $badge = "BESTSELLER";
            } elseif ($totalStock <= 400) {
                $badge = "LAST FEW";
            } else {
                $createdAt = strtotime($rp["created_at"]);
                if ($createdAt && (time() - $createdAt) < 30 * 24 * 60 * 60) {
                    $badge = "NEW";
                }
            }

            $swatches = [];
            $seenCols = [];
            foreach ($v as $vv) {
                $c = stripColorSole($vv["color"] ?? "");
                if ($c === "" || isset($seenCols[$c])) continue;
                $seenCols[$c] = true;
                $swatches[] = [
                    "hex" => colorToHex($c),
                    "thumb" => imageUrl($vv["thumbnail"] ?? $thumb),
                ];
            }

            $relatedCardProducts[] = [
                "name" => $rp["name"],
                "color" => stripColorSole($first["color"] ?? ""),
                "price" => (float)$rp["base_price"],
                "image" => $thumb,
                "swatches" => $swatches,
                "badge" => $badge,
                "url" => "?route=product&id=" . $rp["id"],
            ];
        }

        $reviews = (new Review($pdo))->findByProduct($id);

        require __DIR__ . "/../views/product-detail.php";
    }
}
