<?php
ob_start();
?>

<div class="page-hero" style="padding: calc(80px + var(--space-xl)) 0 var(--space-xl);">
    <div class="container">
        <h1 class="page-title">Checkout</h1>
        <p class="page-subtitle">Complete your order</p>
    </div>
</div>

<section class="section py-0">
    <div class="container">
        <?php if ($error = flash('error')): ?>
        <div class="alert alert-error mb-xl"><?= e($error) ?></div>
        <?php endif; ?>
        
        <div class="checkout-grid">
            <div>
                <form action="/checkout/initiate" method="POST" id="checkoutForm">
                    <div class="checkout-section">
                        <h3 class="checkout-section-title">Contact Information</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" required 
                                   value="<?= auth()['email'] ?? old('email') ?? '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-input" 
                                   value="<?= old('phone') ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="checkout-section">
                        <h3 class="checkout-section-title">Shipping Address</h3>
                        
                        <div class="grid grid-2" style="gap: var(--space-md);">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-input" required
                                       value="<?= auth()['first_name'] ?? old('first_name') ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-input" required
                                       value="<?= auth()['last_name'] ?? old('last_name') ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-input" required
                                   value="<?= old('address') ?? '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Apartment, suite, etc. (optional)</label>
                            <input type="text" name="address_line2" class="form-input"
                                   value="<?= old('address_line2') ?? '' ?>">
                        </div>
                        
                        <div class="grid grid-2" style="gap: var(--space-md);">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-input" required
                                       value="<?= old('city') ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">State / Province</label>
                                <input type="text" name="state" class="form-input"
                                       value="<?= old('state') ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="grid grid-2" style="gap: var(--space-md);">
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" class="form-input" required
                                       value="<?= old('postal_code') ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Country</label>
                                <select name="country" class="form-select" required>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                    <option value="IT">Italy</option>
                                    <option value="ES">Spain</option>
                                    <option value="NL">Netherlands</option>
                                    <option value="BE">Belgium</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-full" id="submitBtn">
                        Continue to Payment
                    </button>
                </form>
            </div>
            
            <div class="checkout-summary">
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Order Summary</h3>
                    
                    <div class="checkout-items">
                        <?php foreach ($cart as $item): ?>
                        <div class="checkout-item">
                            <div class="checkout-item-image">
                                <img src="<?= $item['image_url'] ?? 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=200' ?>" 
                                     alt="<?= e($item['name']) ?>">
                                <span class="badge" style="position: absolute; top: -8px; right: -8px; background: var(--color-gold); color: var(--color-primary-black);">
                                    <?= $item['quantity'] ?>
                                </span>
                            </div>
                            <div class="checkout-item-info">
                                <p class="checkout-item-title"><?= e($item['name']) ?></p>
                                <?php if ($item['size'] || $item['color']): ?>
                                <p class="checkout-item-variant">
                                    <?= e(implode(' / ', array_filter([$item['size'] ?? '', $item['color'] ?? '']))) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="checkout-item-price">
                                <?= format_price($item['price'] * $item['quantity']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary" style="border-top: 1px solid var(--color-border); padding-top: var(--space-lg);">
                        <div class="cart-summary-row">
                            <span>Subtotal</span>
                            <span><?= format_price($subtotal) ?></span>
                        </div>
                        
                        <?php if ($discount > 0): ?>
                        <div class="cart-summary-row" style="color: #27ae60;">
                            <span>Discount</span>
                            <span>-<?= format_price($discount) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="cart-summary-row">
                            <span>Shipping</span>
                            <span><?= $shipping == 0 ? 'FREE' : format_price($shipping) ?></span>
                        </div>
                        
                        <?php if ($tax > 0): ?>
                        <div class="cart-summary-row">
                            <span>Tax</span>
                            <span><?= format_price($tax) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="cart-summary-row total">
                            <span>Total</span>
                            <span><?= format_price($total) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="loading"></span> Processing...';
});
</script>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
