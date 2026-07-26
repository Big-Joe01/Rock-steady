<?php
ob_start();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Collections</h1>
        <p class="page-subtitle">Explore our curated collections</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid grid-3" style="gap: var(--space-xl);">
            <?php foreach ($collections as $collection): ?>
            <a href="/collection/<?= $collection['slug'] ?>" class="collection-card">
                <img src="<?= $collection['cover_image'] ?? 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=800' ?>" 
                     alt="<?= e($collection['name']) ?>" 
                     loading="lazy"
                     style="width: 100%; height: 100%; object-fit: cover;">
                <div class="collection-card-overlay">
                    <h3 class="collection-card-title"><?= e($collection['name']) ?></h3>
                    <p class="collection-card-count"><?= $collection['product_count'] ?? '' ?> Products</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
