<?php

//定数
define("POSTS_PER_PAGE", 30);
define("AADCBLOG_CATEGORY", 'aadcblog_category');
define("NEWS_CATEGORY", 'news_category');

// ヘッダー無効化
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'rel_canonical');

// 絵文字無効化
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script' );
remove_action('admin_print_styles', 'print_emoji_styles' );

/* WordPress Admin Bar 絵文字無効化 */
add_filter( 'show_admin_bar', '__return_false' );

// WPメニュー非表示
add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
  remove_menu_page( 'index.php' ); //ダッシュボード
//  remove_menu_page( 'edit.php' ); //投稿メニュー
//  remove_menu_page( 'upload.php' ); //メディア
//  remove_menu_page( 'edit.php?post_type=page' ); //ページ追加
  remove_menu_page( 'edit-comments.php' ); //コメントメニュー
  remove_menu_page( 'themes.php' ); //外観メニュー
//  remove_menu_page( 'plugins.php' ); //プラグインメニュー
  remove_menu_page( 'users.php' ); // ユーザー
//  remove_menu_page( 'tools.php' ); //ツールメニュー
//  remove_menu_page( 'options-general.php' ); //設定メニュー
}

// 投稿名称変更
function Change_menulabel() {
   global $menu;
   global $submenu;
   $name = 'News Media';
   $menu[5][0] = $name;
   $submenu['edit.php'][5][0] = $name.'一覧';
   $submenu['edit.php'][10][0] = '新規追加';
}
function Change_objectlabel() {
   global $wp_post_types;
   $name = 'News Media';
   $labels = &$wp_post_types['post']->labels;
   $labels->name = $name;
   $labels->singular_name = $name;
   $labels->add_new = _x('追加', $name);
   $labels->add_new_item = '新規'.$name.'を追加';
   $labels->edit_item = $name.'の編集';
   $labels->new_item = '新規'.$name;
   $labels->view_item = $name.'を表示';
   $labels->search_items = $name.'を検索';
   $labels->not_found = $name.'が見つかりませんでした';
   $labels->not_found_in_trash = 'ゴミ箱に'.$name.'は見つかりませんでした';
}
add_action( 'init', 'Change_objectlabel' );
add_action( 'admin_menu', 'Change_menulabel' );

// タグ非表示
function hide_taxonomy_from_menu() {
    global $wp_taxonomies;
 
    // タグの非表示
    if ( !empty( $wp_taxonomies['post_tag']->object_type ) ) {
        foreach ( $wp_taxonomies['post_tag']->object_type as $i => $object_type ) {
            if ( $object_type == 'post' ) {
                unset( $wp_taxonomies['post_tag']->object_type[$i] );
            }
        }
    }
    return true;
}
add_action( 'init', 'hide_taxonomy_from_menu' );

// ウィジェット
register_sidebar( array(
   'name'          => 'Widget-BizCalendar',
   'id'            => 'Widget-BizCalendar',
   'description'   => 'Biz Calendarのウィジットエリアです。',
   'before_widget' => '<div id="%1$s" class="widget %2$s">',
   'after_widget'  => '</div>',
) );


// タイトル表示
function setup_aadctheme() {
   add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'setup_aadctheme');


// アイキャッチ画像
add_theme_support('post-thumbnails');
add_image_size('media_thumbnail');


//functions.php
function register_my_menus() { 
   register_nav_menus( array(
      'header-menu' => 'Header Menu',
      'footer-menu'  => 'Footer Menu',
   ));
}
add_action( 'after_setup_theme', 'register_my_menus' );

function add_additional_class_on_li($classes, $item, $args){
   if (isset($args->add_li_class)) {
      $classes['class'] = $args->add_li_class;
   }
   return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);

function pagename_class($classes = '') {
   if (is_page()) {
       $page = get_page(get_the_ID());
       $classes[] = $page->post_name;
       if ($page->post_parent) {
         $classes[] = get_page_uri($page->post_parent);
    }
   }
return $classes;
}
add_filter('body_class','pagename_class');


//記事から最初の画像を取得する
function get_first_image() {
   global $post, $posts;
   $first_img = '';
   ob_start();
   ob_end_clean();
   $first_img = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
   if(count($matches[1])>0)
      $first_img = $matches[1][0];

   if(empty($first_img)){ //Defines a default image
     $first_img = get_stylesheet_directory_uri()."/assets/images/common/slide-common.png";
   }
   return $first_img;
}