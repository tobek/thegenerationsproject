<footer class="content-info container">
    <div class="row">
        <div class="links col-ms-6 col-sm-6">
            <a href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            <?php
            if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
            endif;
            ?>
        </div>

        <div class="info col-ms-6 col-sm-6">
            <div>[social icons]</div>
            <div>The Generations Project</div>
            <div>www.thegenerationsproject.info</div>
            <div>New York, San Francisco</div>
        </div>
    </div>
</footer>
