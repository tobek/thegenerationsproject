<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;
use TGP\Utils;

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

add_post_type_support('page', 'excerpt');

add_filter( 'upload_size_limit', __NAMESPACE__ . '\\tgp_increase_upload' );
function tgp_increase_upload($bytes) {
    return 16777216; // 16 megabytes
    // @NOTE also have to set `upload_max_filesize` and `post_max_size` to `16M` and probably good idea to up `max_execution_time` (maybe to 60) but that doesn't seem to be working with ini_set to add to php.ini
}

add_action('init', __NAMESPACE__ . '\\create_people');
function create_people() {
  register_taxonomy('tgp_role', 'tgp_person', [
    'labels' => [
      'name' => 'Roles',
      'singular_name' => 'Role'
    ],
    'public' => true
  ]);

  register_post_type('tgp_person', [
    'labels' => [
      'name' => 'People',
      'singular_name' => 'Person',
      'add_new_item' => 'Add New Person',
      'new_item' => 'Add Person',
      'edit_item' => 'Edit Person',
      'view_item' => 'View Person',
      'all_items' => 'All People',
    ],
    'public' => true,
    'has_archive' => true,
    'rewrite' => array('slug' => 'people'),
    'menu_position' => 21,
    'menu_icon' => 'dashicons-universal-access',
    'supports' => [
      'title',
      'editor',
      'thumbnail',
      'custom-fields',
      'page-attributes',
    ],
    'taxonomies' => ['tgp_role']
  ]);
}

add_filter('manage_tgp_person_posts_columns', __NAMESPACE__ . '\\change_tgp_person_columns');
function change_tgp_person_columns($cols)  {
  $cols['role'] = 'Role';
  return $cols;
}

add_action('manage_posts_custom_column', __NAMESPACE__ . '\\tgp_person_custom_columns', 10, 2 );
function tgp_person_custom_columns($column, $post_id) {
  if ($column === 'role') {
    $terms = get_the_terms($post_id, 'tgp_role');
    if (isset($terms[0])) {
      echo $terms[0]->slug;
    }
  }
}

add_filter('manage_edit-tgp_person_sortable_columns', __NAMESPACE__ . '\\tgp_person_sortable_columns');
function tgp_person_sortable_columns() {
  return array(
    'title' => 'title',
    'date' => 'date',
    'role' => 'role',
  );
}


add_action('init', __NAMESPACE__ . '\\create_partners');
function create_partners() {
  register_post_type('tgp_partner', [
    'labels' => [
      'name' => 'Partners',
      'singular_name' => 'Partner',
      'add_new_item' => 'Add New Partner',
      'new_item' => 'Add Partner',
      'edit_item' => 'Edit Partner',
      'view_item' => 'View Partner',
      'all_items' => 'All Partners',
    ],
    'public' => true,
    'has_archive' => false,
    'rewrite' => array('slug' => 'partners'),
    'menu_position' => 22,
    'menu_icon' => 'dashicons-awards',
    'supports' => [
      'title',
      'thumbnail',
      'page-attributes',
    ],
  ]);
}


add_action('admin_init', __NAMESPACE__ . '\\tgp_post_event_date' );
function tgp_post_event_date() {
    add_meta_box('tgp_event_date_meta', 'Event Date', __NAMESPACE__ . '\\tgp_event_date_meta', 'post', 'side');
}
 
function tgp_event_date_meta () {
  global $post;
  $date = get_post_meta($post->ID, 'event_date', true);
   
  $date_input_value = '';
  if ($date) {
    $date_input_value = date('M d, Y', $date);
    // $time_input_value = date($time_format, $time);
  }
   
  ?>
    <style>
      .tgp-event-date > ul > li { height: 20px; clear:both; margin: 0 0 15px 0;}
      .tgp-event-date > ul > li label { width: 100px; display:block; float:left; padding-top:4px; }
      .tgp-event-date > ul > li input { width:125px; display:block; float:left; }
      .tgp-event-date > ul > li em { width: 200px; display:block; float:left; color:gray; margin-left:10px; padding-top: 4px}
    </style>

    <div class="tgp-event-date bootstrap-iso">
      <input type="hidden" name="tgp-event-date-nonce" id="tgp-event-date-nonce" value="<?= wp_create_nonce('tgp-event-date-nonce') ?>" />
      <ul>
          <li><label>Date</label><input name="event_date" class="tgp-date" value="<?= $date_input_value; ?>" /></li>
          <!-- <li><label>Time</label><input name="event_time" value="<?php // echo $time_input_value; ?>" /><em>Use 24h format (7pm = 19:00)</em></li> -->
      </ul>
    </div>

    <script>
      (function($) {
        $('.tgp-date').datetimepicker({
          format: 'MMM D, YYYY'
        });
      })(jQuery);
    </script>
  <?php
}

add_action ('save_post', __NAMESPACE__ . '\\save_tgp_post_event_date');
function save_tgp_post_event_date() {
  global $post;
   
  if (! wp_verify_nonce($_POST['tgp-event-date-nonce'], 'tgp-event-date-nonce')) {
    return $post->ID;
  }
   
  if (! current_user_can('edit_post', $post->ID)) {
    return $post->ID;
  }
   
  if (isset($_POST['event_date']) && $_POST['event_date']) {
    // $new_date = strtotime($_POST['event_date'] . $_POST['event_time']);
    $new_date = strtotime($_POST['event_date']);
    update_post_meta($post->ID, 'event_date', $new_date);
  }
  else {
    delete_post_meta($post->ID, 'event_date');
  }
}

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\include_datetimepicker');
function include_datetimepicker($hook) {
  if ($hook !== 'post.php' && $hook !== 'post-new.php') {
    return;
  }

  wp_register_style('bootstrap_css', get_template_directory_uri() . '/assets/styles/bootstrap-iso.css', null, '3.1.0');
  wp_enqueue_style('bootstrap_css');
  wp_register_style('eonasdan-bootstrap-datetimepicker_css', get_template_directory_uri() . '/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', null, '4.17.37');
  wp_enqueue_style('eonasdan-bootstrap-datetimepicker_css');

  wp_enqueue_script('moment_js', get_template_directory_uri() . '/bower_components/moment/min/moment.min.js', null, '2.12.0', false);
  wp_enqueue_script('eonasdan-bootstrap-datetimepicker_js', get_template_directory_uri() . '/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', ['jquery'], '4.17.37', false);
}


add_action('admin_head', __NAMESPACE__ . '\\admin_css');
function admin_css() { ?>
  <style>
    .post-php.post-type-tgp_partner h1:after,
    .post-new-php.post-type-tgp_partner h1:after {
      content: '(enter partner link as the title)';
      display: block;
      margin-top: 5px;
      font-size: 80%;
      font-style: italic;
    }
  </style>
<?php }

// Ensure that except and custom fields meta boxes always visible
add_action('admin_init', __NAMESPACE__ . '\\unhide_meta_boxes');
function unhide_meta_boxes() {
  $dirty = false;
  $page_hidden = get_usermeta(get_current_user_id(), 'metaboxhidden_page');

  $index = array_search('postexcerpt', $page_hidden);
  if ($index !== false) {
    unset($page_hidden[$index]);
    $dirty = true;
  }

  $index = array_search('postcustom', $page_hidden);
  if ($index !== false) {
    unset($page_hidden[$index]);
    $dirty = true;
  }

  if ($dirty) {
    $page_hidden = array_values($page_hidden);
    update_user_meta(get_current_user_id(), 'metaboxhidden_page', $page_hidden);
  }
}
