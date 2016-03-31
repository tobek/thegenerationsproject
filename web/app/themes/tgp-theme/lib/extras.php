<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  if (is_page() && get_post_thumbnail_id()) {
    $classes[] = 'has-feat-img';
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }
  
  $classes[] = 'env-'. WP_ENV;

  $classes[] = (is_user_logged_in() ? '' : 'not-') . 'logged-in';

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');


/**
 * Miscellaneous routing
 */
add_action( 'template_redirect', __NAMESPACE__ . '\\tgp_rewrite' );
function tgp_rewrite() {
  global $wp_query;

  // print_r($wp_query->query_vars);

  $slug = $wp_query->query_vars['name'];
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  if (strpos($path, '/api/') === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($slug === 'donate-submit') {
      donate_handler();
      exit;
    }
  }
}

function braintree_init() {
  \Braintree_Configuration::environment(BRAINTREE_ENV);
  \Braintree_Configuration::merchantId(BRAINTREE_MERCHANT_ID);
  \Braintree_Configuration::publicKey(BRAINTREE_PUBLIC_KEY);
  \Braintree_Configuration::privateKey(BRAINTREE_PRIVATE_KEY);
}

function braintree_token() {
  braintree_init();
  return \Braintree_ClientToken::generate();
}

function donate_handler() {
  header('Content-Type: text/plain');

  $fields = [
    'full_name' => ['type' => 'string', 'required' => true, 'name' => 'Full Name'],
    'email' => ['type' => 'string', 'required' => true, 'name' => 'Email'],
    'donation_amount' => ['type' => 'float', 'required' => false, 'name' => 'Donation Amount'],
    'donate_to' => ['type' => 'string', 'required' => false, 'name' => 'Donate To'],
    'note' => ['type' => 'string', 'required' => false, 'name' => 'Note'],
    'nonce' => ['type' => 'string', 'required' => true, 'name' => 'nonce'],
  ];

  $data = [];

  foreach ($fields as $field => &$field_info) {
    $field_info['value'] = null;

    if (isset($_POST[$field])) {
      if ($field_info['type'] === 'string') {
        $field_info['value'] = sanitize_text_field($_POST[$field]);
      }
      else if ($field_info['type'] === 'float') {
        $field_info['value'] = floatval($_POST[$field]);
      }
    }

    if ($field_info['required'] && ! $field_info['value']) {
      status_header(400);
      echo "Invalid request, missing field '$field'";
      return;
    }
  }

  braintree_init();

  $result = \Braintree_Transaction::sale([
    'amount' => $fields['donation_amount']['value'],
    'paymentMethodNonce' => $fields['nonce']['value'],
    'options' => [
      'submitForSettlement' => True
    ]
  ]);

  if ($result->success) {
    status_header(200);
    echo 'success!';
    error_log('Successfully submitted Braintree transaction. Result object: ' . print_r($result, true));
  }
  else {
    status_header(500);
    $err_msg = 'Failed to submit Braintree transaction. Result object: ' . print_r($result, true) . '. Errors: ' . print_r($result->errors->deepAll(), true);
    echo $err_msg;
    error_log($err_msg);
  }

  unset($fields['nonce']);
  donation_submit_email($result->success, $fields, $result);
}

function donation_submit_email($success, $fields, $result) {
  $subject = $success ? 'Someone has donated to TGP!' : 'Someone attempted to donate to TGP but it didn\'t work =(';

  $body = '<p>Here\'s the info about their donation' . ($success ? '' : ' attempt') . ':</p>';

  $body .= '<p>';
  foreach ($fields as $field => $field_info) {
    if (! $field_info['value']) continue;

    $body .= "<b>$field_info[name]</b>: ";
    if ($field === 'donation_amount') $body .= '$';
    $body .= "$field_info[value]<br>";
  }
  $body .= '</p>';
  
  $body .= '<hr>';

  if ($success) {
    $body .= '<p>Here\'s all the info we got back from Braintree in case that\'s useful (but the important part is that the donation was successful, and you should be able to see it from your Braintree account):</p>';
  }
  else {
    $body .= '<p>Here\'s all the info we got back from Braintree. This might be useful if you want to get in touch with Braintree support about it, or maybe this info makes it clear what happened:</p>';
  }

  $body .= '<pre>' . print_r($result, true) . '</pre>';

  wp_mail(
    'tobyfox@gmail.com',
    $subject,
    $body,
    ['Content-type: text/html']
  );


  if ($success) {
    $subject = 'Thank you for your donation!';

    $body = '<p style="font-size: large">Thank you so much for your contribution to The Generations Project. What we are doing is very important for our communities and we cannot do it without the continued support of people like you. We encourage you to spread our message! Thanks again!</p>';

    $body .= '<hr>';

    $body .= '<p>';
    $body .= 'Date: ' . date('F j, Y');
    $body .= '<br>Donor: ' . $fields['full_name']['value'];
    $body .= '<br>Donation Amount: $' . $fields['donation_amount']['value'];
    $body .= '</p>';

    $body .= '<p>The Generations Project is fiscally sponsored by Social and Environmental Entrepreneurs, a 501(c)3 nonprofit organization. All contributions $100 and over are tax deductible to the fullest extent allowed by law.</p>';

    $body .= '<p>--</p><p style="font-size: small; color: #888">The Generations Project<br><a href="http://thegenerationsproject.info">thegenerationsproject.info</a><br>New York</p>';

    wp_mail(
      $fields['email']['value'],
      $subject,
      $body,
      ['Content-type: text/html']
    );
  }
}
