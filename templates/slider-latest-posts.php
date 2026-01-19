<?php

/**
 * Template: Latest Blog Posts
 *
 * Displays blog posts in grid layout on desktop (1 large + 4 small)
 * and as a Swiper slider on mobile (1 post per slide)
 *
 * @package WTE_Sliders
 * @var array $posts Array of post data
 * @var int $limit Number of posts
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (empty($posts)) {
    return;
}

// Gerar ID único para o slider
$slider_id = 'wte-latest-posts-' . uniqid();

// Split posts: first one is large, rest are small
$large_post = $posts[0];
$small_posts = array_slice($posts, 1);
?>

<div class="wte-latest-posts-wrapper">
    <div class="wte-latest-posts-swiper swiper"
        id="<?php echo esc_attr($slider_id); ?>"
        data-autoplay="false"
        data-speed="5000"
        data-arrows="true">

        <div class="swiper-wrapper">

            <!-- Slide 1: Large Card (Most Recent Post) -->
            <div class="swiper-slide wte-latest-posts-slide-large">
                <article class="wte-blog-card-large">

                    <!-- Featured Image or Placeholder -->
                    <div class="wte-blog-card-image">
                        <?php if ($large_post['featured_image']) : ?>
                            <img src="<?php echo esc_url($large_post['featured_image']); ?>"
                                alt="<?php echo esc_attr($large_post['title']); ?>">
                        <?php else : ?>
                            <div class="wte-blog-card-placeholder"></div>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="wte-blog-card-content">
                        <!-- Date Badge -->
                        <div class="wte-blog-card-date-small">
                            <?php echo esc_html($large_post['date']); ?>
                        </div>

                        <h3 class="wte-blog-card-title">
                            <a href="<?php echo esc_url($large_post['permalink']); ?>">
                                <?php echo esc_html($large_post['title']); ?>
                            </a>
                        </h3>

                        <div class="wte-blog-card-excerpt">
                            <?php echo wp_kses_post($large_post['excerpt']); ?>
                        </div>

                        <a href="<?php echo esc_url($large_post['permalink']); ?>"
                            class="wte-blog-card-button">
                            <?php esc_html_e('Leia mais', 'wte-sliders'); ?>
                        </a>
                    </div>

                </article>
            </div>

            <!-- Slides 2-5: Small Cards -->
            <?php if (!empty($small_posts)) : ?>
                <?php foreach ($small_posts as $post) : ?>
                    <div class="swiper-slide wte-latest-posts-slide-small">
                        <article class="wte-blog-card-small">

                            <div class="wte-blog-card-small-inner">
                                <!-- Featured Image or Placeholder -->
                                <div class="wte-blog-card-image-small">
                                    <?php if ($post['featured_image']) : ?>
                                        <img src="<?php echo esc_url($post['featured_image']); ?>"
                                            alt="<?php echo esc_attr($post['title']); ?>">
                                    <?php else : ?>
                                        <div class="wte-blog-card-placeholder-small"></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="wte-blog-card-content-small">
                                    <!-- Date Badge -->
                                    <div class="wte-blog-card-date-small">
                                        <?php echo esc_html($post['date']); ?>
                                    </div>

                                    <h4 class="wte-blog-card-title-small">
                                        <a href="<?php echo esc_url($post['permalink']); ?>">
                                            <?php echo esc_html($post['title']); ?>
                                        </a>
                                    </h4>

                                    <div class="wte-blog-card-excerpt-small">
                                        <?php echo wp_kses_post(wp_trim_words($post['excerpt'], 15)); ?>
                                    </div>
                                </div>
                            </div>

                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

    <!-- Navigation Arrows (mobile only - hidden on desktop via CSS) -->
    <div class="swiper-button-prev wte-latest-posts-nav"></div>
    <div class="swiper-button-next wte-latest-posts-nav"></div>
</div>
