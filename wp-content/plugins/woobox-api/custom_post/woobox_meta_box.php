<?php
add_filter( 'rwmb_meta_boxes', 'woobox_meta_boxes' );
function woobox_meta_boxes( $meta_boxes ) {	

	
	// Testimonial Member Details In Class
	$meta_boxes[] = array(
		'title'			=> esc_html__( 'Testimonial Member Details','woobox' ),
		'post_types'	=> 'testimonial',
		'fields'		=> array(
					
			array(
				'id'		=> 'woobox_testimonial_designation',
				'name'		=> esc_html__( 'Designation :','woobox' ),				
				'type'		=> 'text'				
			),
			array(
				'id'		=> 'woobox_testimonial_company',
				'name'		=> esc_html__( 'Company :','woobox' ),				
				'type'		=> 'text'				
			),
		),
	);
	
	return $meta_boxes;
}
?>