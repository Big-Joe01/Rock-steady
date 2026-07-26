<?php
ob_start();
?>

<section class="section min-h-screen flex items-center justify-center" style="background: var(--color-primary-black);">
    <div class="container text-center">
        <h1 style="font-family: var(--font-heading); font-size: clamp(8rem, 20vw, 15rem); line-height: 1; color: var(--color-gold); margin-bottom: var(--space-lg);">404</h1>
        <h2 class="section-title mb-md">Page Not Found</h2>
        <p class="text-gray text-lg mb-2xl" style="max-width: 500px; margin: 0 auto var(--space-2xl);">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <div class="flex gap-md justify-center">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <a href="/shop" class="btn btn-secondary">Shop Now</a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
