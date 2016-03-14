<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup() {
  // Enable features from Soil when plugin is activated
  // https://roots.io/plugins/soil/
  add_theme_support('soil-clean-up');
  add_theme_support('soil-nav-walker');
  add_theme_support('soil-nice-search');
  add_theme_support('soil-jquery-cdn');
  add_theme_support('soil-relative-urls');

  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('sage', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'sage')
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Enable post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Register sidebars
 */
function widgets_init() {
  register_sidebar([
    'name'          => __('Primary', 'sage'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);

  register_sidebar([
    'name'          => __('Footer', 'sage'),
    'id'            => 'sidebar-footer',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
}
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');

/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar() {
  return false;
  // static $display;

  // isset($display) || $display = !in_array(true, [
  //   // The sidebar will NOT be displayed if ANY of the following return true.
  //   // @link https://codex.wordpress.org/Conditional_Tags
  //   is_404(),
  //   is_front_page(),
  //   is_page_template('template-custom.php'),
  // ]);

  // return apply_filters('sage/display_sidebar', $display);
}

/**
 * Theme assets
 */
function assets() {
  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);


// Allow editor role to edit theme options - only need to run this once as it gets saved in the DB
// $role_object = get_role('editor');
// $role_object->add_cap('edit_theme_options');

add_action('admin_menu',  __NAMESPACE__ . '\\hide_menus_for_editors');
function hide_menus_for_editors() {
    if (is_user_logged_in()) {
        global $current_user;
        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        if ($user_role === 'editor') {
            // To allow editor to edit menu we had to allow them to edit theme, but this one is dangerous so let's remove:
            remove_submenu_page('themes.php', 'themes.php');

            // While we're at it, de-clutter some more:
            remove_menu_page('tools.php');
            remove_menu_page('edit-comments.php');
        }
    }
}

add_filter( 'upload_size_limit', __NAMESPACE__ . '\\tgp_increase_upload' );
function tgp_increase_upload($bytes) {
    return 16777216; // 16 megabytes
    // @NOTE also have to set `upload_max_filesize` and `post_max_size` to `16M` and probably good idea to up `max_execution_time` (maybe to 60) but that doesn't seem to be working with ini_set to add to php.ini
}

