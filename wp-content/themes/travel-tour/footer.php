<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package travel-tour
 */

?>
	<footer class="main">
		<div class="container">
		<?php dynamic_sidebar( 'footer-1' ); ?>
	</div>
	</footer>
		<div class="scroll-top-wrapper"> <span class="scroll-top-inner"><i class="fa fa-2x fa-angle-up"></i></span></div> 
		
		<?php wp_footer(); ?>
	</body>
</html>