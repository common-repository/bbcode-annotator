<?php
/*
Plugin Name: BBCode Annotate
Plugin URI: http://vandeft.com/
Description: Add annotations to posts.
Version: 1.0-trunk
Author: Radii
Author URI: http://vandeft.com/
*/

/*
    Wordpress Annotation Plugin - Copyright © 2009  Vandeft

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

// Find the first happening of a text and replace it. After, return the new string.
function v_replace_first_occurance($string, $find, $replace) {
	$len  = strlen($find); // Length of annotation string
	$pos  = strpos($string, $find); // Where is the location of this string?

	$chop    = array();
	return substr($string, 0, $pos) . $replace . substr($string, $pos + $len);
}


function v_annote($content) {
	if( !is_singular() ) { return v_strip_annote($content); }

	$anid       = 0; // Annote ID
	$append     = array(); // Append to bottom of post
	$preg_match = 0; // Matches found via preg_match_all

	$text_annotate = array();
	$text_annote   = array();

	if( preg_match_all('/\[annotate=.[^>]+\](.*)\[\/annotate\]/isU', $content, $annotates, PREG_OFFSET_CAPTURE) )
	{
		$preg_match = $preg_match | 1;
		foreach($annotates[0] as $i => $s) { $text_annotate[$s[1]] = array($s[0], $annotates[1][$i][0]); }
	}
	if( preg_match_all('/\[annote=.[^>]+\]/isU', $content, $annotes, PREG_OFFSET_CAPTURE) )
	{
		$preg_match  = $preg_match | 2;
		foreach($annotes[0] as $s) { $text_annote[$s[1]] = $s[0]; }
	}
	if( !$preg_match ) return $content;

	$annote      = array();
	$annotate    = array();
	$new_content = $content;

	if( $preg_match&3 ) {
		// Keys of Annotates
		$kannote   = array_flip(array_keys($text_annote));
		$kannotate = array_flip(array_keys($text_annotate));

		// # index to replace in annotes
		$i = 0;
		reset($kannote);
		reset($kannotate);
		do {
			if( false === key($kannotate) ) {
				$kannote[key($kannote)] = $i;
				next($kannote);
			} elseif( false === key($kannote) ) {
				$kannotate[key($kannotate)] = $i;
				next($kannotate);
			} elseif( key($kannote) < key($kannotate) ) {
				$kannote[key($kannote)] = $i;
				next($kannote);
			} elseif( key($kannotate) < key($kannote) ) {
				$kannotate[key($kannotate)] = $i;
				next($kannotate);
			}

			++$i;
		} while( current($kannote) || current($kannotate) );

		foreach( $kannote   as $v => $k ) { $annotes[$k]   = $text_annote[$v]  ; }
		foreach( $kannotate as $v => $k ) { $annotates[$k] = $text_annotate[$v]; }
		$kannote = $kannotate = NULL;
		unset($kannote, $kannotate);
	} elseif( $preg_match&1 ) { $annotates = array_values($text_annotate); }
	  elseif( $preg_match&2 ) { $annotes   = array_values($text_annote);   }

	$text_annotate = $text_annote = NULL;
	unset($text_annotate, $text_annote);

	if( !empty($annotates) ) {
		/*
		* annotate  = Hovering over the above text will show this AND (or) shown in post
		* show      = Text to show in post (optional).
		* Inside the brackets = Text added to end of post.
		*/
		$r = array('annotate', 'show');
		foreach($annotates as $i => $annotate) {
			$annotate = $annotate[0];
			$arg = array();
			preg_match('/\[annote=.[^>]+\]/isU', $annotate, $first);
			$first = $first[0];
			$first = wp_specialchars_decode($first, ENT_QUOTES);
			foreach($r as $e) {
				$match = array();
				preg_match("/$e=\"(.+)\"/isU", $first, $match);
				$arg[$e] = wp_specialchars(str_replace("''", '"', array_pop($match)), ENT_QUOTES);
			}

			$anid = $i + 1;
			if( empty($arg['show'])       ) $arg['show']       = $anid;
			if( empty($annotates[$i][1])  ) $annotates[$i][1]  = $arg['annotate'];
		
			$new_string  = '<sup id="annote_' . $anid . '"><a href="#annotation_' . $anid . '" title="' . $arg['annotate'] . '">' . $arg['show'] . '</a></sup>';
			$new_content = v_replace_first_occurance($new_content, $annotates[$i][0], $new_string);
			$append[$i] = '<strong id="annotation_' . $anid . '"><a href="#annote_' . $anid . '">' . $arg['show'] . '</a></strong>. ' . $annotates[$i][1];
		}
	}

	if( !empty($annotes) ) {
		/*
		* annote = Hovering over the above text will show this AND (or) shown in post
		* show  = Text to show in post (optional).
		*/
		$r = array('annote', 'show');
		foreach($annotes as $i => $annote) {
			$arg = array();
			$annote = wp_specialchars_decode($annote, ENT_QUOTES);
			foreach($r as $e) {
				$match = array();
				preg_match("/$e=\"(.+)\"/isU", $annote, $match);
				$arg[$e] = wp_specialchars(str_replace("''", '"', array_pop($match)), ENT_QUOTES);
			}

			$anid = $i + 1;
			if( empty($arg['show']) ) $arg['show'] = $anid;
		
			$new_string  = '<sup id="annote_' . $anid . '"><a href="#annotation_' . $anid . '" title="' . $arg['annote'] . '">' . $arg['show'] . '</a></sup>';
			$new_content = v_replace_first_occurance($new_content, $annotes[$i], $new_string);
			$append[$i] = '<strong id="annotation_' . $anid . '"><a href="#annote_' . $anid . '">' . $arg['show'] . '</a></strong>. ' . $arg['annote'];
		}
	}

	if( sizeof($append) ) {
		ksort($append);
		$new_content .= "\n<hr />\r\n<h6>Annotations</h6>\r\n<p>" . implode("<br />\n", $append) . "</p>";
	}

	return $new_content;
}

// Remove annote text (so if you're viewing index and stuff, it won't be shown).
function v_strip_annote($content) {
	$content = preg_replace('/\[annotate=.[^>]+\](.*)\[\/annotate\]/isU', '', $content);
	$content = preg_replace('/\[annote=.[^>]+\]/isU', '', $content);
	return $content;
}

add_filter('the_content', 'v_annote', 1);
add_filter('the_excerpt', 'v_annote', 1);
?>