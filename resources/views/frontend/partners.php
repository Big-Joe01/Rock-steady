<?php
ob_start();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Our Partners</h1>
        <p class="page-subtitle">Collaborating with the best</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="text-center" style="max-width: 700px; margin: 0 auto var(--space-3xl);">
            <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray);">
                We're proud to collaborate with a diverse range of partners who share our passion for music, 
                art, and authentic self-expression. Together, we're building something bigger than fashion.
            </p>
        </div>
        
        <?php if (!empty($partners)): ?>
        <div class="grid grid-3" style="gap: var(--space-xl);">
            <?php foreach ($partners as $partner): ?>
            <div class="p-xl" style="background: var(--color-dark-gray); border-radius: var(--radius-md); text-align: center;">
                <div style="height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-lg);">
                    <span style="font-family: var(--font-heading); font-size: 1.5rem; letter-spacing: 0.1em;">
                        <?= e($partner['brand_name'] ?? $partner['company_name']) ?>
                    </span>
                </div>
                <?php if ($partner['website']): ?>
                <a href="<?= e($partner['website']) ?>" target="_blank" class="text-gold text-sm">
                    Visit Website
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-section">
            <h2 class="section-title mb-lg">Want to Partner With Us?</h2>
            <p class="text-gray mb-2xl" style="max-width: 500px; margin: 0 auto var(--space-xl);">
                We are always looking for new opportunities to collaborate with brands and organizations 
                that align with our values.
            </p>
            <button class="btn btn-primary btn-lg" data-modal="partner-modal">Become a Partner</button>
        </div>
    </div>
</section>

<!-- Partner Modal -->
<div class="modal" data-modal="partner-modal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Become a Partner</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <?php if ($success = flash('success')): ?>
            <div class="alert alert-success mb-lg"><?= e($success) ?></div>
            <?php endif; ?>
            
            <form action="/partner/submit" method="POST">
                <div class="form-group">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="company_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Brand Name</label>
                    <input type="text" name="brand_name" class="form-input">
                </div>
                <div class="grid grid-2" style="gap: var(--space-md);">
                    <div class="form-group">
                        <label class="form-label">Industry</label>
                        <input type="text" name="industry" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-input" placeholder="https://">
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-textarea" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-full">Submit Application</button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
