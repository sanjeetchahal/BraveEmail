<?php

function filter_paints_json( $response, $post, $context ) {
    $imageUrlArr = array(
		"original" => "",
		"thumbnail" => ""
	);

	if(isset($response->data['paint_media_id']) && $response->data['paint_media_id']!=""){
        $media_id = $response->data['paint_media_id']; // Replace with the actual media ID
		$media_meta = wp_get_attachment_metadata( $media_id );

		if(isset($media_meta['file'])){
			$imageUrlArr['original'] = wp_get_attachment_url( $media_id );
		}
		
		$sizes = $media_meta['sizes'];


		if(count($sizes)){
			if(isset($sizes['thumbnail']))
			{
				$imageSrc =  getImageUrl($media_id,'thumbnail');
				$imageUrlArr['thumbnail'] = $imageSrc['src'];
			}
		}
    }
    
	$response->data['postImages'] = $imageUrlArr;

	return $response;
}

// function filter_paints_json( $response, $post, $context ) {
//     $tempImage = array(
//         "src" => "",
//         "width" => "",
//         "height" => ""
//     );

//     if(isset($response->data['paint_media_id']) && $response->data['paint_media_id']!=""){
//         $thumbimg = wp_get_attachment_image_src( $response->data['paint_media_id'] );
    
    
//         if(isset($thumbimg[0])){
//             $tempImage['src'] = $thumbimg[0];
//             $tempImage['width'] = $thumbimg[1];
//             $tempImage['height'] = $thumbimg[2];
//         } 
//     }
    
    
//     $response->data['postImages'] = $tempImage;

// 	return $response;
// }
add_filter( 'rest_prepare_paints', 'filter_paints_json', 10, 3 );

add_filter( 'rest_paints_query', 'rest_api_paints_filter_add_filter_param', 10, 2 );


/**
 * Add the filter parameter
 *
 * @param  array           $args    The query arguments.
 * @param  WP_REST_Request $request Full details about the request.
 * @return array $args.
 **/
function rest_api_paints_filter_add_filter_param( $args, $request ) {
   

	if ( empty( $request['filter'] )  ) {
	 	// return $args;
	}

	
	$filter = json_decode($request['filter'],true);
   
	if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100 ) ) {
		$args['posts_per_page'] = $filter['posts_per_page'];
	}

	/* Show user specific quotes  */
	// $args['author'] = get_current_user_id();

	global $wp;

	$vars = apply_filters( 'rest_query_vars', $wp->public_query_vars );	
	// Allow valid meta query vars.
	$vars = array_unique( array_merge( $vars, array('tax_query', 'meta_query', 'meta_key', 'meta_value', 'meta_compare' ) ) );
	

	foreach ( $vars as $var ) {
		if ( isset( $filter[ $var ] ) ) {
			
			$args[ $var ] = $filter[ $var ];
			
		}
	}
   return $args;
}

// Search
add_action( 'rest_api_init', function () {
	register_rest_route( 'wp/v2', '/paints-data/', array(
		'methods' => 'GET',
		'callback' => 'get_paints',
	));
});
  
function get_paints( $request ) {
	$paints = get_posts( array(
		'post_type' => 'paints',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	));
  
	$paints_array = array();
	foreach ( $paints as $paint ) {
		
		$imageUrlArr = array(
			"original" => "",
			"thumbnail" => ""
		);
		$paint_media_id = get_post_meta( $paint->ID, 'paint_media_id', true );
		if(isset($paint_media_id) && $paint_media_id!=""){
			$media_meta = wp_get_attachment_metadata( $paint_media_id );

			if(isset($media_meta['file'])){
				$imageUrlArr['original'] = wp_get_attachment_url( $paint_media_id );
			}

			$sizes = $media_meta['sizes'];

			if(count($sizes)){
				if(isset($sizes['thumbnail'])){
					$imageSrc =  getImageUrl($paint_media_id,'thumbnail');
					$imageUrlArr['thumbnail'] = $imageSrc['src'];
				}
			}
		}

		$paints_array[] = array(
			'id' => $paint->ID,
			'title' => array("rendered"=>$paint->post_title),
			'paint_media_id' => $paint_media_id,
			'postImages' => $imageUrlArr,
			'categories' => wp_get_post_terms( $paint->ID, 'wf_paints_type', array( 'fields' => 'names' ) ),
			'color' => wp_get_post_terms( $paint->ID, 'wf_paints_color', array( 'fields' => 'names' ) ),
		);
	}
  
	return $paints_array;
}