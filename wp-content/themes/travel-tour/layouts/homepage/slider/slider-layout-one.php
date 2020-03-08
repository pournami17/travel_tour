<section class="trip-category spacer">
  <div class="container">   
    <?php if( $title ) : ?><h4><?php echo esc_html( $title ); ?></h4><?php endif; ?>
    <div  id="owl-slider" class="owl-carousel"> 
      <?php foreach ( $terms as $term ) : ?>
          <div class="item">
          <div class="trip-category-list">
            <?php if( class_exists( 'Wp_Travel_Engine' ) ) : ?>
                <?php
                  $image_id = get_term_meta( $term->term_id, 'category-image-id', true);
                  $image = wp_get_attachment_image_src( $image_id, 'travel-tour-slider' );
                ?>
                <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" class="img-responsive"></a>
            <?php endif; ?>
              <div class="trip-category-caption">
                <h3><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
                <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="readmore"><?php esc_html_e( 'View Tours', 'travel-tour' ); ?></a>
              </div> 
          </div>
          </div>
      <?php endforeach; ?>
    </div> 
  </div>
</section>