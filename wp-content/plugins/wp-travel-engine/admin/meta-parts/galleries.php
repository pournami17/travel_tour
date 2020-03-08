<?php
global $post;
$ids = get_post_meta($post->ID, 'wpte_gallery_id', true);
?>
<div id="trip-gallery">
  <table class='form-table'>
    <tr><td>
      <div class="enable-gallery">
        <label for='wpte_gallery_id[enable]'><?php _e('Enable Gallery','wp-travel-engine'); ?></label>
      <input type='checkbox' name='wpte_gallery_id[enable]' id='wpte_gallery_id[enable]' <?php $j = isset( $ids['enable'] ) ? esc_attr( $ids['enable'] ): '0';?> value='1' <?php checked( $j, true ); ?>/>
      <label for='wpte_gallery_id[enable]' class="checkbox-label"></label>
    </div>
      <div class="settings-note"><?php _e('Check this to enable gallery instead of featured image in single trip.','wp-travel-engine');?></div>
    </td></tr>
    <tr><td>
      <a class='feat-img-gallery-add button' href='#' data-uploader-title='Add image(s) to gallery' data-uploader-button-text='Add image(s)'><span><?php _e('Add image(s)','wp-travel-engine');?></span></a>
      <ul id='feat-img-gallery-metabox-list'>
      <?php if ($ids) : unset($ids['enable']); foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>
        <li>
          <input type='hidden' name='wpte_gallery_id[<?php echo $key; ?>]' value='<?php echo $value; ?>'>
          <img class='image-preview' src='<?php echo $image[0]; ?>'>
          <a class='change-image button button-small' href='#' data-uploader-title='Change image' data-uploader-button-text='Change image'>Change image</a><br>
          <small><a class='remove-image' href='#'><?php _e('Remove image','wp-travel-engine');?></a></small>
        </li>
      <?php endforeach; endif; ?>
      </ul>
    </td></tr>
  </table>
</div>