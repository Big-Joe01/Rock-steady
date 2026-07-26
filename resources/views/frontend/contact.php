<?php
ob_start();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">We'd love to hear from you</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2 class="section-title mb-xl">Get in Touch</h2>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h4 class="contact-info-title">Email</h4>
                        <p class="contact-info-text"><?= e($contactEmail ?? 'rocksteady@gmail.com') ?></p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="contact-info-title">Phone</h4>
                        <p class="contact-info-text"><?= e($contactPhone ?? '+1 (555) 123-4567') ?></p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h4 class="contact-info-title">Address</h4>
                        <p class="contact-info-text"><?= nl2br(e($address ?? '123 Rock Street, Los Angeles, CA 90001')) ?></p>
                    </div>
                </div>
                
                <div class="flex gap-md mt-xl">
                    <?php if (!empty($social['facebook'])): ?>
                    <a href="<?= e($social['facebook']) ?>" target="_blank" class="btn btn-icon btn-secondary">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($social['instagram'])): ?>
                    <a href="<?= e($social['instagram']) ?>" target="_blank" class="btn btn-icon btn-secondary">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="contact-form">
                <h2 class="section-title mb-xl">Send a Message</h2>
                
                <?php if ($success = flash('success')): ?>
                <div class="alert alert-success mb-lg"><?= e($success) ?></div>
                <?php endif; ?>
                
                <?php if ($error = flash('error')): ?>
                <div class="alert alert-error mb-lg"><?= e($error) ?></div>
                <?php endif; ?>
                
                <form action="/contact" method="POST">
                    <div class="form-group">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-input" required value="<?= old('name') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" required value="<?= old('email') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-input" value="<?= old('phone') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-input" value="<?= old('subject') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message *</label>
                        <textarea name="message" class="form-textarea" rows="5" required><?= old('message') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
