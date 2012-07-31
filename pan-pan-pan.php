<?php

/*
  Plugin Name: Pan Pan Pan
  Version: 0.7.1.0
  Plugin URI:
  Description:
  Author: Webnist, horike37
  Author URI: http://webni.st
  License: GPLv2 or later
 */

if ( ! defined( 'PANPANPAN_PLUGIN_URL' ) )
	define( 'PANPANPAN_PLUGIN_URL', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ));

if ( ! defined( 'PANPANPAN_PLUGIN_DIR' ) )
	define( 'PANPANPAN_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ));

//カスタム投稿タイプの作成
add_action( 'init', 'panpanpan_create_initial_post_types' );
function panpanpan_create_initial_post_types() {
	$labels = array(
		'name' => sprintf( __( '%s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'singular_name' => sprintf( __( '%s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'add_new_item' => sprintf( __( 'Add New %s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'edit_item' => sprintf( __( 'Edit %s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'new_item' => sprintf( __( 'New %s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'view_item' => sprintf( __( 'View %s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'search_items' => sprintf( __( 'Search %s', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'not_found' => sprintf( __( 'No %s found.', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
		'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'panpanpan' ), __( 'Pan Pan Pan', 'panpanpan' ) ),
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array( 'title', 'thumbnail', 'page-attributes' ),
		'rewrite' => array( 'slug' => 'pan-pan-pan', 'with_front' => false ),
		'query_var' => 'pan-pan-pan',
		'has_archive' => false,
	);
	register_post_type( 'pan-pan-pan', $args );
}

//stylesheetの登録
add_action( 'wp_print_styles', 'panpanpan_add_slide_style' );
function panpanpan_add_slide_style() {
	if ( !is_admin() && (is_home() || is_front_page() ) ) {
		wp_enqueue_style( 'pan-pan-pan-style', PANPANPAN_PLUGIN_URL . '/css/style.css' );
	}
}

//JavaScriptの登録
add_action( 'wp_print_scripts', 'panpanpan_add_slide_js' );
function panpanpan_add_slide_js() {
	if ( !is_admin() && (is_home() || is_front_page() ) ) {
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'pan-pan-pan-common', PANPANPAN_PLUGIN_URL . '/js/common-min.js', array( 'jquery' ), '0.7.1.0', true );
	}
}


//画像のサイズ指定
add_image_size( 'pan-pan-pan-slide', 640, 250, true );


//メタボックスの追加
add_action( 'admin_menu', 'panpanpan_add_meta_boxes' );
function panpanpan_add_meta_boxes() {
	add_meta_box( 'add-pan-pan-pan-link', __( 'Slide Links', 'pan-pan-pan' ), 'panpanpan_add_link_box', 'pan-pan-pan', 'normal', 'high' );
}

function panpanpan_add_link_box() {
	$post_id = get_the_ID();
	$get_noncename = 'slide_link_noncename';
	$url = esc_url( get_post_meta( $post_id, '_slide_link', true ) );
	$blank = (int) get_post_meta( $post_id, '_slide_blank', true );
	echo '<input type="hidden" name="' . $get_noncename . '" id="' . $get_noncename . '" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	echo '<p><label for="slide_link">' . __( 'Link : ', 'pan-pan-pan' );
	echo '<input type="text" name="slide_link" id="slide_link" value="' . $url . '" size="30"></label></p>';
	echo '<p><label for="slide_blank"><input type="checkbox" name="slide_blank" id="slide_blank" value="1"' . checked( 1, $blank, false ) . '> ' . __( 'Open link in a new window/tab' ) . '</label></p>';
}

//データ登録
add_action( 'save_post', 'pan_pan_pan_link_save' );
function pan_pan_pan_link_save( $post_id ) {
	$get_noncename = 'slide_link_noncename';
	$key1 = '_slide_link';
	$post1 = 'slide_link';
	$key2 = '_slide_blank';
	$post2 = 'slide_blank';
	$get1 = esc_url( $_POST[$post1] );
	$get2 = (int) $_POST[$post2];
	if ( !isset( $_POST[$get_noncename] ) )
		return;
	if ( !wp_verify_nonce( $_POST[$get_noncename], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}
	if ( '' == get_post_meta( $post_id, $key1 ) ) {
		add_post_meta( $post_id, $key1, $get1, true );
	} else if ( $get1 != get_post_meta( $post_id, $key1 ) ) {
		update_post_meta( $post_id, $key1, $get1 );
	} else if ( '' == $get1 ) {
		delete_post_meta( $post_id, $key1 );
	}

	if ( '' == get_post_meta( $post_id, $key2 ) ) {
		add_post_meta( $post_id, $key2, $get2, true );
	} else if ( $get2 != get_post_meta( $post_id, $key2 ) ) {
		update_post_meta( $post_id, $key2, $get2 );
	} else if ( '' == $get2 ) {
		delete_post_meta( $post_id, $key2 );
	}
}


//テーマで呼び出す関数
function panpanpan_get_slide_post( $limit = -1 ) {
	if ( is_home() || is_front_page() ) {
		$output = '';
		$posts_array = array( );
		$args = array(
			'post_type' => 'pan-pan-pan',
			'posts_per_page' => $limit,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);
		$posts_array = get_posts( $args );
		if ( $posts_array ) {
			$count = 0;
			$output .= '<div id="pan-pan-pan-slide">' . "\n";
			foreach ( $posts_array as $post ) {
				setup_postdata( $post );
				$count++;
				$image = get_the_post_thumbnail( $post->ID, 'pan-pan-pan-slide' );
				$slide_link = esc_url( get_post_meta( $post->ID, '_slide_link', true ) );
				if ( (int) get_post_meta( $post->ID, '_slide_blank', true ) ) {
					$blank = ' target="_blank"';
				} else {
					$blank = '';
				}
				$output .= '<div id="fragment-' . $count . '" class="ui-tabs-panel">';
				$output .= '<p class="thumb"><a href="' . $slide_link . '"' . $blank . '>' . $image . '</a></a>';
				$output .= '</div>' . "\n";
			}
			$count = 0;
			$output .= '<ul class="ui-tabs-nav">' . "\n";
			foreach ( $posts_array as $post ) {
				setup_postdata( $post );
				$count++;
				$title = get_the_title( $post->ID );
				$output .= '<li class="ui-tabs-nav-item" id="nav-fragment-' . $count . '"><a href="#fragment-' . $count . '">' . $title . '</a></li>';
			}
			$output .= '</ul>' . "\n";
			$output .= '</div>' . "\n";
			return $output;
		}
	}
}