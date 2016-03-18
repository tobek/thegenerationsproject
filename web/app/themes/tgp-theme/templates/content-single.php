<?php
    $two_col = false;

?>

<?php while (have_posts()) : the_post(); ?>
    <article <?php post_class(); ?>>
        <header class="page-header container">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php get_template_part('templates/entry-meta'); ?>
        </header>

        <div class="container">
            <div class="row">
                <div class="<?= $two_col ? 'col-ms-12 col-sm-7' : 'col-sm-9 col-lg-7' ?> post-content-wrapper">
                    <div class="post-content">
                        <div class="post-content-inner">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php // comments_template('/templates/comments.php'); ?>
    </article>
<?php endwhile; ?>
