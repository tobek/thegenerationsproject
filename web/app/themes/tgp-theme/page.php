<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  
  <?php
    $template_path = locate_template("templates/page-{$post->post_name}.php");
    if (file_exists($template_path)) {
      include $template_path;
    }
    else {
      get_template_part('templates/content', 'page');
    }
  ?>
<?php endwhile; ?>
