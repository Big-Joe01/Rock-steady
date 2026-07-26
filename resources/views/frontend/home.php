<?php
ob_start();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-video">
        <video autoplay muted loop playsinline>
            <source src="<?= $heroVideo ?? 'https://assets.mixkit.co/videos/preview/mixkit-man-running-through-a-dark-alley-1279-large.mp4' ?>" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
    </div>
    
    <div class="hero-content">
        <h1 class="hero-logo">ROCK STEADY</h1>
        <p class="hero-tagline"><?= SITE_TAGLINE ?></p>
        <div class="hero-actions">
            <a href="/shop" class="btn btn-primary btn-lg">Shop Now</a>
            <a href="/collections" class="btn btn-secondary btn-lg">Explore Collection</a>
        </div>
    </div>
    
    <div class="scroll-indicator">
        <span>Scroll</span>
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M19 12l-7 7-7-7"/>
        </svg>
    </div>
</section>

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Handpicked pieces for the bold</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <article class="product-card" data-aos="fade-up">
                <div class="product-card-image">
                    <img src="<?= $product['image_url'] ?? 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600' ?>" 
                         alt="<?= e($product['name']) ?>" 
                         loading="lazy">
                    
                    <div class="product-card-badges">
                        <?php if ($product['is_new']): ?>
                        <span class="product-badge product-badge-new">New</span>
                        <?php endif; ?>
                        <?php if ($product['sale_price']): ?>
                        <span class="product-badge product-badge-sale">Sale</span>
                        <?php endif; ?>
                        <?php if ($product['trending']): ?>
                        <span class="product-badge product-badge-trending">Trending</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-card-actions">
                        <button class="product-action-btn wishlist-btn" data-product-id="<?= $product['id'] ?>" aria-label="Add to wishlist">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                            </svg>
                        </button>
                        <button class="product-action-btn quick-add-btn" data-product-id="<?= $product['id'] ?>" aria-label="Quick add">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="product-card-content">
                    <p class="product-card-category"><?= e($product['category_name'] ?? 'Uncategorized') ?></p>
                    <h3 class="product-card-title">
                        <a href="/product/<?= $product['slug'] ?>"><?= e($product['name']) ?></a>
                    </h3>
                    <div class="product-card-price">
                        <span class="price-current"><?= format_price($product['sale_price'] ?? $product['price']) ?></span>
                        <?php if ($product['sale_price']): ?>
                        <span class="price-original"><?= format_price($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-card-quick-add">
                        <button class="btn btn-primary btn-sm w-full add-to-cart-btn" data-product-id="<?= $product['id'] ?>">Add to Cart</button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-2xl">
            <a href="/shop" class="btn btn-outline">View All Products</a>
        </div>
    </div>
</section>

<!-- Collections -->
<?php if (!empty($collections)): ?>
<section class="section py-2xl" style="background: var(--color-dark-gray);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Collections</h2>
            <div class="section-divider"></div>
            <p class="section-subtitle">Explore our curated collections</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach (array_slice($collections, 0, 3) as $collection): ?>
            <a href="/collection/<?= $collection['slug'] ?>" class="collection-card">
                <img src="<?= $collection['cover_image'] ?? 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=800' ?>" 
                     alt="<?= e($collection['name']) ?>" 
                     loading="lazy">
                <div class="collection-card-overlay">
                    <h3 class="collection-card-title"><?= e($collection['name']) ?></h3>
                    <p class="collection-card-count">Shop Now</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Brand Story -->
<section class="section">
    <div class="container">
        <div class="grid grid-2 items-center" style="gap: var(--space-4xl);">
            <div data-aos="fade-right">
                <h2 class="section-title mb-lg">Rock Without Limits</h2>
                <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray); margin-bottom: var(--space-xl);">
                    Born from the rebellious spirit of rock and roll, ROCK STEADY represents those who dare to stand out. 
                    Our premium streetwear blends the raw energy of rock culture with modern design.
                </p>
                <a href="/about" class="btn btn-secondary">Our Story</a>
            </div>
            <div data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?w=800" 
                     alt="ROCK STEADY Brand" 
                     style="border-radius: var(--radius-md);">
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="section" style="background: linear-gradient(135deg, var(--color-dark-gray) 0%, var(--color-primary-black) 100%);">
    <div class="container">
        <div class="text-center" style="max-width: 600px; margin: 0 auto;">
            <h2 class="section-title mb-md">Join the Movement</h2>
            <p class="text-gray mb-2xl" style="font-family: var(--font-accent);">
                Subscribe for exclusive drops, early access, and rocksteady updates.
            </p>
            <form class="footer-newsletter-form" style="max-width: 500px; margin: 0 auto;" action="/subscribe" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required style="flex: 1;">
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
