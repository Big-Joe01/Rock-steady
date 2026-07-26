<?php
ob_start();
?>

<div class="page-hero" style="background: linear-gradient(to bottom, var(--color-dark-gray), var(--color-primary-black));">
    <div class="container">
        <h1 class="page-title"><?= e($collection['name']) ?></h1>
        <?php if (!empty($collection['description'])): ?>
        <p class="page-subtitle"><?= e($collection['description']) ?></p>
        <?php endif; ?>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($products)): ?>
        <div class="text-center py-section">
            <p class="text-gray text-lg">No products in this collection yet.</p>
            <a href="/collections" class="btn btn-primary mt-lg">View All Collections</a>
        </div>
        <?php else: ?>
        
        <div class="products-grid grid-3">
            <?php foreach ($products as $product): ?>
            <article class="product-card" data-aos="fade-up">
                <div class="product-card-image">
                    <img src="<?= $product['image_url'] ?? 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600' ?>" 
                         alt="<?= e($product['name']) ?>" 
                         loading="lazy">
                    
                    <div class="product-card-badges">
                        <?php if (is_new_arrival($product['created_at'])): ?>
                        <span class="product-badge product-badge-new">New</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-card-actions">
                        <button class="product-action-btn wishlist-btn" data-product-id="<?= $product['id'] ?>">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                            </svg>
                        </button>
                        <button class="product-action-btn quick-add-btn" data-product-id="<?= $product['id'] ?>">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="product-card-content">
                    <h3 class="product-card-title">
                        <a href="/product/<?= $product['slug'] ?>"><?= e($product['name']) ?></a>
                    </h3>
                    <div class="product-card-price">
                        <span class="price-current"><?= format_price($product['sale_price'] ?? $product['price']) ?></span>
                        <?php if ($product['sale_price']): ?>
                        <span class="price-original"><?= format_price($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
