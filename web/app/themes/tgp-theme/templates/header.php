<header class="banner">
    <div class="timeline">
        <?php for ($year = 1900; $year <= 2020; $year += 10) { ?><span class="chunk"><?= $year ?></span><?php } ?>
    </div>

    <div class="container">
        <h1 class="logo">
            <a class="brand" href="<?= esc_url(home_url('/')); ?>">
                <img src="<?= get_stylesheet_directory_uri(); ?>/dist/images/logo.png" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>">
            </a>
        </h1>

        <nav class="nav-primary">
            <?php
            if (has_nav_menu('primary_navigation')) :
                wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
            endif;
            ?>
        </nav>
    </div>
</header>
