<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('/assets/css/main.css') ?>">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="/" class="admin-logo">ROCK STEADY</a>
            </div>
            <nav class="admin-nav">
                <a href="/admin" class="admin-nav-item <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                <a href="/admin/products" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 01-8 0"></path></svg>
                    Products
                </a>
                <a href="/admin/categories" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/categories') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"></path></svg>
                    Categories
                </a>
                <a href="/admin/collections" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/collections') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    Collections
                </a>
                <a href="/admin/orders" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Orders
                    <?php if ($orderStats['pending_orders'] > 0): ?>
                    <span class="badge badge-warning" style="margin-left: auto;"><?= $orderStats['pending_orders'] ?></span>
                    <?php endif; ?>
                </a>
                <a href="/admin/customers" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/customers') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87"></path><path d="M16 3.13a4 4 0 010 7.75"></path></svg>
                    Customers
                </a>
                <a href="/admin/partners" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/partners') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                    Partners
                </a>
                <a href="/admin/sponsorships" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/sponsorships') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    Sponsorships
                </a>
                <a href="/admin/contacts" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/contacts') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    Contacts
                </a>
                <a href="/admin/reviews" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/reviews') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    Reviews
                    <?php if ($pendingReviews > 0): ?>
                    <span class="badge badge-warning" style="margin-left: auto;"><?= $pendingReviews ?></span>
                    <?php endif; ?>
                </a>
                <a href="/admin/coupons" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/coupons') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"></path></svg>
                    Coupons
                </a>
                <a href="/admin/newsletter" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/newsletter') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    Newsletter
                </a>
                <a href="/admin/settings" class="admin-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"></path></svg>
                    Settings
                </a>
                <div style="padding-top: var(--space-lg); margin-top: var(--space-lg); border-top: 1px solid var(--color-border);">
                    <a href="/" class="admin-nav-item" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        View Site
                    </a>
                    <a href="/admin/logout" class="admin-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Dashboard</h1>
            </div>
            
            <?php if ($success = flash('success')): ?>
            <div class="alert alert-success mb-xl"><?= e($success) ?></div>
            <?php endif; ?>
            
            <?php if ($error = flash('error')): ?>
            <div class="alert alert-error mb-xl"><?= e($error) ?></div>
            <?php endif; ?>
            
            <!-- Stats -->
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Today's Revenue</p>
                    <p class="admin-stat-value currency"><?= number_format($orderStats['today_revenue'], 2) ?></p>
                </div>
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Monthly Revenue</p>
                    <p class="admin-stat-value currency"><?= number_format($orderStats['month_revenue'], 2) ?></p>
                </div>
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Total Orders</p>
                    <p class="admin-stat-value"><?= $orderStats['total_orders'] ?></p>
                </div>
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Products</p>
                    <p class="admin-stat-value"><?= $productCount ?></p>
                </div>
            </div>
            
            <div class="grid grid-2" style="gap: var(--space-xl);">
                <!-- Recent Orders -->
                <div class="admin-section">
                    <div class="flex justify-between items-center mb-lg">
                        <h2 style="font-family: var(--font-heading); font-size: 1.25rem; letter-spacing: 0.05em;">Recent Orders</h2>
                        <a href="/admin/orders" class="text-gold text-sm">View All</a>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="/admin/orders/<?= $order['id'] ?>" class="text-gold"><?= e($order['order_number']) ?></a></td>
                                <td><?= e(($order['first_name'] ?? 'Guest') . ' ' . substr($order['last_name'] ?? '', 0, 1) . '.') ?></td>
                                <td><?= format_price($order['total']) ?></td>
                                <td><span class="admin-badge admin-badge-<?= $order['status'] === 'paid' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info') ?>"><?= ucfirst($order['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-gray">No orders yet</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Low Stock -->
                <div class="admin-section">
                    <div class="flex justify-between items-center mb-lg">
                        <h2 style="font-family: var(--font-heading); font-size: 1.25rem; letter-spacing: 0.05em;">Low Stock Alert</h2>
                        <a href="/admin/products" class="text-gold text-sm">View All</a>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                            <tr>
                                <td><a href="/admin/products/<?= $product['id'] ?>/edit" class="text-gold"><?= e($product['name']) ?></a></td>
                                <td style="color: <?= $product['stock'] <= 0 ? '#e74c3c' : '#f39c12' ?>"><?= $product['stock'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($lowStockProducts)): ?>
                            <tr>
                                <td colspan="2" class="text-center text-gray">All products are well stocked</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-4 mt-xl" style="gap: var(--space-lg);">
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Customers</p>
                    <p class="admin-stat-value"><?= $customerCount ?></p>
                </div>
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Pending Partners</p>
                    <p class="admin-stat-value"><?= $partnerStats['pending'] ?></p>
                </div>
                <div class="admin-stat-card">
                    <p class="admin-stat-label">Pending Sponsorships</p>
                    <p class="admin-stat-value"><?= $sponsorshipStats['pending'] ?></p>
                </div>
                <div class="admin-stat-card">
                    <p class="admin-stat-label">New Messages</p>
                    <p class="admin-stat-value"><?= $contactStats['new'] ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php
echo ob_get_clean();
