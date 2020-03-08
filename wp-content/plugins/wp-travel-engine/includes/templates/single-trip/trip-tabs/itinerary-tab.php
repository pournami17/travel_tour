<?php
global $post;
$wp_travel_engine_tabs = get_post_meta($post->ID, 'wp_travel_engine_setting', true);
?>
		<div class="post-data itinerary">
			<?php
			$maxlen = max(array_keys($wp_travel_engine_tabs['itinerary']['itinerary_title']));
			$arr_keys = array_keys($wp_travel_engine_tabs['itinerary']['itinerary_title']);
			foreach ($arr_keys as $key => $value) {
				if (array_key_exists($value, $wp_travel_engine_tabs['itinerary']['itinerary_title'])) {
					?>
					<div class="itinerary-row">
						<div class="title">
							<?php
							_e('Day ', 'wp-travel-engine');
							echo esc_attr($value);
							?>
						</div>
						<div class="itinerary-content">
							<div class="title">
								<?php
								echo (isset($wp_travel_engine_tabs['itinerary']['itinerary_title'][$value]) ? esc_attr($wp_travel_engine_tabs['itinerary']['itinerary_title'][$value]) : '');
								?>
							</div>
							<div class="content">
								<p>
									<?php
									if (isset($wp_travel_engine_tabs['itinerary']['itinerary_content_inner'][$value]) && $wp_travel_engine_tabs['itinerary']['itinerary_content_inner'][$value] != '') {
										$content_itinerary = $wp_travel_engine_tabs['itinerary']['itinerary_content_inner'][$value];
									} else {
										$content_itinerary = $wp_travel_engine_tabs['itinerary']['itinerary_content'][$value];
									}
									echo apply_filters('the_content', html_entity_decode($content_itinerary, 3, 'UTF-8'));
									?>
								</p>
							</div>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>

