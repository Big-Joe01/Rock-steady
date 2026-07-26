<?php
ob_start();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        <p class="page-subtitle">Review your items</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($cart)): ?>
        <div class="text-center py-section">
            <svg viewBox="0 0 24 24" width="80" height="80" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto var(--space-xl); opacity: 0.3;">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 01-8 0"></path>
            </svg>
            <h2 class="text-xl mb-md">Your cart is empty</h2>
            <p class="text-gray mb-2xl">Looks like you haven't added anything yet.</p>
            <a href="/shop" class="btn btn-primary">Start Shopping</a>
        </div>
        <?php else: ?>
        
        <div class="grid" style="grid-template-columns: 1fr 400px; gap: var(--space-3xl);">
            <div>
                <div class="flex justify-between items-center mb-xl">
                    <h2 class="text-lg"><?= count($cart) ?> item(s)</h2>
                    <a href="/cart/clear" class="text-gray text-sm" onclick="return confirm('Clear all items?')">Clear Cart</a>
                </div>
                
                <?php foreach ($cart as $item): ?>
                <div class="cart-item" style="padding: var(--space-lg) 0; border-bottom: 1px solid var(--color-border);">
                    <div class="grid" style="grid-template-columns: 120px 1fr; gap: var(--space-lg);">
                        <div class="cart-item-image" style="width: 120px; height: 150px; border-radius: var(--radius-sm); overflow: hidden;">
                            <img src="<?= $item['image_url'] ?? 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400' ?>" 
                                 alt="<?= e($item['name']) ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        
                        <div>
                            <h3 class="product-card-title" style="margin-bottom: var(--space-sm);">
                                <a href="/product/<?= \App\Models\Product::getById($item['product_id'])['slug'] ?? '#' ?>">
                                    <?= e($item['name']) ?>
                                </a>
                            </h3>
                            <?php if ($item['size'] || $item['color']): ?>
                            <p class="cart-item-variant" style="font-size: 0.875rem; color: var(--color-text-gray); margin-bottom: var(--space-sm);">
                                <?= e(implode(' / ', array_filter([$item['size'] ?? '', $item['color'] ?? '']))) ?>
                            </p>
                            <?php endif; ?>
                            <p class="cart-item-price" style="font-size: 1.125rem; color: var(--color-gold); margin-bottom: var(--space-md);">
                                <?= format_price($item['price']) ?>
                            </p>
                            
                            <div class="flex gap-lg items-center">
                                <form action="/cart/update" method="POST" class="flex items-center gap-md">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?? '' ?>">
                                    <input type="hidden" name="size" value="<?= $item['size'] ?? '' ?>">
                                    <input type="hidden" name="color" value="<?= $item['color'] ?? '' ?>">
                                    <div class="quantity-control">
                                        <button type="submit" name="action" value="decrease" class="quantity-btn">−</button>
                                        <span class="quantity-value"><?= $item['quantity'] ?></span>
                                        <button type="submit" name="action" value="increase" class="quantity-btn">+</button>
                                    </div>
                                </form>
                                
                                <form action="/cart/remove" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?? '' ?>">
                                    <button type="submit" class="cart-item-remove" style="background: none; border: none; color: var(--color-text-gray); cursor: pointer;">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-lg font-bold"><?= format_price($item['price'] * $item['quantity']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="mt-xl">
                    <a href="/shop" class="btn btn-secondary">Continue Shopping</a>
                </div>
            </div>
            
            <div>
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Order Summary</h3>
                    
                    <div class="checkout-items">
                        <div class="checkout-summary-row flex justify-between mb-md">
                            <span>Subtotal</span>
                            <span><?= format_price($subtotal) ?></span>
                        </div>
                        
                        <?php if ($discount > 0): ?>
                        <div class="checkout-summary-row flex justify-between mb-md" style="color: #27ae60;">
                            <span>Discount (-)</span>
                            <span>-<?= format_price($discount) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="checkout-summary-row flex justify-between mb-md">
                            <span>Shipping</span>
                            <span><?= $shipping == 0 ? 'FREE' : format_price($shipping) ?></span>
                        </div>
                        
                        <div class="checkout-summary-row flex justify-between mb-md" style="border-top: 1px solid var(--color-border); padding-top: var(--space-md); margin-top: var(--space-md);">
                            <span class="text-lg font-bold">Total</span>
                            <span class="text-lg font-bold" style="color: var(--color-gold);"><?= format_price($total) ?></span>
                        </div>
                    </div>
                    
                    <?php if ($coupon): ?>
                    <div class="flex justify-between items-center p-md" style="background: rgba(39, 174, 96, 0.1); border-radius: var(--radius-sm); margin-bottom: var(--space-lg);">
                        <div>
                            <span class="text-sm">Coupon: </span>
                            <span class="font-bold"><?= e($coupon['code']) ?></span>
                        </div>
                        <form action="/cart/remove-coupon" method="POST">
                            <button type="submit" class="text-sm text-gray">&times;</button>
                        </form>
                    </div>
                    <?php else: ?>
                    <form action="/cart/apply-coupon" method="POST" class="mb-lg">
                        <div class="flex gap-sm">
                            <input type="text" name="coupon_code" class="form-input" placeholder="Coupon code" style="flex: 1;">
                            <button type="submit" class="btn btn-secondary btn-sm">Apply</button>
                        </div>
                    </form>
                    <?php endif; ?>
                    
                    <a href="/checkout" class="btn btn-primary w-full">Proceed to Checkout</a>
                    
                    <p class="text-center text-gray text-sm mt-lg">
                        Taxes and shipping calculated at checkout
                    </p>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
