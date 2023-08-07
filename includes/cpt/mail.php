<?php
// Register Custom Post Type
function mails() {

	$labels = array(
		'name'                  => _x( 'mails', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'mail', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'mails', 'text_domain' ),
		'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),

	);
	$args = array(
		'label'                 => __( 'mails', 'text_domain' ),
		'description'           => __( 'Moodboard mails', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor2','custom-fields' ),
		'taxonomies'            => array( 'wf_mails_type', 'wf_mails_color' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-email',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		// 'capabilities'          => $capabilities,
       // 'rewrite'             => array( 'slug' => 'accounts','with_front' => true )
	);

	register_post_type( 'mails', $args );

	$labels = array(
	    'name'              => 'Types',
	    'singular_name'     => 'Type',
	    'search_items'      => 'Search Types',
	    'all_items'         => 'All Types',
	    'parent_item'       => 'Parent Type',
	    'parent_item_colon' => 'Parent Type:',
	    'edit_item'         => 'Edit Type',
	    'update_item'       => 'Update Type',
	    'add_new_item'      => 'Add New Type',
	    'new_item_name'     => 'New Type Name',
	    'menu_name'         => 'Types',
	  );

	$args = array(
	    'hierarchical'      => true,
	    'labels'            => $labels,
	    'show_ui'           => true,
	    'show_admin_column' => true,
	    'query_var'         => true,
	    'rewrite'           => array( 'slug' => 'type' ),
	    'show_in_rest'          => true,
	  );

	  register_taxonomy('wf_mails_type',array('mails'),$args);

	  // Add a taxonomy like tags
	  $labels = array(
	    'name'                       => 'Email Formatting',
	    'singular_name'              => 'Color',
	    'search_items'               => 'Color Name',
	    'popular_items'              => 'Popular Color',
	    'all_items'                  => 'All Color',
	    'parent_item'                => null,
	    'parent_item_colon'          => null,
	    'edit_item'                  => 'Edit Color',
	    'update_item'                => 'Update Color',
	    'add_new_item'               => 'Add New Color',
	    'new_item_name'              => 'New Color Name',
	    'separate_items_with_commas' => 'Separate Email Formatting with commas',
	    'add_or_remove_items'        => 'Add or remove Email Formatting',
	    'choose_from_most_used'      => 'Choose from most used Email Formatting',
	    'not_found'                  => 'No Email Formatting found',
	    'menu_name'                  => 'Email Formatting',
	  );

	  $args = array(
	    'hierarchical'          => false,
	    'labels'                => $labels,
	    'show_ui'               => true,
	    'show_admin_column'     => true,
	    'update_count_callback' => '_update_post_term_count',
	    'query_var'             => true,
	    'rewrite'               => array( 'slug' => 'color' ),
	    'show_in_rest'          => true,
	  );

	  register_taxonomy('wf_mails_color','mails',$args);

}
add_action( 'init', 'mails', 0 );


function only_users_mails( $query )
{
	if ( $query->get( 'post_type' ) === 'mails' ) {
		$query->set( 'author', get_current_user_id() );
	}
}
// add_action( 'pre_get_posts', 'only_users_mails' );