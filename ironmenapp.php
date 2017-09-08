<?php 

/* 
Plugin Name: ironmenapp-api
Plugin URI:
Description: plugin used to allow user to link to ironmen app
Version: 1.00
Author: John Calzado
*/

// Load external file to add support for MultiPostThumbnails. Allows you to set more than one "feature image" per post.
//require_once('/assets/multi-post-thumbnails.php');
//add_theme_support( 'post-thumbnails' );


function hkdc_admin_scripts() {
  wp_enqueue_script( 'jquery-ui-datepicker');
  wp_enqueue_style('e2b-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
  wp_enqueue_script(
    'wp-jquery-date-picker',
    plugin_dir_url(__FILE__) . 'jquery-ui-datepicker/pbd-datepicker.js'
  );  
}
add_action('admin_enqueue_scripts', 'hkdc_admin_scripts');


//============ CUSTOM POST TYPES ============//

// Create a single function to initialize all the different custom post types
function create_custom_post_types() {	
  register_post_type( 'ironmen-event',
    array(
      'show_in_rest' => true,
    	'supports' => array(
    		'title', 'editor', 'thumbnail'
    		),
      	'labels' => array(
        'name' => __( 'Events' ),
        'singular_name' => __( 'Event' )
      ),
    'public' => true,
    'has_archive' => true,
    )
  );
}

// add the create custom post types funtion to the intialize action list
add_action( 'init', 'create_custom_post_types' );

//============ EXTEND CUSTOM POST TYPES ============//

//function used to add feature images to posts
function add_feature_image_controls_to_posts() {
	// Add Custom image sizes
	// Note: 'true' enables hard cropping so each image is exactly those dimensions and automatically cropped
	add_image_size( 'feature-image', 960, 500, true ); 
	add_image_size( 'medium-thumb', 300, 156, true );
	add_image_size( 'small-thumb', 75, 75, true );

	// Define additional "post thumbnails" for the Industry Custom Post Type. Relies on MultiPostThumbnails to work
	if (class_exists('MultiPostThumbnails')) {
	    new MultiPostThumbnails(array(
	        'label' => 'Feature Image 2',
	        'id' => 'feature-image-2',
	        'post_type' => 'ironmen-event'
	        )
	    );   
	};
}
//add_feature_image_controls_to_posts();

//function to contain all addition of meta boxes
function add_metabox () {
  add_meta_box(
    'event_date',
    __( 'Event Date', 'Event' ),
    'add_event_date_meta_cb',
    'ironmen-event',
    'side',
    'default'
  );

  add_meta_box(
    'event_reg_date'
,    __( 'Event Registration Date', 'Event' ),
    'add_event_reg_date_meta_cb',
    'ironmen-event',
    'side',
    'default'
  );

  add_meta_box(
    'event_notif_days',
    __( 'Number of days before notifying user', 'Event' ),
    'add_event_event_notif_days_meta_cb',
    'ironmen-event'
  );

}

add_action( 'add_meta_boxes_ironmen-event', 'add_metabox' );

//callback for adding date event
function add_event_date_meta_cb ($post) {

    // // Add a nonce field so we can check for it later.
    // wp_nonce_field( 'global_notice_nonce', 'global_notice_nonce' );

    $value = get_post_meta( $post->ID, '_event_date', true );

    echo '<div><input type="text" style="width:100%" class="datepicker" name="event_date" value="' . esc_attr( $value ) . '"></div>';

}  

//callback for adding date event registration
function add_event_reg_date_meta_cb ($post) {

    $value = get_post_meta( $post->ID, '_event_reg_date', true );

    echo '<div><input type="text" style="width:100%" class="datepicker" name="event_reg_date" value="' . esc_attr( $value ) . '"></div>';
}

//callback for adding date event registration
function add_event_event_notif_days_meta_cb ($post) {

    $value = get_post_meta( $post->ID, '_event_notif_days', true );

    echo '<ul class="ironmen_event_reminders_list"></ul><a class="ironmen_event_reminders_list_btn page-title-action">Add Reminder</a>';
}

//saving meta
function save_event_date ($event_id) {
  save_meta_data($event_id, 'event_date', '_event_date');
  save_meta_data($event_id, 'event_reg_date', '_event_reg_date');
  save_meta_data($event_id, 'event_notif_days', '_event_notif_days');
}
add_action( 'save_post_ironmen-event', 'save_event_date' );


//============ HELPER FUNCTIONS ============//

//----- 2c. Save meta data of a term -----//
/**
  * TODO: UPDATE COMMENT
  * @param number: term_id is is the id of the term passed when you call edited_{$taxonomy} or created_{$taxonomy} action hook
  * @param string: $POST_field_name is the name of the POST id assigned to term field (e.g. image control)
  * @param number: term_key is used so to tell the update_term_meta function the key value you are saving to
  */
function save_meta_data( $post_id , $POST_field_name, $meta_key) {
  if ( isset( $_POST[$POST_field_name] ) ) {
    $meta_value = $_POST[$POST_field_name];
    if( $meta_value ) {
      update_post_meta( $post_id, $meta_key, $meta_value );
    } else {
      delete_post_meta( $post_id, $meta_key );
    }
  }
}  





//============ WP REST ============//

//register post fields for REST
function create_api_posts_meta_field() {
  register_rest_field( 
    'ironmen-event', 
    'feature_image_2', array(
    'get_callback' => function ( $data ) {
      return get_meta_image($data, 'event_industry-slider-image_thumbnail_id');
    })
  );
  register_rest_field(
    'ironmen-event',
    'feature_image',
    array(
        'get_callback' => 'get_image'
    )
  ); 
  register_rest_field(
    'ironmen-event',
    'event_date',
    array(
        'get_callback' => 'get_event_date'
    )
  );  
  register_rest_field(
    'ironmen-event',
    'event_reg_date',
    array(
        'get_callback' => 'get_event_reg_date'
    )
  ); 
  register_rest_field(
    'ironmen-event',
    'event_desc',
    array(
        'get_callback' => 'get_event_desc'
    )
  ); 
  register_rest_field(
    'ironmen-event',
    'event_notif_days',
    array(
        'get_callback' => 'get_event_notif_days'
    )
  );   
}
add_action( 'rest_api_init', 'create_api_posts_meta_field' );

function get_image($object, $field_name, $request){
    $thumbID = get_post_thumbnail_id($object['id']);
    $image = wp_get_attachment_image_src( $thumbID);
    return $image[0];
}

function get_meta_image($data, $meta_field){
  $meta = get_post_meta( $data['id'] );
  $img_id = $meta[$meta_field][0]; 
  $img_src_url = wp_get_attachment_image_src($img_id);
  return $img_src_url[0];  
}

function get_event_date($data) {
  $meta = get_post_meta( $data['id'] , '_event_date');
  return $meta[0];
}

function get_event_desc($data) {
  $event = get_post($data['id']);
  return $event->post_content;
}

function get_event_reg_date($data) {
  $meta = get_post_meta( $data['id'] , '_event_reg_date');
  return $meta[0];
}

function get_event_notif_days($data) {
  $meta = get_post_meta( $data['id'] , '_event_notif_days');
  return $meta[0];
}

?>