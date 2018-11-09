<?php

add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
  $wp_admin_bar->add_node([
    'id'    => 'preview_themes',
    'title' => 'Themes',
    'meta'  => array( 'class' => 'preview-themes' )
  ]);
  $wp_admin_bar->add_node([
    'id'    => 'default_theme',
    'title' => 'Default Theme',
    'href'  => '#',
    'parent' => 'preview_themes',
    'meta'  => [ 'class' => 'preview-theme' ]
  ]);
  foreach (wp_get_themes() as $slug => $theme) {
    if (!$theme->name) continue;
    $wp_admin_bar->add_node([
      'id'    => "preview_themes_$slug",
      'title' => $theme->name,
      'href'  => "#$slug",
      'parent' => 'preview_themes',
      'meta'  => [ 'class' => 'preview-theme' ]
    ]);
  }

}, 999);

add_action('wp_enqueue_scripts', function() {
  wp_add_inline_script('jquery',
"
jQuery(function($) {
  $('.preview-theme').click(function(e) {
    if (e.preventDefault) e.preventDefault();
    var new_theme = $(this).find('a').attr('href').substr(1);
    if (new_theme === '') {
      document.cookie = 'alternate_theme=;path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
    else {
      document.cookie = 'alternate_theme=' + new_theme + ';path=/';
    }
    document.location.reload();
    return false;
  });
});
");

}, 999);
