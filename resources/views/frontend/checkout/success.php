<?php
ob_start();
?>

<section class="section min-h-screen flex items-center" style="background: var(--color-primary-black);">
    <div class="container">
        <div class="text-center" style="max-width: 600px; margin: 0 auto;">
            <div class="text-gold mb-xl">
                <svg viewBox="0 0 24 24" width="80" height="80" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto;">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            
            <h1 class="section-title mb-md">Order Confirmed!</h1>
            <p class="text-gray text-lg mb-2xl">
                Thank you for your purchase. We've sent a confirmation email with your order details.
            </p>
            
            <?php if (!empty($order)): ?>
            <div class="checkout-section mb-2xl" style="text-align: left;">
                <h3 class="checkout-section-title">Order Details</h3>
                <div class="grid grid-2" style="gap: var(--space-md);">
                    <div>
                        <p class="text-gray text-sm">Order Number</p>
                        <p class="font-bold"><?= e($order['order_number']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray text-sm">Total</p>
                        <p class="font-bold text-gold"><?= format_price($order['total']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray text-sm">Email</p>
                        <p><?= e($order['email']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray text-sm">Status</p>
                        <span class="admin-badge admin-badge-success"><?= ucfirst($order['status']) ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="flex gap-md justify-center">
                <a href="/shop" class="btn btn-primary">Continue Shopping</a>
                <?php if (!empty($order)): ?>
                <a href="/user/order/<?= $order['order_number'] ?>" class="btn btn-secondary">View Order</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
