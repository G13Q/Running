<?php

class AdminController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["user_id"]) || ($_SESSION["user_role"] ?? "") !== "admin") {
            header("Location: ?route=login");
            exit;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/order.php";
        require_once __DIR__ . "/../models/user.php";
        require_once __DIR__ . "/../models/product_variant.php";
        require_once __DIR__ . "/../models/brand.php";
        require_once __DIR__ . "/../models/category.php";
        require_once __DIR__ . "/../models/discount.php";
        require_once __DIR__ . "/../models/collection.php";
        require_once __DIR__ . "/../models/city.php";
        require_once __DIR__ . "/../models/shipping_rule.php";
        require_once __DIR__ . "/../models/audit_log.php";
        require_once __DIR__ . "/../models/inventory_log.php";

        $adminId = (int) $_SESSION["user_id"];

        $action = $_GET["action"] ?? "dashboard";

        switch ($action) {
            case "dashboard":
                $this->dashboard($pdo);
                break;
            case "products":
                $this->products($pdo);
                break;
            case "product-create":
                $this->productCreate($pdo);
                break;
            case "product-edit":
                $this->productEdit($pdo);
                break;
            case "product-delete":
                $this->productDelete($pdo);
                break;
            case "orders":
                $this->orders($pdo);
                break;
            case "order-assign":
                $this->orderAssign($pdo);
                break;
            case "order-status":
                $this->orderStatus($pdo);
                break;
            case "users":
                $this->users($pdo);
                break;
            case "user-edit":
                $this->userEdit($pdo);
                break;
            case "user-delete":
                $this->userDelete($pdo);
                break;
            case "inventory":
                $this->inventory($pdo);
                break;
            case "inventory-restock":
                $this->inventoryRestock($pdo, $adminId);
                break;
            case "brands":
                $this->brands($pdo);
                break;
            case "categories":
                $this->categories($pdo);
                break;
            case "discounts":
                $this->discounts($pdo);
                break;
            case "discount-edit":
                $this->discountEdit($pdo);
                break;
            case "discount-delete":
                $this->discountDelete($pdo);
                break;
            case "collections":
                $this->collections($pdo);
                break;
            case "cities":
                $this->cities($pdo);
                break;
            case "shipping-rules":
                $this->shippingRules($pdo);
                break;
            case "audit-logs":
                $this->auditLogs($pdo);
                break;
            default:
                $this->dashboard($pdo);
                break;
        }
    }

    private function dashboard(PDO $pdo): void
    {
        $productModel  = new Product($pdo);
        $orderModel    = new Order($pdo);
        $userModel     = new User($pdo);
        $variantModel  = new ProductVariant($pdo);

        $stats = [
            "total_products"   => count($productModel->findAll()),
            "total_orders"     => (int) $pdo->query("SELECT COUNT(*) FROM Orders")->fetchColumn(),
            "total_users"      => (int) $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'user'")->fetchColumn(),
            "pending_orders"   => (int) $pdo->query("SELECT COUNT(*) FROM Orders WHERE shipping_status = 'pending'")->fetchColumn(),
            "total_revenue"    => (float) $pdo->query("SELECT COALESCE(SUM(subtotal), 0) FROM Orders WHERE shipping_status != 'cancelled'")->fetchColumn(),
        ];

        $recentOrders = $pdo->query("
            SELECT o.id, o.subtotal, o.shipping_status, o.created_at,
                   CONCAT(u.first_name, ' ', u.last_name) as client_name
            FROM Orders o
            JOIN Users u ON o.client_id = u.id
            ORDER BY o.created_at DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        $lowStock = $variantModel->findLowStock();

        $action = "dashboard";
        require __DIR__ . "/../views/admin.php";
    }

    private function products(PDO $pdo): void
    {
        $productModel = new Product($pdo);
        $products = $productModel->findAll();
        $action = "products";
        require __DIR__ . "/../views/admin.php";
    }

    private function productCreate(PDO $pdo): void
    {
        $productModel  = new Product($pdo);
        $brandModel    = new Brand($pdo);
        $categoryModel = new Category($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name        = trim($_POST["name"] ?? "");
            $brandId     = (int) ($_POST["brand_id"] ?? 0);
            $categoryId  = (int) ($_POST["category_id"] ?? 0);
            $description = trim($_POST["description"] ?? "");
            $basePrice   = (float) ($_POST["base_price"] ?? 0);
            $gender      = $_POST["gender"] ?? "unisex";

            if ($name && $brandId && $categoryId) {
                $productId = $productModel->create($name, $brandId, $categoryId, $description, $basePrice, $gender);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Products", (string) $productId);
                header("Location: ?route=admin&action=products");
                exit;
            }
            $error = "All required fields must be filled.";
        }

        $brands     = $brandModel->findAll();
        $categories = $categoryModel->findAll();
        $action     = "product-create";
        require __DIR__ . "/../views/admin.php";
    }

    private function productEdit(PDO $pdo): void
    {
        $productModel  = new Product($pdo);
        $brandModel    = new Brand($pdo);
        $categoryModel = new Category($pdo);
        $id = (int) ($_GET["id"] ?? 0);

        $product = $productModel->findById($id);
        if (!$product) {
            header("Location: ?route=admin&action=products");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name        = trim($_POST["name"] ?? "");
            $brandId     = (int) ($_POST["brand_id"] ?? 0);
            $categoryId  = (int) ($_POST["category_id"] ?? 0);
            $description = trim($_POST["description"] ?? "");
            $basePrice   = (float) ($_POST["base_price"] ?? 0);
            $gender      = $_POST["gender"] ?? "unisex";

            if ($name && $brandId && $categoryId) {
                $productModel->update($id, $name, $brandId, $categoryId, $description, $basePrice, $gender);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "UPDATE", "Products", (string) $id);
                header("Location: ?route=admin&action=products");
                exit;
            }
            $error = "All required fields must be filled.";
        }

        $brands     = $brandModel->findAll();
        $categories = $categoryModel->findAll();
        $action     = "product-edit";
        require __DIR__ . "/../views/admin.php";
    }

    private function productDelete(PDO $pdo): void
    {
        $productModel = new Product($pdo);
        $id = (int) ($_GET["id"] ?? 0);
        if ($id) {
            $productModel->delete($id);
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "DELETE", "Products", (string) $id);
        }
        header("Location: ?route=admin&action=products");
        exit;
    }

    private function orders(PDO $pdo): void
    {
        $orderModel = new Order($pdo);
        $userModel  = new User($pdo);

        $status = $_GET["status"] ?? "all";
        if ($status === "all") {
            $orders = $pdo->query("
                SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as client_name, c.name as city_name
                FROM Orders o
                JOIN Users u ON u.id = o.client_id
                JOIN Cities c ON c.id = o.city_id
                ORDER BY o.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $orders = $orderModel->findByStatus($status);
        }

        $deliveryGuys = $userModel->findByRole("delivery_guy");
        $action = "orders";
        require __DIR__ . "/../views/admin.php";
    }

    private function orderAssign(PDO $pdo): void
    {
        $orderModel = new Order($pdo);
        $orderId = (int) ($_POST["order_id"] ?? 0);
        $deliveryGuyId = (int) ($_POST["delivery_guy_id"] ?? 0);

        if ($orderId && $deliveryGuyId) {
            $orderModel->assignDeliveryGuy($orderId, $deliveryGuyId);
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "ASSIGN_DELIVERY", "Orders", (string) $orderId);
        }
        header("Location: ?route=admin&action=orders");
        exit;
    }

    private function orderStatus(PDO $pdo): void
    {
        $orderModel = new Order($pdo);
        $orderId = (int) ($_POST["order_id"] ?? 0);
        $status  = $_POST["status"] ?? "";

        if ($orderId && in_array($status, ["pending", "shipped", "delivered", "cancelled"], true)) {
            if ($status === "delivered") {
                $orderModel->markDelivered($orderId);
            } elseif ($status === "cancelled") {
                $orderModel->cancel($orderId);
            } else {
                $orderModel->updateStatus($orderId, $status);
            }
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "UPDATE_STATUS", "Orders", (string) $orderId);
        }
        header("Location: ?route=admin&action=orders");
        exit;
    }

    private function users(PDO $pdo): void
    {
        $userModel = new User($pdo);
        $users = $userModel->findAll();
        $action = "users";
        require __DIR__ . "/../views/admin.php";
    }

    private function userEdit(PDO $pdo): void
    {
        $userModel = new User($pdo);
        $cityModel = new City($pdo);
        $id = (int) ($_GET["id"] ?? 0);

        $user = $userModel->findById($id);
        if (!$user) {
            header("Location: ?route=admin&action=users");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $firstName = trim($_POST["first_name"] ?? "");
            $lastName  = trim($_POST["last_name"] ?? "");
            $email     = trim($_POST["email"] ?? "");
            $role      = $_POST["role"] ?? "user";
            $cityId    = $_POST["city_id"] ? (int) $_POST["city_id"] : null;

            if ($firstName && $lastName && $email) {
                $userModel->update($id, $firstName, $lastName, $email, $cityId, $role);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "UPDATE", "Users", (string) $id);
                header("Location: ?route=admin&action=users");
                exit;
            }
            $error = "All fields are required.";
        }

        $cities = $cityModel->findAll();
        $action = "user-edit";
        require __DIR__ . "/../views/admin.php";
    }

    private function userDelete(PDO $pdo): void
    {
        $userModel = new User($pdo);
        $id = (int) ($_GET["id"] ?? 0);
        if ($id) {
            $userModel->delete($id);
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "DELETE", "Users", (string) $id);
        }
        header("Location: ?route=admin&action=users");
        exit;
    }

    private function inventory(PDO $pdo): void
    {
        $variantModel = new ProductVariant($pdo);
        $productModel = new Product($pdo);

        $variants = $variantModel->findLowStock();
        $allVariants = $pdo->query("
            SELECT pv.*, p.name as product_name
            FROM Product_variants pv
            JOIN Products p ON p.id = pv.product_id
            ORDER BY p.name, pv.color, pv.size
        ")->fetchAll(PDO::FETCH_ASSOC);

        $action = "inventory";
        require __DIR__ . "/../views/admin.php";
    }

    private function inventoryRestock(PDO $pdo, int $adminId): void
    {
        $inventoryLog = new InventoryLog($pdo);
        $variantId    = (int) ($_POST["variant_id"] ?? 0);
        $quantity     = (int) ($_POST["quantity"] ?? 0);
        $unitPrice    = (float) ($_POST["unit_price"] ?? 0);

        if ($variantId && $quantity > 0 && $unitPrice > 0) {
            try {
                $inventoryLog->restock($variantId, $adminId, $quantity, $unitPrice);
                $auditLog = new AuditLog($pdo);
                $auditLog->log($adminId, "RESTOCK", "Product_variants", (string) $variantId);
            } catch (Exception $e) {
                $_SESSION["admin_error"] = "Restock failed: " . $e->getMessage();
            }
        }
        header("Location: ?route=admin&action=inventory");
        exit;
    }

    private function brands(PDO $pdo): void
    {
        $brandModel = new Brand($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = trim($_POST["name"] ?? "");
            if ($name) {
                $brandId = $brandModel->create($name);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Brands", (string) $brandId);
            }
            header("Location: ?route=admin&action=brands");
            exit;
        }

        $brands = $brandModel->findAll();
        $action = "brands";
        require __DIR__ . "/../views/admin.php";
    }

    private function categories(PDO $pdo): void
    {
        $categoryModel = new Category($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id       = (int) ($_POST["id"] ?? 0);
            $material = trim($_POST["material"] ?? "");
            if ($id && $material) {
                $categoryModel->create($id, $material);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Categories", (string) $id);
            }
            header("Location: ?route=admin&action=categories");
            exit;
        }

        $categories = $categoryModel->findAll();
        $action = "categories";
        require __DIR__ . "/../views/admin.php";
    }

    private function discounts(PDO $pdo): void
    {
        $discountModel = new Discount($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $code        = trim($_POST["code"] ?? "") ?: null;
            $type        = $_POST["discount_type"] ?? "%";
            $value       = (float) ($_POST["value"] ?? 0);
            $startDate   = $_POST["start_date"] ?: null;
            $endDate     = $_POST["end_date"] ?: null;
            $nUses       = $_POST["n_uses"] ? (int) $_POST["n_uses"] : null;
            $isActive    = isset($_POST["is_active"]);

            $discountId = $discountModel->create($code, $type, $value, $startDate, $endDate, $nUses, $isActive);
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Discounts", (string) $discountId);
            header("Location: ?route=admin&action=discounts");
            exit;
        }

        $discounts = $discountModel->findAll();
        $action = "discounts";
        require __DIR__ . "/../views/admin.php";
    }

    private function discountEdit(PDO $pdo): void
    {
        $discountModel = new Discount($pdo);
        $id = (int) ($_GET["id"] ?? 0);

        $discount = $discountModel->findById($id);
        if (!$discount) {
            header("Location: ?route=admin&action=discounts");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $code        = trim($_POST["code"] ?? "") ?: null;
            $type        = $_POST["discount_type"] ?? "%";
            $value       = (float) ($_POST["value"] ?? 0);
            $startDate   = $_POST["start_date"] ?: null;
            $endDate     = $_POST["end_date"] ?: null;
            $nUses       = $_POST["n_uses"] ? (int) $_POST["n_uses"] : null;
            $isActive    = isset($_POST["is_active"]);

            $discountModel->update($id, $code, $type, $value, $startDate, $endDate, $nUses, $isActive);
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "UPDATE", "Discounts", (string) $id);
            header("Location: ?route=admin&action=discounts");
            exit;
        }

        $discounts = $discountModel->findAll();
        $action = "discount-edit";
        require __DIR__ . "/../views/admin.php";
    }

    private function discountDelete(PDO $pdo): void
    {
        $discountModel = new Discount($pdo);
        $id = (int) ($_GET["id"] ?? 0);
        if ($id) {
            $discountModel->delete($id);
            $auditLog = new AuditLog($pdo);
            $auditLog->log((int) $_SESSION["user_id"], "DELETE", "Discounts", (string) $id);
        }
        header("Location: ?route=admin&action=discounts");
        exit;
    }

    private function collections(PDO $pdo): void
    {
        $collectionModel = new Collection($pdo);
        $productModel    = new Product($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create"])) {
            $name        = trim($_POST["name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $img         = trim($_POST["img"] ?? "") ?: null;
            $isActive    = isset($_POST["is_active"]);
            $isLimited   = isset($_POST["is_limited"]);
            $releaseDate = $_POST["release_date"] ?: null;

            if ($name) {
                $collectionId = $collectionModel->create($name, $description, $img, $isActive, $isLimited, $releaseDate);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Collections", (string) $collectionId);
            }
            header("Location: ?route=admin&action=collections");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_product"])) {
            $collectionId = (int) ($_POST["collection_id"] ?? 0);
            $productId    = (int) ($_POST["product_id"] ?? 0);
            if ($collectionId && $productId) {
                $collectionModel->addProduct($collectionId, $productId);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "ADD_PRODUCT", "Product_collections", $collectionId . ":" . $productId);
            }
            header("Location: ?route=admin&action=collections");
            exit;
        }

        $collections = $collectionModel->findAll();
        $products    = $productModel->findAll();
        $action = "collections";
        require __DIR__ . "/../views/admin.php";
    }

    private function cities(PDO $pdo): void
    {
        $cityModel = new City($pdo);
        $shippingRuleModel = new ShippingRule($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name           = trim($_POST["name"] ?? "");
            $shippingRuleId = $_POST["shipping_rule_id"] ? (int) $_POST["shipping_rule_id"] : null;

            if ($name) {
                $cityId = $cityModel->create($name, $shippingRuleId);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Cities", (string) $cityId);
            }
            header("Location: ?route=admin&action=cities");
            exit;
        }

        $cities = $cityModel->findAll();
        $shippingRules = $shippingRuleModel->findAll();
        $action = "cities";
        require __DIR__ . "/../views/admin.php";
    }

    private function shippingRules(PDO $pdo): void
    {
        $shippingRuleModel = new ShippingRule($pdo);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name                   = trim($_POST["name"] ?? "");
            $price                  = (float) ($_POST["price"] ?? 0);
            $deliveryCommission     = (float) ($_POST["delivery_commission"] ?? 0);
            $freeShippingThreshold  = (float) ($_POST["free_shipping_threshold"] ?? 0);

            if ($name) {
                $ruleId = $shippingRuleModel->create($name, $price, $deliveryCommission, $freeShippingThreshold);
                $auditLog = new AuditLog($pdo);
                $auditLog->log((int) $_SESSION["user_id"], "CREATE", "Shipping_rules", (string) $ruleId);
            }
            header("Location: ?route=admin&action=shipping-rules");
            exit;
        }

        $shippingRules = $shippingRuleModel->findAll();
        $action = "shipping-rules";
        require __DIR__ . "/../views/admin.php";
    }

    private function auditLogs(PDO $pdo): void
    {
        $auditLogModel = new AuditLog($pdo);
        $logs = $auditLogModel->findAll(100);
        $action = "audit-logs";
        require __DIR__ . "/../views/admin.php";
    }
}
