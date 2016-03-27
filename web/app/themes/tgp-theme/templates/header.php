<header class="banner">
    <div class="timeline">
        <div class="timeline-bg">
            <?php for ($year = 1820; $year <= 2120; $year += 10) { ?><span class="chunk"><?= $year ?></span><?php } ?>
        </div>
    </div>

    <div class="container">
        <a class="brand" href="<?= esc_url(home_url('/')); ?>">
            <h1 class="logo">
                <img src="<?= get_stylesheet_directory_uri(); ?>/dist/images/logo.png" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>">
            </h1>
        </a>

        <nav class="nav-primary">
            <?php
            if (has_nav_menu('primary_navigation')) :
                wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
            endif;
            ?>
        </nav>
    </div>
</header>
