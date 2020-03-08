<div id="trip-map">
  <div class="settings-note"><?php _e('Note: Either you can upload an image as a trip map or add iframe codes for the location. By default, image will be given first priority.','wp-travel-engine');?></div>
  <?php
  global $post;
  $wp_travel_engine_setting = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
  $src[0]='';
  if ( isset( $wp_travel_engine_setting['map']['image_url'] ) && $wp_travel_engine_setting['map']['image_url']!='' )
  {
    $src = wp_get_attachment_image_src( $wp_travel_engine_setting['map']['image_url'],'full' );
  }
  ?>
  <div class="map-img-upload">
      <label for="image_url"><?php _e('Map Image: ','wp-travel-engine');?></label>
      <input type="hidden" name="wp_travel_engine_setting[map][image_url]" id="image_url" class="regular-text" value="<?php echo isset($wp_travel_engine_setting['map']['image_url']) ? esc_attr($wp_travel_engine_setting['map']['image_url']): ''; ?>">
      <div class="preview">
        <img src="<?php echo ( isset( $wp_travel_engine_setting['map']['image_url'] ) && $wp_travel_engine_setting['map']['image_url']!='' ) ? $src[0] :''; ?>">
      </div>
      <input type="button" <?php
      if ( isset( $wp_travel_engine_setting['map']['image_url'] ) && $wp_travel_engine_setting['map']['image_url']!='' )
      { echo 'style="display:none;"'; }?> name="upload-btn" id="upload-btn1" class="button-secondary" value="Upload Image">
      <input type="button" <?php
      if ( !isset( $wp_travel_engine_setting['map']['image_url'] ) || $wp_travel_engine_setting['map']['image_url']=='' )
      { echo 'style="display:none;"'; }?> name="remove-btn" id="remove-btn" class="button-secondary" value="Remove Image">
  </div>
  <div class="map-iframe">
      <label for="wp_travel_engine_setting[map][iframe]"><?php _e('Map Iframe code: ','wp-travel-engine');?></label>
      <textarea name="wp_travel_engine_setting[map][iframe]" id="wp_travel_engine_setting[map][iframe]"><?php echo isset($wp_travel_engine_setting['map']['iframe']) ? $wp_travel_engine_setting['map']['iframe']:'' ?></textarea>
  </div>
  <?php
  $page_shortcode = '<b>[wte_trip_map id='."'".$post->ID."'".']</b>';
  $template_shortcode = "<b>&lt;?php echo do_shortcode('[wte_trip_map id=".$post->ID."]'); ?&gt;</b>";
  _e( sprintf('<br><b>Note:</b> You can use this shortcode %1$s to display Trip Map of this trip in posts/pages/tabs/widgets or use this snippet %2$s to display Trip Map in templates.',$page_shortcode, $template_shortcode),'wp-travel-engine');
  ?> 
</div>