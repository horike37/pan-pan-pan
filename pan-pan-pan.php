<?php

/*
  Plugin Name: Carousel（Pan Pan Pan）
  Version: 0.1
  Plugin URI:
  Description: スライドショー用のプラグイン (Original by Webnist, horike37)
  Author: gatespace (Original by Webnist, horike37)
  Author URI: http://gatespace.wordpress.com/
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
		'name' => sprintf( __( '%s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'singular_name' => sprintf( __( '%s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'add_new_item' => sprintf( __( 'Add New %s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'edit_item' => sprintf( __( 'Edit %s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'new_item' => sprintf( __( 'New %s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'view_item' => sprintf( __( 'View %s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'search_items' => sprintf( __( 'Search %s', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'not_found' => sprintf( __( 'No %s found.', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
		'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'panpanpan' ), __( 'Carousel', 'panpanpan' ) ),
	);
	$args = array(
		'labels' => $labels,
		'public' => false, // false ; show_ui=false, publicly_queryable=false, exclude_from_search=true, show_in_nav_menus=false
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'supports' => array( 'title', 'thumbnail', 'page-attributes' ),
		'rewrite' => false,
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
add_image_size( 'pan-pan-pan-slide', 700, 300, true );


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
	}
	return $output;
}

/*
 * 管理画面の一覧にサムネイルと順番を表示
 * 参照　http://www.warna.info/archives/1661/
 * 参照　http://www.webopixel.net/wordpress/167.html
 */

// カラムを追加
function panpanpan_manage_posts_columns( $posts_columns ) {
	$new_columns = array();
	foreach ( $posts_columns as $column_name => $column_display_name ) {
		if ( $column_name == 'date' ) {
			$new_columns['thumbnail'] = __('Thumbnail');
			$new_columns['order'] = __( 'Order' );
			add_action( 'manage_posts_custom_column', 'panpanpan_add_column', 10, 2 );
		}
		$new_columns[$column_name] = $column_display_name;
	}
	return $new_columns;

}

// 追加したカラムの中身
function panpanpan_add_column($column_name, $post_id) {
	$post_id = (int)$post_id;

	// アイキャッチ
	if ( $column_name == 'thumbnail') {
		$thum = ( get_the_post_thumbnail( $post_id, array(50,50), 'thumbnail' ) ) ? get_the_post_thumbnail( $post_id, array(50,50), 'thumbnail' ) : __('None') ;
		echo $thum;
	}

	// 順序
	if ( $column_name == 'order' ) {
		$post = get_post( $post_id );
		echo $post->menu_order;
	}
}

// 追加したカラムのスタイルシート
function panpanpan_add_menu_order_column_styles() {
	if ('pan-pan-pan' == get_post_type()) {
		
?>
<style type="text/css" charset="utf-8">
.fixed .column-thumbnail {
	width: 10%;
}
.fixed .column-order {
	width: 7%;
	text-align: center;
}
</style>
<?php
	}
}

// 順序でソートできるように
function add_menu_order_sortable_column( $sortable_column ) {
	$sortable_column['order'] = 'menu_order';
	return $sortable_column;
}

add_filter( 'manage_pan-pan-pan_posts_columns', 'panpanpan_manage_posts_columns' );
add_action( 'admin_print_styles-edit.php', 'panpanpan_add_menu_order_column_styles' );
add_filter( 'manage_edit-pan-pan-pan_sortable_columns', 'add_menu_order_sortable_column' );

