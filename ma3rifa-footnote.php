<?php
/*
Plugin Name: Ma3rifa Footnote
Plugin URI: https://hijra.jp/ma3rifa-plugin/
Description: Search Ma3rifa and find suitable description
Version: 0.0.1
Author:HijraJapan
Author URI: https://hijra.jp
License: 
*/

require_once(WP_PLUGIN_DIR . '/ma3rifa-footnote/lib/menu.php');

function ma3rifa_lookup_method( $content = "", $count = 0 ) {
	$options=get_option(ma3rifaSettingPage::getKey());
    $api_host = esc_html($options['wikihost']);
	$api_loc = $api_host . esc_html($options['wikiapi']);;
	$api_req = "?action=query&format=json&prop=extracts&list=&generator=search&exsentences=1&exlimit=10&exintro=1&explaintext=1&gsrsearch=" . rawurlencode($content) ."&gsrlimit=1";
    $context = [
    'http' => [
        'method'        => 'GET',
        'ignore_errors' => true,
        'header'        => null
    ],
];
    
    
$http_response_header = null;
$json = file_get_contents($api_loc . $api_req , null, stream_context_create($context));
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$arr = json_decode($json,true);
    
    //var_dump( $http_response_header );
    //var_dump ($arr);
	//$api_out =  print_r($arr, true);
    $api_out = "";
    if (array_key_exists('query', $arr))
    {
    $api_out = reset($arr ["query"]["pages"])["extract"];
    //$api_out =  print_r(reset($arr ["query"]["pages"])["extract"], true);
    }else{
    $api_out = "not found";
    }
	return $api_out . '<p><a href="' . $api_host . '/wiki/' . $content . '"> 出典: '. $api_host . '</a></p>';
	//return "Showing footnote for :" . $content;
}

function ref_ma3rifa_sc_method( $atts, $content = "" ) {
	static $count = 0;
	$count++;
	$str = $content . '<sup id="fnref:' . $count . '"><a href="#fn:' . $count . '" rel="footnote">' . $content . '</a></sup><span class="footnotes"><ol><li class="footnote" id="fn:' . $count . '">' . ma3rifa_lookup_method( $content, $count ) . '<a href="#fnref:' . $count . '" title="return to article">#</a></li></ol></span>';
	return $str;
}
add_shortcode( 'ref_ma3rifa', 'ref_ma3rifa_sc_method' );

function ma3rifa_footnote_scripts_method() {
	wp_enqueue_style( 'style-name', plugins_url( '/css/bigfoot-number.css' , __FILE__ ) );
	wp_enqueue_script( 'bigfoot', plugins_url( '/js/bigfoot.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ma3rifa_footnote', plugins_url( '/js/ma3rifa_footnote.js' , __FILE__ ), array('bigfoot') );
}
add_action( 'wp_enqueue_scripts', 'ma3rifa_footnote_scripts_method' );
remove_filter('the_content', 'wpautop'); 
remove_filter('the_excerpt', 'wpautop'); 

?>