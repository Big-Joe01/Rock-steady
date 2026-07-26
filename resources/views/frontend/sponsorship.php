<?php
ob_start();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Sponsorship</h1>
        <p class="page-subtitle">Partner with ROCK STEADY</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid grid-2" style="gap: var(--space-4xl); align-items: start;">
            <div>
                <h2 class="section-title mb-lg">Join Our Family</h2>
                <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray); margin-bottom: var(--space-xl);">
                    At ROCK STEADY, we believe in the power of collaboration. We sponsor athletes, artists, musicians, 
                    and creators who embody our brand values of authenticity, creativity, and excellence.
                </p>
                <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray); margin-bottom: var(--space-xl);">
                    Whether you're a professional athlete, an emerging artist, a content creator, or organizing an event, 
                    we want to hear from you. Together, we can create something extraordinary.
                </p>
                
                <h3 class="text-xl mb-md" style="font-family: var(--font-heading); letter-spacing: 0.1em;">What We Look For</h3>
                <ul style="list-style: disc; padding-left: var(--space-xl); color: var(--color-text-gray);">
                    <li style="margin-bottom: var(--space-sm);">Authentic connection to rock/music culture</li>
                    <li style="margin-bottom: var(--space-sm);">Strong social media presence</li>
                    <li style="margin-bottom: var(--space-sm);">Creative content that aligns with our brand</li>
                    <li style="margin-bottom: var(--space-sm);">Professional attitude and reliability</li>
                    <li style="margin-bottom: var(--space-sm);">Active engagement with your community</li>
                </ul>
            </div>
            
            <div class="contact-form">
                <h2 class="section-title mb-xl">Apply for Sponsorship</h2>
                
                <?php if ($success = flash('success')): ?>
                <div class="alert alert-success mb-lg"><?= e($success) ?></div>
                <?php endif; ?>
                
                <?php if ($error = flash('error')): ?>
                <div class="alert alert-error mb-lg"><?= e($error) ?></div>
                <?php endif; ?>
                
                <form action="/sponsorship/submit" method="POST">
                    <div class="grid grid-2" style="gap: var(--space-md);">
                        <div class="form-group">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-input" required value="<?= old('name') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Brand/Team Name</label>
                            <input type="text" name="brand" class="form-input" value="<?= old('brand') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" required value="<?= old('email') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Social Media Followers</label>
                        <select name="followers" class="form-select">
                            <option value="">Select range</option>
                            <option value="1k-10k">1,000 - 10,000</option>
                            <option value="10k-50k">10,000 - 50,000</option>
                            <option value="50k-100k">50,000 - 100,000</option>
                            <option value="100k-500k">100,000 - 500,000</option>
                            <option value="500k+">500,000+</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Platforms</label>
                        <div class="flex flex-wrap gap-md" style="gap: var(--space-sm);">
                            <label class="form-checkbox">
                                <input type="checkbox" name="platforms[]" value="instagram"> Instagram
                            </label>
                            <label class="form-checkbox">
                                <input type="checkbox" name="platforms[]" value="tiktok"> TikTok
                            </label>
                            <label class="form-checkbox">
                                <input type="checkbox" name="platforms[]" value="youtube"> YouTube
                            </label>
                            <label class="form-checkbox">
                                <input type="checkbox" name="platforms[]" value="twitter"> Twitter
                            </label>
                            <label class="form-checkbox">
                                <input type="checkbox" name="platforms[]" value="other"> Other
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Budget Expectations</label>
                        <select name="budget" class="form-select">
                            <option value="">Select range</option>
                            <option value="product">Product Only</option>
                            <option value="500-1000">$500 - $1,000</option>
                            <option value="1000-5000">$1,000 - $5,000</option>
                            <option value="5000-10000">$5,000 - $10,000</option>
                            <option value="10000+">$10,000+</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Portfolio/Previous Work</label>
                        <input type="url" name="portfolio" class="form-input" placeholder="Link to portfolio or social profiles">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tell Us About Yourself *</label>
                        <textarea name="proposal" class="form-textarea" rows="5" required placeholder="Tell us why you'd be a great fit for ROCK STEADY..."><?= old('proposal') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">Submit Application</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
