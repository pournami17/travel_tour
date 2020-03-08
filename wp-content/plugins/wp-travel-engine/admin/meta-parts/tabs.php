<div id="trip-tabs">
    <span class="settings-note">
        <?php
        global $post;
        $tab_settings = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
        $bold_open = '<b>';
        $bold_close = '</b>';
        _e(sprintf('You can add, edit, delete and change order of the tabs via %1$s Trips > Settings > Trip Tab Settings %2$s.', $bold_open, $bold_close), 'wp-travel-engine');
        ?>
    </span>
    <div class="tabs">
        <?php
        global $post;
        add_filter('the_content', 'do_shortcode', 10);
        $wp_travel_engine_tab_settings = get_option('wp_travel_engine_settings');
        $default_tabs = wte_get_default_settings_tab();
        $saved_tabs = isset( $wp_travel_engine_tab_settings['trip_tabs'] ) && !empty( $wp_travel_engine_tab_settings['trip_tabs'] ) ? $wp_travel_engine_tab_settings['trip_tabs'] : $default_tabs;

        if ( $saved_tabs ) {
            $wp_travel_engine_tabs_id = $saved_tabs['id'];
            if (isset($wp_travel_engine_tabs_id)) {
                ?>
                <ul class="itinerary-accordion">
                    <?php
                    $obj = new Wp_Travel_Engine_Functions();
                    $arr = array_keys($saved_tabs['id']);
                    $count = 0;
                    foreach ($arr as $tab => $value) {
                        if (array_key_exists($value, $wp_travel_engine_tabs_id)) {
                            $val = $saved_tabs['id'][$value];
                            $tab_name = $saved_tabs['name'][$value];
                            $tab_tag = preg_replace('/-/', ' ', $val);
                            $val = $obj->wpte_clean($val);
                            if ($tab_tag != '') {
                                ?>
                                <li <?php
                                if ($count == 0) {
                                    ?>class="current"<?php }
                                ?>><a href="<?php echo '#' . $val; ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14">
                                            <g id="baseline-check_box-24px" transform="translate(-3 -3)">
                                                <path id="Path_1" data-name="Path 1" class="cls-1" d="M0,0H12V12H0Z" transform="translate(4 4)" />
                                                <path id="Path_2" data-name="Path 2" class="cls-2" d="M15.444,3H4.556A1.555,1.555,0,0,0,3,4.556V15.444A1.555,1.555,0,0,0,4.556,17H15.444A1.555,1.555,0,0,0,17,15.444V4.556A1.555,1.555,0,0,0,15.444,3Zm-7,10.889L4.556,10l1.1-1.1,2.792,2.784,5.9-5.9,1.1,1.1Z" transform="translate(0 0)" />
                                            </g>
                                        </svg><?php echo $tab_name; ?></a></li>
                                <?php
                            }
                        }

                        $count++;
                    }
                    ?>
                </ul>
                <?php
                $counter = 0;
                $tab_settings = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
                if (isset($tab_settings['tab_content'])) {
                    $tab_settings = $tab_settings['tab_content'];
                }

                if (isset($wp_travel_engine_tabs_id) && $wp_travel_engine_tabs_id != '') {
                    foreach ($arr as $tab => $value) {
                        if (isset($saved_tabs['id'][$value]) && $saved_tabs['id'][$value] != '') {
                            switch ($saved_tabs['field'][$value]) {
                                case 'wp_editor':
                                    $val = $saved_tabs['id'][$value];
                                    $val = $obj->wpte_clean($val);
                                    ?>
                                    <div id="<?php echo $val; ?>" class="tab-content" <?php
                                    if ($counter == 0) {
                                        ?> style="display: block;" <?php }
                                    ?>>
                                             <?php
                                             $value_wysiwyg = '';
                                             $value = $saved_tabs['id'][$value];
                                             if (isset($tab_settings[$val . '_wpeditor'])) {
                                                 $value_wysiwyg = $tab_settings[$val . '_wpeditor'];
                                             }

                                             $editor_id = $val . '_wpeditor';
                                             $settings = array(
                                                 'media_buttons' => true,
                                                 'textarea_name' => 'wp_travel_engine_setting[tab_content][' . $editor_id . ']'
                                             );
                                             wp_editor(html_entity_decode($value_wysiwyg), $editor_id, $settings);
                                             ?>
                                    </div>
                                    <?php
                                    break;

                                case 'itinerary':
                                    $wp_travel_engine_tabs = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
                                    $val = $saved_tabs['id'][$value];
                                    $val = $obj->wpte_clean($val);
                                    ?>
                                    <div id="<?php echo $val; ?>" class="tab-content" <?php
                                    if ($counter == 0) {
                                        ?> style="display: block;" <?php }
                                    ?>>
                                        <div class="expand-itinerary">
                                            <a href="#" class="expand-all-itinerary">
                                                <i class="fa fa-toggle-off" aria-hidden="true"></i>
                                                <?php _e('Expand/Close', 'wp-travel-engine'); ?>
                                            </a>
                                        </div>
                                        <?php
                                        do_action('wp_travel_engine_trip_itinerary_setting');
                                        ?>
                                        <div class="settings-note"><?php _e('Add Itinerary for the trip days by pressing the above button. You can write title and description for each day.', 'wp-travel-engine'); ?></div>
                                    </div>
                                    <?php
                                    break;

                                case 'cost':
                                    $wp_travel_engine_tabs = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
                                    $val = $saved_tabs['id'][$value];
                                    $val = $obj->wpte_clean($val);
                                    ?>
                                    <div id="<?php echo $val; ?>" class="tab-content" <?php
                                    if ($counter == 0) {
                                        ?> style="display: block;" <?php }
                                    ?>>
                                        <div class="cost-content">
                                            <label for="wp_travel_engine_setting[cost][includes_title]"><?php _e('Title:', 'wp-travel-engine'); ?></label>
                                            <input type="text" class="includes_title" name="wp_travel_engine_setting[cost][includes_title]" id="wp_travel_engine_setting[cost][includes_title]" value="<?php echo (isset($wp_travel_engine_tabs['cost']['includes_title']) ? esc_attr($wp_travel_engine_tabs['cost']['includes_title']) : ''); ?>">
                                                <p>
                                                    <label for="cost_includes"><?php _e('Price Includes:', 'wp-travel-engine'); ?></label>
                                                    <textarea rows="3" cols="25" name="wp_travel_engine_setting[cost][cost_includes]" id="cost_includes"><?php echo (isset($wp_travel_engine_tabs['cost']['cost_includes']) ? esc_attr($wp_travel_engine_tabs['cost']['cost_includes']) : ''); ?></textarea>
                                                    <textarea style="display: none;" rows="3" cols="25" name="wp_travel_engine_setting[cost][cost_includes_val]" id="include-result"><?php echo (isset($wp_travel_engine_tabs['cost']['cost_includes_val']) ? esc_attr($wp_travel_engine_tabs['cost']['cost_includes_val']) : ''); ?></textarea>
                                                </p>
                                        </div>
                                        <div class="cost-content">
                                            <label for="wp_travel_engine_setting[cost][excludes_title]"><?php _e('Title:', 'wp-travel-engine'); ?></label>
                                            <input type="text" class="excludes_title" name="wp_travel_engine_setting[cost][excludes_title]" id="wp_travel_engine_setting[cost][excludes_title]" value="<?php echo (isset($wp_travel_engine_tabs['cost']['excludes_title']) ? esc_attr($wp_travel_engine_tabs['cost']['excludes_title']) : ''); ?>">
                                                <p>
                                                    <label for="cost_excludes"><?php _e("Price doesn't Include:", "wp-travel-engine"); ?></label>
                                                    <textarea  rows="3" cols="25" name="wp_travel_engine_setting[cost][cost_excludes]" id="cost_excludes"><?php echo (isset($wp_travel_engine_tabs['cost']['cost_excludes']) ? esc_attr($wp_travel_engine_tabs['cost']['cost_excludes']) : ''); ?></textarea>
                                                    <textarea style="display: none;" rows="3" cols="25" name="wp_travel_engine_setting[cost][cost_excludes_val]" id="exclude-result"><?php echo (isset($wp_travel_engine_tabs['cost']['cost_excludes_val']) ? esc_attr($wp_travel_engine_tabs['cost']['cost_excludes_val']) : ''); ?></textarea>
                                                </p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'faqs':
                                    $wp_travel_engine_tabs = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
                                    $val = $saved_tabs['id'][$value];
                                    $val = $obj->wpte_clean($val);
                                    ?>
                                    <div id="<?php echo $val; ?>" class="tab-content" <?php
                                    if ($counter == 0) {
                                        ?> style="display: block;" <?php }
                                    ?>>
                                        <div class="faq-holder">
                                            <label for="wp_travel_engine_setting[faq][title]"><?php _e('Title:', 'wp-travel-engine'); ?></label>
                                            <div>
                                                <input type="text" class="title" name="wp_travel_engine_setting[faq][title]" id="wp_travel_engine_setting[faq][title]" value="<?php echo (isset($wp_travel_engine_tabs['faq']['title']) ? esc_attr($wp_travel_engine_tabs['faq']['title']) : ''); ?>">
                                            </div>
                                            <?php
                                            $wp_travel_engine_tabs = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
                                            if (isset($wp_travel_engine_tabs['faq']['faq_title']) && !empty($wp_travel_engine_tabs['faq']['faq_title'])) {
                                                echo '<ul id="faqs-list">';
                                                $maxlen = max(array_keys($wp_travel_engine_tabs['faq']['faq_title']));
                                                $arr_keys = array_keys($wp_travel_engine_tabs['faq']['faq_title']);
                                                $j = 1;
                                                foreach ($arr_keys as $key => $value) {
                                                    if (array_key_exists($value, $wp_travel_engine_tabs['faq']['faq_title'])) {
                                                        ?>
                                                        <li id="faq-tabs<?php echo esc_attr($value); ?>" data-id="<?php echo esc_attr($value); ?>" class="faq-row">
                                                            <span class="tabs-handle"><span></span></span>
                                                            <i class="dashicons dashicons-no-alt delete-faq delete-icon" data-id="<?php echo esc_attr($value); ?>"></i>
                                                            <div class="content-holder">
                                                                <a class="accordion-tabs-toggle" href="javascript:void(0);"><span class="day-count"><?php
                                                                        _e('Faq-', 'wp-travel-engine');
                                                                        echo esc_attr($j);
                                                                        ?></span></a>
                                                                <div class="faq-content">
                                                                    <div class="title">
                                                                        <input placeholder="<?php _e('Question:', 'wp-travel-engine'); ?>" type="text" class="faq-title" name="wp_travel_engine_setting[faq][faq_title][<?php echo esc_attr($value); ?>]" id="wp_travel_engine_setting[faq][faq_title][<?php echo esc_attr($value); ?>]" value="<?php echo (isset($wp_travel_engine_tabs['faq']['faq_title'][$value]) ? esc_attr($wp_travel_engine_tabs['faq']['faq_title'][$value]) : ''); ?>">
                                                                    </div>
                                                                    <div class="content">
                                                                        <textarea placeholder="<?php _e('Answer:', 'wp-travel-engine'); ?>" rows="3" cols="78" name="wp_travel_engine_setting[faq][faq_content][<?php echo esc_attr($value); ?>]" id="wp_travel_engine_setting[faq][faq_content][<?php echo esc_attr($value); ?>]"><?php echo (isset($wp_travel_engine_tabs['faq']['faq_content'][$value]) ? esc_attr($wp_travel_engine_tabs['faq']['faq_content'][$value]) : ''); ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                    $j++;
                                                }
                                                echo '</ul>';
                                            }
                                            ?>
                                            <span id="faq-holder"></span>
                                            <input type="button" class="button button-small add-faq" value="<?php _e('Add Faq', 'wp-travel-engine'); ?>">
                                                <div class="settings-note"><?php _e('Add Frequently Asked Questions for the trip by pressing the above button. You can write question and answer for each FAQs.', 'wp-travel-engine'); ?></div>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'review':
                                    $wp_travel_engine_tabs = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
                                    $val = $saved_tabs['id'][$value];
                                    $val = $obj->wpte_clean($val);
                                    ?>
                                    <div id="<?php echo $val; ?>" class="tab-content" <?php
                                    if ($counter == 0) {
                                        ?> style="display: block;" <?php }
                                    ?>>
                                        <div class="content">
                                            <label for="wp_travel_engine_setting[review][review_title]"><?php _e('Review Title:', 'wp-travel-engine'); ?></label>
                                            <input type="text" class="review_title" name="wp_travel_engine_setting[review][review_title]" id="wp_travel_engine_setting[review][review_title]" value="<?php echo (isset($wp_travel_engine_tabs['review']['review_title']) ? esc_attr($wp_travel_engine_tabs['review']['review_title']) : ''); ?>">
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'map':
                                    $val = $saved_tabs['id'][$value];
                                    $val = $obj->wpte_clean($val);
                                    ?>

                                    <div id="<?php echo $val; ?>" class="tab-content" <?php
                                    if ($counter == 0) {
                                        ?> style="display: block;" <?php }
                                    ?>>
                                         <?php
                                         $content = '[wte_trip_map id="' . $post->ID . '"]';
                                         $editor_id = 'map_wpeditor';

                                         $value_wysiwyg = isset($tab_settings[$editor_id]) ? $tab_settings[$editor_id] : '';

                                         $settings = array(
                                             'media_buttons' => true,
                                             'textarea_name' => 'wp_travel_engine_setting[tab_content][' . $editor_id . ']'
                                         );

                                         wp_editor(html_entity_decode($value_wysiwyg), $editor_id, $settings);
                                         ?>
                                        <p><?php _e(sprintf('<b>Note:</b> Goto <b>Map</b> Tab above to add Map Image or Iframe. Copy and Paste the shortcode here to display Trip Map.'), 'wp-travel-engine'); ?></p>
                                    </div>
                                    <?php
                                    break;
                            }
                        }

                        $counter++;
                    }
                }
            }
        }
        ?>
    </div>
</div>
