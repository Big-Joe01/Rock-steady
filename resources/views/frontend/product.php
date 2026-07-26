<?php
ob_start();
?>

<section class="product-detail">
    <div class="container">
        <nav class="product-breadcrumb">
            <a href="/">Home</a> / 
            <a href="/shop">Shop</a> / 
            <a href="/shop?category=<?= $product['category_slug'] ?? '' ?>"><?= e($product['category_name'] ?? '') ?></a> / 
            <?= e($product['name']) ?>
        </nav>
        
        <div class="product-detail-grid">
            <div class="product-gallery">
                <div class="product-gallery-main">
                    <?php $mainImage = !empty($images) ? $images[0]['url'] : 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1200'; ?>
                    <img src="<?= $mainImage ?>" alt="<?= e($product['name']) ?>" id="mainImage">
                </div>
                
                <?php if (count($images) > 1): ?>
                <div class="product-gallery-thumbs">
                    <?php foreach ($images as $index => $image): ?>
                    <div class="product-gallery-thumb <?= $index === 0 ? 'active' : '' ?>" data-src="<?= $image['url'] ?>">
                        <img src="<?= str_replace('?w=1200', '?w=200', $image['url']) ?>" alt="Thumbnail <?= $index + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <?php if ($product['category_name']): ?>
                <p class="product-breadcrumb" style="margin-bottom: var(--space-sm);"><?= e($product['category_name']) ?></p>
                <?php endif; ?>
                
                <h1 class="product-title"><?= e($product['name']) ?></h1>
                
                <div class="product-price-wrapper">
                    <span class="product-price"><?= format_price($product['sale_price'] ?? $product['price']) ?></span>
                    <?php if ($product['sale_price']): ?>
                    <span class="product-price-original"><?= format_price($product['price']) ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($reviewStats['total'] > 0): ?>
                <div class="product-rating">
                    <div class="product-rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="<?= $i <= round($reviewStats['average']) ? '#C9A227' : '#333' ?>">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <span class="product-rating-count">(<?= $reviewStats['total'] ?> reviews)</span>
                </div>
                <?php endif; ?>
                
                <div class="product-description">
                    <?= nl2br(e($product['description'] ?? '')) ?>
                </div>
                
                <form class="product-options" action="/cart/add" method="POST" id="addToCartForm">
                    <?php if (!empty($groupedVariants['sizes'])): ?>
                    <div class="product-option">
                        <label class="product-option-label">
                            Size <?php if (!empty($selectedSize)): ?><span><?= e($selectedSize) ?></span><?php endif; ?>
                        </label>
                        <div class="product-sizes">
                            <?php foreach (array_unique(array_column($variants, 'size')) as $size): ?>
                            <label class="product-size <?= ($selectedSize ?? '') === $size ? 'active' : '' ?>">
                                <input type="radio" name="size" value="<?= e($size) ?>" <?= ($selectedSize ?? '') === $size ? 'checked' : '' ?>>
                                <?= e($size) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($groupedVariants['colors'])): ?>
                    <div class="product-option">
                        <label class="product-option-label">
                            Color <?php if (!empty($selectedColor)): ?><span><?= e($selectedColor) ?></span><?php endif; ?>
                        </label>
                        <div class="product-colors">
                            <?php foreach (array_unique(array_column($variants, 'color'), SORT_REGULAR) as $color): ?>
                            <?php 
                            $colorCode = '#000';
                            foreach ($variants as $v) {
                                if ($v['color'] === $color && !empty($v['color_code'])) {
                                    $colorCode = $v['color_code'];
                                    break;
                                }
                            }
                            ?>
                            <label class="product-color <?= ($selectedColor ?? '') === $color ? 'active' : '' ?>" 
                                   style="background-color: <?= e($colorCode) ?>">
                                <input type="radio" name="color" value="<?= e($color) ?>" <?= ($selectedColor ?? '') === $color ? 'checked' : '' ?>>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-actions">
                        <div class="product-quantity">
                            <button type="button" onclick="updateQuantity(-1)">−</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="10">
                            <button type="button" onclick="updateQuantity(1)">+</button>
                        </div>
                        
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn btn-primary product-add-to-cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                            Add to Cart
                        </button>
                        
                        <button type="button" class="product-wishlist-btn wishlist-btn <?= ($product['is_wishlisted'] ?? false) ? 'active' : '' ?>" 
                                data-product-id="<?= $product['id'] ?>">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="<?= ($product['is_wishlisted'] ?? false) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                
                <div class="product-meta">
                    <div class="product-meta-item">
                        <span class="product-meta-label">SKU:</span>
                        <span><?= e($product['sku']) ?></span>
                    </div>
                    <?php if ($product['category_name']): ?>
                    <div class="product-meta-item">
                        <span class="product-meta-label">Category:</span>
                        <span><?= e($product['category_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="product-meta-item">
                        <span class="product-meta-label">Stock:</span>
                        <span style="color: <?= $product['stock'] > 10 ? '#27ae60' : ($product['stock'] > 0 ? '#f39c12' : '#e74c3c') ?>">
                            <?= $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock' ?>
                        </span>
                    </div>
                </div>
                
                <div class="flex gap-md mt-xl">
                    <button class="btn btn-secondary" onclick="shareProduct()">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                        </svg>
                        Share
                    </button>
                </div>
            </div>
        </div>
        
        <?php if (!empty($related)): ?>
        <section class="section-sm">
            <h2 class="section-title text-center mb-2xl">You May Also Like</h2>
            <div class="products-grid grid-4">
                <?php foreach ($related as $item): ?>
                <article class="product-card">
                    <div class="product-card-image">
                        <img src="<?= $item['image_url'] ?? 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600' ?>" 
                             alt="<?= e($item['name']) ?>" loading="lazy">
                    </div>
                    <div class="product-card-content">
                        <h3 class="product-card-title">
                            <a href="/product/<?= $item['slug'] ?>"><?= e($item['name']) ?></a>
                        </h3>
                        <div class="product-card-price">
                            <span class="price-current"><?= format_price($item['price']) ?></span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($reviews)): ?>
        <section class="reviews-section" id="reviews">
            <div class="reviews-header">
                <div>
                    <h2 class="section-title mb-md">Customer Reviews</h2>
                    <?php if ($reviewStats['total'] > 0): ?>
                    <div class="flex items-center gap-md">
                        <span class="text-gold text-xl"><?= number_format($reviewStats['average'], 1) ?></span>
                        <div>
                            <div class="product-rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="<?= $i <= round($reviewStats['average']) ? '#C9A227' : '#333' ?>">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <?php endfor; ?>
                            </div>
                            <span class="text-gray text-sm">Based on <?= $reviewStats['total'] ?> reviews</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-xl">
                <?php foreach (array_slice($reviews, 0, 5) as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-author">
                            <div class="review-author-avatar">
                                <?= strtoupper(substr($review['first_name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div>
                                <p class="review-author-name"><?= e(($review['first_name'] ?? 'Anonymous') . ' ' . substr($review['last_name'] ?? '', 0, 1) . '.') ?></p>
                                <p class="review-author-date"><?= date('M d, Y', strtotime($review['created_at'])) ?></p>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="<?= $i <= $review['rating'] ? '#C9A227' : '#333' ?>">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php if ($review['title']): ?>
                    <h4 class="review-title"><?= e($review['title']) ?></h4>
                    <?php endif; ?>
                    <?php if ($review['comment']): ?>
                    <p class="review-content"><?= e($review['comment']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</section>

<script>
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) + change;
    if (value < 1) value = 1;
    if (value > 10) value = 10;
    input.value = value;
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?= e($product['name']) ?>',
            text: 'Check out this product from ROCK STEADY',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}

document.querySelectorAll('.product-gallery-thumb').forEach(thumb => {
    thumb.addEventListener('click', () => {
        document.getElementById('mainImage').src = thumb.dataset.src;
        document.querySelectorAll('.product-gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    });
});

document.querySelectorAll('.product-size input').forEach(input => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.product-size').forEach(s => s.classList.remove('active'));
        input.closest('.product-size').classList.add('active');
    });
});

document.querySelectorAll('.product-color input').forEach(input => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.product-color').forEach(c => c.classList.remove('active'));
        input.closest('.product-color').classList.add('active');
    });
});
</script>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
