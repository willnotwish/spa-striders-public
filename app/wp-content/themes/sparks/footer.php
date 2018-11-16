	<?php zilla_footer_before(); ?>
	<!-- BEGIN #footer -->
	<div id="footer">
		
		<!-- BEGIN .block -->
		<div class="block clearfix">
	    
	    <?php zilla_footer_start(); ?>
	    
	    	<?php get_sidebar( 'footer' ); ?>
	    	
			<!-- BEGIN .footer-lower -->	    
		    <div class="footer-lower">
		    
				<p class="copyright">&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>.</p>
			
				<p class="credit">Website by <a href="http://edadams.io" target="_new">Ed Adams</a></p>
 
			<!-- END .footer-lower -->
			</div>
		
	    <?php zilla_footer_end(); ?>
	    <!--END .block -->
	    </div>
	    
	<!-- END #footer -->
	</div>
	<?php zilla_footer_after(); ?>
			
	<!-- Theme Hook -->
	<?php wp_footer(); ?>
	<?php zilla_body_end(); ?>
<!--END body-->
</body>
<!--END html-->
</html>