<?php
ob_start();
?>

<div class="about-hero">
    <div class="container">
        <h1 class="page-title">About Us</h1>
        <p class="page-subtitle"><?= SITE_TAGLINE ?></p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="about-story">
            <div>
                <h2 class="section-title mb-lg">Our Story</h2>
                <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray); margin-bottom: var(--space-xl);">
                    Born from the rebellious spirit of rock and roll, ROCK STEADY was founded with a simple mission: 
                    to create clothing that empowers individuals to express their unique identity without compromise.
                </p>
                <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray); margin-bottom: var(--space-xl);">
                    What started as a small collection of band-inspired t-shirts has evolved into a comprehensive 
                    streetwear brand that blends the raw energy of rock culture with contemporary design aesthetics.
                </p>
                <p style="font-family: var(--font-accent); font-size: 1.125rem; line-height: 1.8; color: var(--color-text-gray);">
                    Today, ROCK STEADY is proud to serve a global community of artists, musicians, athletes, and 
                    free thinkers who share our belief that style should be as unique as the individual wearing it.
                </p>
            </div>
            <div class="about-story-image">
                <img src="https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=800" alt="ROCK STEADY Brand Story">
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--color-dark-gray);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our Values</h2>
            <div class="section-divider"></div>
        </div>
        
        <div class="grid grid-3">
            <div class="text-center p-xl">
                <div class="text-gold text-4xl mb-lg">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <h3 class="text-xl mb-md" style="font-family: var(--font-heading); letter-spacing: 0.1em;">Quality First</h3>
                <p class="text-gray">Premium materials and craftsmanship in every piece we create.</p>
            </div>
            <div class="text-center p-xl">
                <div class="text-gold text-4xl mb-lg">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                    </svg>
                </div>
                <h3 class="text-xl mb-md" style="font-family: var(--font-heading); letter-spacing: 0.1em;">Stay Authentic</h3>
                <p class="text-gray">We stay true to our rock roots and never compromise our vision.</p>
            </div>
            <div class="text-center p-xl">
                <div class="text-gold text-4xl mb-lg">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 010 7.75"></path>
                    </svg>
                </div>
                <h3 class="text-xl mb-md" style="font-family: var(--font-heading); letter-spacing: 0.1em;">Community</h3>
                <p class="text-gray">Building a global community of artists and free thinkers.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our Timeline</h2>
            <div class="section-divider"></div>
        </div>
        
        <div class="timeline" style="max-width: 600px; margin: 0 auto;">
            <div class="timeline-item">
                <span class="timeline-year">2014</span>
                <h4 class="timeline-title">The Beginning</h4>
                <p class="timeline-text">ROCK STEADY is born from a garage in Los Angeles with a simple mission.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-year">2016</span>
                <h4 class="timeline-title">First Collection</h4>
                <p class="timeline-text">Launch of our debut collection featuring iconic rock-inspired designs.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-year">2018</span>
                <h4 class="timeline-title">Going Global</h4>
                <p class="timeline-text">Expansion to international markets and first flagship store opens.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-year">2020</span>
                <h4 class="timeline-title">Digital Transformation</h4>
                <p class="timeline-text">Launch of our e-commerce platform and partnership program.</p>
            </div>
            <div class="timeline-item">
                <span class="timeline-year">2024</span>
                <h4 class="timeline-title">The Future</h4>
                <p class="timeline-text">Continuing to push boundaries with innovative designs and sustainable practices.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: linear-gradient(135deg, var(--color-dark-gray) 0%, var(--color-primary-black) 100%);">
    <div class="container text-center">
        <h2 class="section-title mb-lg">Join the Movement</h2>
        <p class="text-gray mb-2xl" style="max-width: 500px; margin: 0 auto var(--space-xl);">
            Be part of our global community of rock enthusiasts and streetwear lovers.
        </p>
        <a href="/shop" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
</section>

<?php
$content = ob_get_clean();
require RESOURCES_PATH . '/views/layouts/main.php';
