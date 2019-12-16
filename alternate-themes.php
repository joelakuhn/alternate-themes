<?php
/*
Plugin Name: Alternate Themes
Description: Allows pages to use an alternate theme from the site theme and enables hot swapping preview of themes on the front end.
Version: 1.0.3
License: GPL2
Author: Joel Kuhn
*/

namespace AlternateThemes;

add_action('init','\AlternateThemes\set_up_metabox');
function set_up_metabox(){
  if (is_super_admin() || current_user_can('edit_posts')) {
    require 'metabox.php';
  }
}

function is_home_url() {
  $url = get_option('home', false);
  if ($url === false) return false;

  $url_pieces = explode('/', substr($url, 8));
  if (isset($url_pieces[1])) {
    $path = trim($url_pieces[1], '/');
  }
  else {
    $path = '';
  }
  return $path === trim($_SERVER['REQUEST_URI'], '/');
}

function get_page_id() {
  $page_id = false;



  if (is_admin()) {
    if (preg_match('|post\.php$|', $_SERVER['SCRIPT_NAME'])) {
      // Edit page
      if (isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
        $page_id = $_GET['post'];
      }
      // Update post page
      else if (isset($_POST['post_ID'])) {
        $page_id = $_POST['post_ID'];
      }
    }
  }
  // Preview page
  else if (isset($_GET['preview_id'])) {
    $page_id = $_GET['preview_id'];
  }
  // Home page
  else if (is_home_url()) {
    $page_id = get_option('page_on_front', false);
    error_log("front page: " . $page_id);
  }
  // Front-end page
  else {
    $page_path = substr($_SERVER['REQUEST_URI'], strlen(get_blog_details()->path));
    $page_path = explode('?', $page_path)[0];
    $page = get_page_by_path($page_path, OBJECT, ['post', 'page']);
    if ($page) {
      if ($page->post_status == 'publish' || is_user_logged_in()) {
        $page_id = $page->ID;
      }
    }
  }
  return $page_id;
}

function set_theme($selected_theme) {

  if (defined("ALTERNATE_THEME")) {
    return ALTERNATE_THEME;
  }
  else if (isset($_COOKIE['alternate_theme'])) {
    $selected_theme = $_COOKIE['alternate_theme'];
  }
  else if ($page_id = get_page_id()) {
    $theme = get_post_meta($page_id, 'alternate_theme', true);
    if ($theme) {
      $selected_theme = $theme;
    }
  }

  define("ALTERNATE_THEME", $selected_theme);
  return $selected_theme;
}

add_filter('pre_option_stylesheet', 'AlternateThemes\set_theme');
add_filter('pre_option_template', 'AlternateThemes\set_theme');

add_action('init', function() {
  if (is_user_logged_in()) {
    include 'admin-bar.php';
  }
});
