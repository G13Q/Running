<?php

class CartController
{
    public function index(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $action = $_POST["action"] ?? "";
            if ($action === "add") {
                $this->add();
            } elseif ($action === "remove") {
                $this->remove();
            } elseif ($action === "update") {
                $this->update();
            }
        } else {
            $this->show();
        }
    }

    private function show(): void
    {
        $cart = $_SESSION["cart"] ?? [];
        $needsRefresh = false;

        foreach ($cart as $data) {
            if (empty($data["name"])) {
                $needsRefresh = true;
                break;
            }
        }

        if ($needsRefresh) {
            require_once __DIR__ . "/../config/database.php";
            require_once __DIR__ . "/../models/product.php";
            require_once __DIR__ . "/../models/product_variant.php";

            $productModel = new Product($pdo);
            $variantModel = new ProductVariant($pdo);

            foreach ($cart as $productId => &$data) {
                if (!empty($data["name"])) continue;
                $product = $productModel->findById((int) $productId);
                if (!$product) continue;
                $variants = $variantModel->findByProduct((int) $productId);
                $first = $variants[0] ?? [];
                $data["name"] = $product["name"];
                $data["price"] = $product["base_price"];
                $data["image"] = $first["thumbnail"] ?? "";
            }
            unset($data);
            $_SESSION["cart"] = $cart;
        }

        $cartItems = [];
        foreach ($cart as $productId => $data) {
            $cartItems[] = [
                "product_id" => $productId,
                "name" => $data["name"] ?? "Product",
                "price" => $data["price"] ?? 0,
                "image" => $data["image"] ?? "",
                "size" => $data["size"] ?? "",
                "quantity" => $data["quantity"] ?? 1,
            ];
        }

        require __DIR__ . "/../views/cart.php";
    }

    private function add(): void
    {
        $productId = (int) ($_POST["product_id"] ?? 0);
        if ($productId <= 0) {
            header("Location: ?route=shop-all");
            exit;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $product = (new Product($pdo))->findById($productId);
        if (!$product) {
            header("Location: ?route=shop-all");
            exit;
        }

        $variants = (new ProductVariant($pdo))->findByProduct($productId);
        $first = $variants[0] ?? [];
        $thumbnail = $first["thumbnail"] ?? "";
        $size = trim($_POST["size"] ?? "");

        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        if (isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId]["quantity"]++;
        } else {
            $_SESSION["cart"][$productId] = [
                "quantity" => 1,
                "name" => $product["name"],
                "price" => $product["base_price"],
                "image" => $thumbnail,
                "size" => $size,
            ];
        }

        $this->redirectWithCartOpen();
    }

    private function remove(): void
    {
        $productId = (int) ($_POST["product_id"] ?? 0);
        unset($_SESSION["cart"][$productId]);

        $this->redirectWithCartOpen();
    }

    private function update(): void
    {
        $productId = (int) ($_POST["product_id"] ?? 0);
        $quantity = max(1, (int) ($_POST["quantity"] ?? 1));

        if (isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId]["quantity"] = $quantity;
        }

        $this->redirectWithCartOpen();
    }

    private function redirectWithCartOpen(): void
    {
        $referer = $_SERVER["HTTP_REFERER"] ?? "?route=home";
        $separator = str_contains($referer, "?") ? "&" : "?";
        header("Location: " . $referer . $separator . "cart_open=1");
        exit;
    }
}
