<?php

namespace AlternateThemes;

add_action('add_meta_boxes', '\AlternateThemes\alternate_theme_add_meta_box');


function alternate_theme_add_meta_box() {
  add_meta_box(
    'alternate_theme-alternate-theme',
    __( 'Alternate Theme', 'alternate_theme' ),
    '\AlternateThemes\alternate_theme_html',
    ['page', 'post'],
    'side',
    'core'
  );
}


function alternate_theme_html( $post) {
  $alternate_theme = get_post_meta($post->ID, 'alternate_theme', true);

  wp_nonce_field( '_alternate_theme_nonce', 'alternate_theme_nonce' );
  ?>
  <p>
    <label for="alternate_theme"><?php _e( 'Theme', 'alternate_theme' ); ?></label><br>
    <select name="alternate_theme" id="alternate_theme">
      <option value="">Default</option>
      <?php foreach (wp_get_themes() as $slug => $theme): ?>
        <option value="<?= $slug ?>"<?= $alternate_theme == $slug ? ' selected' : '' ?>>
          <?= $theme->Name ? $theme->Name : $slug ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p><?php
}


function alternate_theme_save( $post_id ) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (! isset($_POST['alternate_theme_nonce']) || ! wp_verify_nonce($_POST['alternate_theme_nonce'], '_alternate_theme_nonce')) return;
  if (! current_user_can('edit_post', $post_id)) return;

  if (isset( $_POST['alternate_theme'])) {
    if ($_POST['alternate_theme'] === '') {
      delete_post_meta($post_id, 'alternate_theme');
    }
    else {
      update_post_meta($post_id, 'alternate_theme', esc_attr($_POST['alternate_theme']));
    }
  }
}
add_action( 'save_post', '\AlternateThemes\alternate_theme_save' );
