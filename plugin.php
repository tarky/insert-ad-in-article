<?php
/*
Plugin Name: Insert Ad In Article
Author: webfood
Plugin URI: http://webfood.info/
Description: Insert Ad In Article.
Version: 0.1
Author URI: http://webfood.info/
Text Domain: Insert Ad In Article
Domain Path: /languages

License:
 Released under the GPL license
  http://www.gnu.org/copyleft/gpl.html

  Copyright 2019 (email : webfood.info@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_filter('the_content','insert_ad');
function insert_ad($content){
	if(get_post_meta( get_the_ID(), "custom_ad_off", true) == 'この記事で広告を表示しない' ) {
		return $content;
	}
	$ad_num = floor(mb_strlen(strip_tags($content)) / 2000);
	if($ad_num > 5){
		$ad_num = 5;
	}
  if($ad_num < 1){
		$ad_num = 1;
	}

	$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'auto');
	$dom = new DOMDocument;
	$dom->loadHTML($content);
	$xpath = new DOMXPath($dom);
	$headers = $xpath->query("//h2|//h3");
	$h_count = $headers->length - 1;
	$intrvl = floor($h_count / $ad_num);

	$ad_code = file_get_contents( get_stylesheet_directory_uri().'/ad-in-article.html');
  $ad_code = '<p style="text-align:center;padding-bottom:0.5em;">スポンサーリンク</p><p>'.$ad_code.'</p>';

  foreach ( $headers as $i => $h ) {
		if ($i == 0){
			continue;
		}
		if($ad_num == 1){
			if($i == floor(($h_count+1)/2)){
				$ad = $dom->createCDATASection($ad_code);
				$h->parentNode->insertBefore($ad, $h);
			}
		}else{
			if(($i+1) % $intrvl == 0){
				$ad = $dom->createCDATASection($ad_code);
				$h->parentNode->insertBefore($ad, $h);
			}
		}
  }
	return $dom->saveHTML();
}
