<?php
$action ??= '';

function formatPrice(float $price): string
{
    return '$' . number_format($price, 2);
}

function formatDate(?string $date): string
{
    return $date ? date('M j, Y g:i A', strtotime($date)) : 'N/A';
}

function statusBadge(string $status): string
{
    return match ($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'shipped' => 'bg-blue-100 text-blue-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — <?= e(ucfirst(str_replace('-', ' ', $action))) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-link {
            transition: all 0.2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-xl">
            <div class="p-6 border-b border-slate-700">
                <h1 class="text-xl font-bold tracking-wide"><i class="fas fa-cube mr-2 text-indigo-400"></i>Admin Panel</h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <?php
                $menuItems = [
                    ['dashboard', 'fas fa-chart-line', 'Dashboard'],
                    ['products', 'fas fa-box', 'Products'],
                    ['orders', 'fas fa-shopping-cart', 'Orders'],
                    ['users', 'fas fa-users', 'Users'],
                    ['inventory', 'fas fa-warehouse', 'Inventory'],
                    ['brands', 'fas fa-tag', 'Brands'],
                    ['categories', 'fas fa-folder', 'Categories'],
                    ['discounts', 'fas fa-percent', 'Discounts'],
                    ['collections', 'fas fa-layer-group', 'Collections'],
                    ['cities', 'fas fa-city', 'Cities'],
                    ['shipping-rules', 'fas fa-truck', 'Shipping Rules'],
                    ['audit-logs', 'fas fa-clipboard-list', 'Audit Logs'],
                ];
                foreach ($menuItems as [$itemAction, $icon, $label]):
                    $isActive = $action === $itemAction || ($itemAction === 'products' && in_array($action, ['product-create', 'product-edit']));
                    $isActive = $isActive || ($itemAction === 'users' && in_array($action, ['user-edit']));
                    $isActive = $isActive || ($itemAction === 'discounts' && in_array($action, ['discount-edit']));
                ?>
                    <a href="?route=admin&action=<?= $itemAction ?>"
                        class="sidebar-link flex items-center px-6 py-3 <?= $isActive ? 'active border-l-4 border-indigo-400 bg-slate-800' : 'text-slate-300' ?>">
                        <i class="<?= $icon ?> w-6"></i>
                        <span><?= $label ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <a href="?route=logout" class="flex items-center px-4 py-2 text-red-400 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <h2 class="text-2xl font-semibold text-gray-800"><?= e(ucfirst(str_replace('-', ' ', $action))) ?></h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">Admin #<?= $_SESSION['user_id'] ?? '?' ?></span>
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                    </div>
                </div>
            </header>

            <div class="p-8 fade-in">
                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p class="font-medium"><?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['admin_error'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p class="font-medium"><?= e($_SESSION['admin_error']) ?></p>
                    </div>
                    <?php unset($_SESSION['admin_error']); ?>
                <?php endif; ?>

                <?php
                if ($action === 'dashboard'): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                            <div class="text-gray-500 text-sm font-medium uppercase">Total Products</div>
                            <div class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['total_products'] ?? 0 ?></div>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                            <div class="text-gray-500 text-sm font-medium uppercase">Total Orders</div>
                            <div class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['total_orders'] ?? 0 ?></div>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                            <div class="text-gray-500 text-sm font-medium uppercase">Total Users</div>
                            <div class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['total_users'] ?? 0 ?></div>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
                            <div class="text-gray-500 text-sm font-medium uppercase">Pending Orders</div>
                            <div class="text-3xl font-bold text-gray-800 mt-1"><?= $stats['pending_orders'] ?? 0 ?></div>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
                            <div class="text-gray-500 text-sm font-medium uppercase">Total Revenue</div>
                            <div class="text-3xl font-bold text-gray-800 mt-1"><?= formatPrice($stats['total_revenue'] ?? 0) ?></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white rounded-xl shadow">
                            <div class="px-6 py-4 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-lg">Recent Orders</h3>
                                <a href="?route=admin&action=orders" class="text-indigo-600 hover:text-indigo-800 text-sm">View All</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">Order</th>
                                            <th class="px-6 py-3">Customer</th>
                                            <th class="px-6 py-3">Amount</th>
                                            <th class="px-6 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($recentOrders ?? [] as $order): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-3 font-medium">#<?= $order['id'] ?></td>
                                                <td class="px-6 py-3 text-gray-600"><?= e($order['client_name'] ?? 'N/A') ?></td>
                                                <td class="px-6 py-3 font-medium"><?= formatPrice((float)($order['subtotal'] ?? 0)) ?></td>
                                                <td class="px-6 py-3">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= statusBadge($order['shipping_status'] ?? '') ?>">
                                                        <?= ucfirst(e($order['shipping_status'] ?? 'unknown')) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($recentOrders)): ?>
                                            <tr>
                                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">No orders yet</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow">
                            <div class="px-6 py-4 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-lg text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Alerts</h3>
                                <a href="?route=admin&action=inventory" class="text-indigo-600 hover:text-indigo-800 text-sm">Manage Inventory</a>
                            </div>
                            <div class="p-6">
                                <?php if (!empty($lowStock)): ?>
                                    <div class="space-y-3">
                                        <?php foreach ($lowStock as $item): ?>
                                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-100">
                                                <div>
                                                    <div class="font-medium text-gray-800"><?= e($item['product_name'] ?? 'Unknown') ?></div>
                                                    <div class="text-sm text-gray-500"><?= e($item['color'] ?? '') ?> / <?= e($item['size'] ?? '') ?></div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-red-600 font-bold"><?= $item['stock'] ?? 0 ?> left</div>
                                                    <div class="text-xs text-gray-500">SKU: <?= e($item['sku'] ?? 'N/A') ?></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-500 text-center py-4">All items are well stocked!</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'products'): ?>
                    <div class="flex justify-between items-center mb-6">
                        <div class="relative">
                            <input type="text" id="productSearch" placeholder="Search products..."
                                class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64"
                                onkeyup="filterTable('productSearch', 'productsTable')">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <a href="?route=admin&action=product-create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>

                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <table class="w-full text-left" id="productsTable">
                            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Brand</th>
                                    <th class="px-6 py-3">Category</th>
                                    <th class="px-6 py-3">Base Price</th>
                                    <th class="px-6 py-3">Gender</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($products ?? [] as $p): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-gray-500">#<?= $p['id'] ?? '?' ?></td>
                                        <td class="px-6 py-4 font-medium text-gray-800"><?= e($p['name'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($p['brand_name'] ?? $p['brand_id'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($p['category_material'] ?? $p['category_id'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4 font-medium"><?= formatPrice((float)($p['base_price'] ?? 0)) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 capitalize">
                                                <?= e($p['gender'] ?? 'unisex') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="?route=admin&action=product-edit&id=<?= $p['id'] ?>" class="text-indigo-600 hover:text-indigo-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?route=admin&action=product-delete&id=<?= $p['id'] ?>"
                                                onclick="return confirm('Delete this product? This cannot be undone.')"
                                                class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No products found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php
                elseif (in_array($action, ['product-create', 'product-edit'])):
                    $isEdit = $action === 'product-edit';
                    $p = $product ?? null;
                ?>
                    <div class="max-w-2xl mx-auto">
                        <a href="?route=admin&action=products" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>

                        <div class="bg-white rounded-xl shadow p-8">
                            <h3 class="text-xl font-semibold mb-6"><?= $isEdit ? 'Edit Product' : 'Create New Product' ?></h3>

                            <form method="POST" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                                    <input type="text" name="name" value="<?= e($p['name'] ?? '') ?>" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand *</label>
                                        <select name="brand_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select Brand</option>
                                            <?php foreach ($brands ?? [] as $b): ?>
                                                <option value="<?= $b['id'] ?>" <?= ($p['brand_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                                                    <?= e($b['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                        <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories ?? [] as $c): ?>
                                                <option value="<?= $c['id'] ?>" <?= ($p['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                                    <?= e($c['material'] ?? $c['name'] ?? 'Unnamed') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="4"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"><?= e($p['description'] ?? '') ?></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Base Price *</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input type="number" name="base_price" step="0.01" min="0"
                                                value="<?= $p['base_price'] ?? '' ?>" required
                                                class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                        <select name="gender" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                            <option value="unisex" <?= ($p['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                                            <option value="mens" <?= ($p['gender'] ?? '') === 'mens' ? 'selected' : '' ?>>Men's</option>
                                            <option value="womens" <?= ($p['gender'] ?? '') === 'womens' ? 'selected' : '' ?>>Women's</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex gap-3 pt-4">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                        <?= $isEdit ? 'Update Product' : 'Create Product' ?>
                                    </button>
                                    <a href="?route=admin&action=products" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php
                elseif ($action === 'orders'): ?>
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex gap-2">
                            <?php
                            $statusFilters = ['all' => 'All', 'pending' => 'Pending', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
                            foreach ($statusFilters as $key => $label):
                                $active = ($_GET['status'] ?? 'all') === $key;
                            ?>
                                <a href="?route=admin&action=orders&status=<?= $key ?>"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border' ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                <tr>
                                    <th class="px-6 py-3">Order #</th>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3">City</th>
                                    <th class="px-6 py-3">Subtotal</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($orders ?? [] as $o): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-medium">#<?= $o['id'] ?></td>
                                        <td class="px-6 py-4"><?= e($o['client_name'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($o['city_name'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4 font-medium"><?= formatPrice((float)($o['subtotal'] ?? 0)) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= statusBadge($o['shipping_status'] ?? '') ?>">
                                                <?= ucfirst(e($o['shipping_status'] ?? 'unknown')) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 text-sm"><?= formatDate($o['created_at'] ?? null) ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2">
                                                <?php if (($o['shipping_status'] ?? '') === 'pending'): ?>
                                                    <form method="POST" action="?route=admin&action=order-assign" class="flex gap-1">
                                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                                        <select name="delivery_guy_id" required class="text-sm border rounded px-2 py-1">
                                                            <option value="">Assign...</option>
                                                            <?php foreach ($deliveryGuys ?? [] as $dg): ?>
                                                                <option value="<?= $dg['id'] ?>"><?= e($dg['first_name'] . ' ' . $dg['last_name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-800" title="Assign">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form method="POST" action="?route=admin&action=order-status" class="flex gap-1">
                                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                                    <select name="status" class="text-sm border rounded px-2 py-1" onchange="this.form.submit()">
                                                        <option value="">Update...</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="shipped">Shipped</option>
                                                        <option value="delivered">Delivered</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php
                elseif ($action === 'users'): ?>
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3">City</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($users ?? [] as $u): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-gray-500">#<?= $u['id'] ?></td>
                                        <td class="px-6 py-4 font-medium">
                                            <?= e(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($u['email'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            <?= ($u['role'] ?? '') === 'admin' ? 'bg-purple-100 text-purple-800' : (($u['role'] ?? '') === 'delivery_guy' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                                                <?= ucfirst(e($u['role'] ?? 'user')) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($u['city_name'] ?? $u['city_id'] ?? 'N/A') ?></td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="?route=admin&action=user-edit&id=<?= $u['id'] ?>" class="text-indigo-600 hover:text-indigo-800">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (($u['role'] ?? '') !== 'admin'): ?>
                                                <a href="?route=admin&action=user-delete&id=<?= $u['id'] ?>"
                                                    onclick="return confirm('Delete this user?')"
                                                    class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php
                elseif ($action === 'user-edit'): ?>
                    <div class="max-w-2xl mx-auto">
                        <a href="?route=admin&action=users" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>

                        <div class="bg-white rounded-xl shadow p-8">
                            <h3 class="text-xl font-semibold mb-6">Edit User #<?= $user['id'] ?? '?' ?></h3>

                            <form method="POST" class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" name="first_name" value="<?= e($user['first_name'] ?? '') ?>" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                        <input type="text" name="last_name" value="<?= e($user['last_name'] ?? '') ?>" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <select name="role" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                            <option value="user" <?= ($user['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="delivery_guy" <?= ($user['role'] ?? '') === 'delivery_guy' ? 'selected' : '' ?>>Delivery Guy</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                        <select name="city_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                            <option value="">No City</option>
                                            <?php foreach ($cities ?? [] as $c): ?>
                                                <option value="<?= $c['id'] ?>" <?= ($user['city_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                                    <?= e($c['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex gap-3 pt-4">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                        Update User
                                    </button>
                                    <a href="?route=admin&action=users" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php
                elseif ($action === 'inventory'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                                <h3 class="text-lg font-semibold mb-4"><i class="fas fa-plus-circle text-green-600 mr-2"></i>Restock Inventory</h3>
                                <form method="POST" action="?route=admin&action=inventory-restock" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Variant</label>
                                        <select name="variant_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select Variant</option>
                                            <?php foreach ($allVariants ?? [] as $v): ?>
                                                <option value="<?= $v['id'] ?>">
                                                    <?= e(($v['product_name'] ?? 'Unknown') . ' — ' . ($v['color'] ?? '') . ' / ' . ($v['size'] ?? '')) ?>
                                                    (<?= $v['stock_quantity'] ?? 0 ?> in stock)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                        <input type="number" name="quantity" min="1" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input type="number" name="unit_price" step="0.01" min="0.01" required
                                                class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium transition">
                                        Record Restock
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <div class="px-6 py-4 border-b">
                                    <h3 class="font-semibold text-lg">All Variants</h3>
                                </div>
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">Product</th>
                                            <th class="px-6 py-3">Variant</th>
                                            <th class="px-6 py-3">SKU</th>
                                            <th class="px-6 py-3">Stock</th>
                                            <th class="px-6 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($allVariants ?? [] as $v):
                                            $stock = (int)($v['stock_quantity'] ?? 0);
                                            $isLow = $stock <= 10;
                                        ?>
                                            <tr class="hover:bg-gray-50 transition <?= $isLow ? 'bg-red-50' : '' ?>">
                                                <td class="px-6 py-4 font-medium"><?= e($v['product_name'] ?? 'Unknown') ?></td>
                                                <td class="px-6 py-4 text-gray-600"><?= e(($v['color'] ?? '') . ' / ' . ($v['size'] ?? '')) ?></td>
                                                <td class="px-6 py-4 text-gray-500 font-mono text-sm"><?= e($v['sku'] ?? 'N/A') ?></td>
                                                <td class="px-6 py-4 font-bold <?= $isLow ? 'text-red-600' : 'text-gray-800' ?>"><?= $stock ?></td>
                                                <td class="px-6 py-4">
                                                    <?php if ($isLow): ?>
                                                        <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 font-medium">Low Stock</span>
                                                    <?php else: ?>
                                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 font-medium">OK</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($allVariants)): ?>
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">No variants found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'brands'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Add New Brand</h3>
                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                                        <input type="text" name="name" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-medium transition">
                                        Add Brand
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">ID</th>
                                            <th class="px-6 py-3">Name</th>
                                            <th class="px-6 py-3 text-right">Products</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($brands ?? [] as $b): ?>
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-gray-500">#<?= $b['id'] ?></td>
                                                <td class="px-6 py-4 font-medium"><?= e($b['name']) ?></td>
                                                <td class="px-6 py-4 text-right text-gray-500"><?= $b['product_count'] ?? '-' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($brands)): ?>
                                            <tr>
                                                <td colspan="3" class="px-6 py-12 text-center text-gray-400">No brands found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'categories'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Add Category</h3>
                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category ID</label>
                                        <input type="number" name="id" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Material</label>
                                        <input type="text" name="material" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-medium transition">
                                        Add Category
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">ID</th>
                                            <th class="px-6 py-3">Material</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($categories ?? [] as $c): ?>
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-gray-500">#<?= $c['id'] ?></td>
                                                <td class="px-6 py-4 font-medium"><?= e($c['material'] ?? $c['name'] ?? 'Unnamed') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($categories)): ?>
                                            <tr>
                                                <td colspan="2" class="px-6 py-12 text-center text-gray-400">No categories found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'discounts'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                                <h3 class="text-lg font-semibold mb-4">Create Discount</h3>
                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Code (optional)</label>
                                        <input type="text" name="code" placeholder="SUMMER2024"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                            <select name="discount_type" class="w-full px-4 py-2 border rounded-lg">
                                                <option value="%">Percentage %</option>
                                                <option value="$">Fixed $</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                            <input type="number" name="value" step="0.01" min="0" required
                                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                            <input type="date" name="start_date"
                                                class="w-full px-4 py-2 border rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                            <input type="date" name="end_date"
                                                class="w-full px-4 py-2 border rounded-lg">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Uses (optional)</label>
                                        <input type="number" name="n_uses" min="1"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Active</span>
                                    </label>
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-medium transition">
                                        Create Discount
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">Code</th>
                                            <th class="px-6 py-3">Type</th>
                                            <th class="px-6 py-3">Value</th>
                                            <th class="px-6 py-3">Period</th>
                                            <th class="px-6 py-3">Uses</th>
                                            <th class="px-6 py-3">Status</th>
                                            <th class="px-6 py-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($discounts ?? [] as $d): ?>
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 font-mono font-medium"><?= e($d['code'] ?? 'N/A') ?></td>
                                                <td class="px-6 py-4"><?= e($d['discount_type'] ?? '%') ?></td>
                                                <td class="px-6 py-4 font-medium"><?= $d['discount_type'] === '$' ? '$' : '' ?><?= $d['value'] ?? 0 ?><?= $d['discount_type'] === '%' ? '%' : '' ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    <?= $d['start_date'] ? date('M d', strtotime($d['start_date'])) : 'Always' ?> -
                                                    <?= $d['end_date'] ? date('M d, Y', strtotime($d['end_date'])) : 'No end' ?>
                                                </td>
                                                <td class="px-6 py-4 text-gray-600"><?= $d['uses_count'] ?? 0 ?><?= $d['n_uses'] ? '/' . $d['n_uses'] : '' ?></td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= ($d['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                                        <?= ($d['is_active'] ?? false) ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <a href="?route=admin&action=discount-edit&id=<?= $d['id'] ?>" class="text-indigo-600 hover:text-indigo-800" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?route=admin&action=discount-delete&id=<?= $d['id'] ?>"
                                                        onclick="return confirm('Delete this discount?')"
                                                        class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($discounts)): ?>
                                            <tr>
                                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">No discounts found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'discount-edit'): ?>
                    <div class="max-w-2xl mx-auto">
                        <a href="?route=admin&action=discounts" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Back to Discounts
                        </a>
                        <div class="bg-white rounded-xl shadow p-8">
                            <h3 class="text-xl font-semibold mb-6">Edit Discount</h3>
                            <form method="POST" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Code (optional)</label>
                                    <input type="text" name="code" value="<?= e($discount['code'] ?? '') ?>" placeholder="SUMMER2024"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <select name="discount_type" class="w-full px-4 py-2 border rounded-lg">
                                            <option value="%" <?= ($discount['discount_type'] ?? '') === '%' ? 'selected' : '' ?>>Percentage %</option>
                                            <option value="fixed" <?= ($discount['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed $</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                        <input type="number" name="value" step="0.01" min="0" value="<?= e($discount['value'] ?? '') ?>" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" name="start_date" value="<?= e($discount['start_date'] ?? '') ?>"
                                            class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="date" name="end_date" value="<?= e($discount['end_date'] ?? '') ?>"
                                            class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Uses (optional)</label>
                                    <input type="number" name="n_uses" min="1" value="<?= e($discount['n_uses'] ?? '') ?>"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" <?= ($discount['is_active'] ?? false) ? 'checked' : '' ?> class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">Active</span>
                                </label>
                                <div class="flex gap-3 pt-4">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                        Update Discount
                                    </button>
                                    <a href="?route=admin&action=discounts" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php
                elseif ($action === 'collections'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Create Collection</h3>
                                <form method="POST" class="space-y-4">
                                    <input type="hidden" name="create" value="1">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                        <input type="text" name="name" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" rows="3"
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                                        <input type="text" name="img" placeholder="https://..."
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Release Date</label>
                                        <input type="date" name="release_date"
                                            class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_active" checked class="rounded text-indigo-600">
                                            <span class="text-sm text-gray-700">Active</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_limited" class="rounded text-indigo-600">
                                            <span class="text-sm text-gray-700">Limited</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-medium transition">
                                        Create Collection
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <?php foreach ($collections ?? [] as $col): ?>
                                <div class="bg-white rounded-xl shadow overflow-hidden">
                                    <div class="p-6 border-b flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold"><?= e($col['name'] ?? 'Unnamed') ?></h3>
                                            <p class="text-gray-500 text-sm mt-1"><?= e($col['description'] ?? '') ?></p>
                                            <div class="flex gap-2 mt-2">
                                                <?php if ($col['is_active'] ?? false): ?>
                                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>
                                                <?php endif; ?>
                                                <?php if ($col['is_limited'] ?? false): ?>
                                                    <span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">Limited</span>
                                                <?php endif; ?>
                                                <?php if ($col['release_date']): ?>
                                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                        <?= date('M d, Y', strtotime($col['release_date'])) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($col['img']): ?>
                                            <img src="<?= e($col['img']) ?>" alt="" class="w-24 h-24 object-cover rounded-lg">
                                        <?php endif; ?>
                                    </div>

                                    <div class="px-6 py-4 bg-gray-50 border-b">
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="add_product" value="1">
                                            <input type="hidden" name="collection_id" value="<?= $col['id'] ?>">
                                            <select name="product_id" required class="flex-1 px-3 py-2 border rounded-lg text-sm">
                                                <option value="">Add product...</option>
                                                <?php foreach ($products ?? [] as $p): ?>
                                                    <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($collections)): ?>
                                <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
                                    <i class="fas fa-layer-group text-4xl mb-3"></i>
                                    <p>No collections yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php
                elseif ($action === 'cities'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Add City</h3>
                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City Name</label>
                                        <input type="text" name="name" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Rule</label>
                                        <select name="shipping_rule_id" class="w-full px-4 py-2 border rounded-lg">
                                            <option value="">None</option>
                                            <?php foreach ($shippingRules ?? [] as $sr): ?>
                                                <option value="<?= $sr['id'] ?>"><?= e($sr['name']) ?> (<?= formatPrice($sr['price'] ?? 0) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-medium transition">
                                        Add City
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">ID</th>
                                            <th class="px-6 py-3">Name</th>
                                            <th class="px-6 py-3">Shipping Rule</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($cities ?? [] as $c): ?>
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-gray-500">#<?= $c['id'] ?></td>
                                                <td class="px-6 py-4 font-medium"><?= e($c['name']) ?></td>
                                                <td class="px-6 py-4 text-gray-600"><?= e($c['rule_name'] ?? $c['shipping_rule_id'] ?? 'None') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($cities)): ?>
                                            <tr>
                                                <td colspan="3" class="px-6 py-12 text-center text-gray-400">No cities found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'shipping-rules'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow p-6">
                                <h3 class="text-lg font-semibold mb-4">Add Shipping Rule</h3>
                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rule Name</label>
                                        <input type="text" name="name" required
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Price</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input type="number" name="price" step="0.01" min="0" required
                                                class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Commission</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input type="number" name="delivery_commission" step="0.01" min="0" required
                                                class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Free Shipping Threshold</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input type="number" name="free_shipping_threshold" step="0.01" min="0" required
                                                class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-medium transition">
                                        Add Rule
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                        <tr>
                                            <th class="px-6 py-3">ID</th>
                                            <th class="px-6 py-3">Name</th>
                                            <th class="px-6 py-3">Price</th>
                                            <th class="px-6 py-3">Commission</th>
                                            <th class="px-6 py-3">Free Threshold</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <?php foreach ($shippingRules ?? [] as $sr): ?>
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-gray-500">#<?= $sr['id'] ?></td>
                                                <td class="px-6 py-4 font-medium"><?= e($sr['name']) ?></td>
                                                <td class="px-6 py-4"><?= formatPrice((float)($sr['price'] ?? 0)) ?></td>
                                                <td class="px-6 py-4"><?= formatPrice((float)($sr['delivery_commission'] ?? 0)) ?></td>
                                                <td class="px-6 py-4"><?= formatPrice((float)($sr['free_shipping_threshold'] ?? 0)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($shippingRules)): ?>
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">No shipping rules found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                elseif ($action === 'audit-logs'): ?>
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b flex justify-between items-center">
                            <h3 class="font-semibold text-lg">System Audit Logs</h3>
                            <span class="text-sm text-gray-500">Last 100 entries</span>
                        </div>
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                <tr>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Action</th>
                                    <th class="px-6 py-3">Entity</th>
                                    <th class="px-6 py-3">User</th>
                                    <th class="px-6 py-3">Details</th>
                                    <th class="px-6 py-3">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($logs ?? [] as $log): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-gray-500">#<?= $log['id'] ?? '?' ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-xs font-medium 
                                            <?= match (strtolower($log['action_performed'] ?? '')) {
                                                'create' => 'bg-green-100 text-green-800',
                                                'update' => 'bg-blue-100 text-blue-800',
                                                'delete' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            } ?>">
                                                <?= ucfirst(e($log['action_performed'] ?? 'unknown')) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($log['target_table'] ?? 'N/A') ?> #<?= $log['target_id'] ?? '?' ?></td>
                                        <td class="px-6 py-4 text-gray-600"><?= e($log['admin_name'] ?? ('User #' . ($log['admin_id'] ?? '?'))) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?= e($log['action_performed'] ?? '') ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"><?= formatDate($log['created_at'] ?? null) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No audit logs found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            if (!table) return;
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        }
    </script>
</body>

</html>