<?php
ob_start();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Shop</h1>
        <p class="page-subtitle">Premium rock-inspired streetwear</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="shop-filters">
            <div class="flex gap-md flex-wrap">
                <a href="/shop" class="shop-filter-btn <?= empty($filters['category']) ? 'active' : '' ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                <a href="/shop?category=<?= $cat['slug'] ?>" 
                   class="shop-filter-btn <?= ($filters['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>">
                    <?= e($cat['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div class="shop-sort">
                <span class="shop-sort-label">Sort by:</span>
                <select onchange="window.location.href=this.value">
                    <option value="/shop?<?= http_build_query(array_merge($filters, ['sort' => 'newest', 'category' => $filters['category'] ?? ''])) ?>" 
                            <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="/shop?<?= http_build_query(array_merge($filters, ['sort' => 'price_asc', 'category' => $filters['category'] ?? ''])) ?>" 
                            <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="/shop?<?= http_build_query(array_merge($filters, ['sort' => 'price_desc', 'category' => $filters['category'] ?? ''])) ?>" 
                            <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="/shop?<?= http_build_query(array_merge($filters, ['sort' => 'popular', 'category' => $filters['category'] ?? ''])) ?>" 
                            <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Popular</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($products['items'])): ?>
        <div class="text-center py-section">
            <p class="text-gray text-lg">No products found</p>
            <a href="/shop" class="btn btn-primary mt-lg">View All Products</a>
        </div>
        <?php else: ?>
        
        <div class="products-grid grid-4 mb-section">
            <?php foreach ($products['items'] as $product): ?>
            <article class="product-card" data-aos="fade-up">
                <div class="product-card-image">
                    <?php 
                    $imageUrl = !empty($product['image_url']) 
                        ? $product['image_url'] 
                        : 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600';
                    ?>
                    <img src="<?= $imageUrl ?>" 
                         alt="<?= e($product['name']) ?>" 
                         loading="lazy">
                    
                    <div class="product-card-badges">
                        <?php if (is_new_arrival($product['created_at'])): ?>
                        <span class="product-badge product-badge-new">New</span>
                        <?php endif; ?>
                        <?php if ($product['sale_price']): ?>
                        <span class="product-badge product-badge-sale">Sale</span>
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
                    <p class="product-card-category"><?= e($product['category_name'] ?? '') ?></p>
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
        
        <?php if ($products['total_pages'] > 1): ?>
        <div class="flex justify-center">
            <nav class="pagination">
                <?php if ($products['has_prev']): ?>
                <a href="/shop?page=<?= $products['prev_page'] ?>&<?= http_build_query(array_filter($filters)) ?>" 
                   class="pagination__link">&laquo;</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $products['total_pages']; $i++): ?>
                <a href="/shop?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>" 
                   class="pagination__link <?= $i === $products['current_page'] ? 'pagination__link--active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($products['has_next']): ?>
                <a href="/shop?page=<?= $products['next_page'] ?>&<?= http_build_query(array_filter($filters)) ?>" 
                   class="pagination__link">&raquo;</a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
