<?php
/*
Plugin Name: Continue Reading Link for Excerpts
Plugin URI: https://www.pqinternet.com/wordpress/wordpress-continue-reading-link-plugin-for-excerpts/
Description: by pqInternet.com. Adds a "Continue Reading →" link to Hand-Crafted Excerpts on Archive Pages (i.e. Blog).  Mainly for use with Studio Press since Studio Press adds continue link to auto generated excerpts but not hand-crafted, but should work with other themes as well.
Author: Fred Black
Version: 1.02
Author URI: http://www.pqInternet.com
*/

/** add setup menu stuff*/
if ( is_admin() ){ // admin actions
	add_action( 'admin_menu', 'pqHCExL_menu' );
	add_action( 'admin_init', 'pqHCExL_register_settings' );
}
else {
	// Add "Read More" link to hand-crafted excerpts
	add_filter('get_the_excerpt', 'wpm_manual_excerpt_read_more_link');
	function wpm_manual_excerpt_read_more_link($excerpt) {
		$options = get_option('pqHCExL_options');
		$strClass = '';
		$strLinkText = 'Continue Reading →';
		$strScreenReaderBypass = 'unchecked';
		$strScreenReaderText = "";
		if ($options['link_class_string'] != '') {
			$strClass = 'class="' . $options['link_class_string'] . '"';
		}
		if ($options['link_text_string'] != '') {
			$strLinkText = $options['link_text_string'];
		}		
		if ($options['screen_reader_bypass'] !='') {
			$strScreenReaderBypass = $options['screen_reader_bypass'];
		}
		$excerpt_more = '';
		if ($strScreenReaderBypass == 'unchecked') {
			$strScreenReaderText = '<span class="screen-reader-text">about ' . get_the_title() . '</span>';
		}
		if (has_excerpt() && ! is_attachment() && get_post_type() == 'post') {
			$excerpt_more = '<br/>&nbsp;<br/><a href="' . get_permalink() . '" ' . $strClass . ' title="Continue Reading: ' . get_the_title() . '">' . $strLinkText . $strScreenReaderText . '</a>';
		}
		return $excerpt . $excerpt_more;
	}	
}

function pqHCExL_menu() {
	add_options_page( 'pqInternet Continue Reading Link for Hand-Crafted Excerpts Options', 'Continue Reading Link', 'manage_options', 'pqHCExL', 'pqHCExL_options_page' );
}

function pqHCExL_register_settings() {
		register_setting( 'pqHCExL_options', 'pqHCExL_options', 'pqHCExL_options_validate' );
		add_settings_section('pqHCExL_main', 'Main Settings', 'pqHCExL_main_text', 'pqHCExL');		
		add_settings_field('pqHCExL_text', 'Text for Link', 'pqHCExL_link_text', 'pqHCExL', 'pqHCExL_main');
		add_settings_field('pqHCExL_class', 'Class for Link', 'pqHCExL_link_class_text', 'pqHCExL', 'pqHCExL_main');
		add_settings_field('pqHCExL_sreader', 'Bypass Screen Reader Class', 'pqHCExL_screen_reader', 'pqHCExL', 'pqHCExL_main');
}

function pqHCExL_main_text() {
		echo '<p>Adds a "Continue Reading →" link to Hand-Crafted Excerpts on Archive Pages (i.e. Blog).  Excerpts are optional hand-crafted summaries of your content, that show up on Archive Pages, RSS feeds, etc.  Hand-crafting these summaries give you a SEO as well as click through advantage.  The "Excerpt" field may not display by default on your edit post page, if not, click "Screen Options" at the top of the page and check "Excerpt". <em>Mainly for use with Studio Press since Studio Press adds continue link to auto generated excerpts but not hand-crafted, but should work with other themes as well.</em></p>';
		echo '<p>Automatically adds the correct screen reader span/class as well.</p>';
}
function pqHCExL_link_text() {
		$options = get_option('pqHCExL_options');
		echo "<input id='pqHCExL_text' name='pqHCExL_options[link_text_string]' size='40' type='text' value='{$options['link_text_string']}' />";
		echo '<em>This is the text for the link. Example: "Keep Reading...".  If blank will use "Continue Reading →". The only valid input here are letters and numbers, spaces, period, dash, greater than, and the little arrow (A-Z, a-z, 0-9, , ., -, >, →),</em>';
}
function pqHCExL_link_class_text() {
		$options = get_option('pqHCExL_options');
		echo "<input id='pqHCExL_class' name='pqHCExL_options[link_class_string]' size='40' type='text' value='{$options['link_class_string']}' />";
		echo "<em>This allows you to add a css class to the text link.  Some themes have built-in classes for displaying links as buttons,etc. Example: button small. The only valid input here are letters, numbers, spaces (A-Z, a-z, 0-9, )'</em>";
}
function pqHCExL_screen_reader() {
	$options = get_option('pqHCExL_options');
	echo "<input id='pqHCExL_sreader' name='pqHCExL_options[screen_reader_bypass]' type='checkbox' value='screen reader bypass' {$options['screen_reader_bypass']} />";
	echo '<p>Bypass adding Screen Reader Class and Text to button (screen-reader-text).  If your theme does not support this, the button will have too much text in it, in that case, check this checkbox.</p>';
	
}
// validate our options
function pqHCExL_options_validate($input) {
	$newinput['link_class_string'] = trim($input['link_class_string']);
	$newinput['link_text_string'] = trim($input['link_text_string']);
	if ($input['screen_reader_bypass'] == '') {
		$newinput['screen_reader_bypass'] = 'unchecked';
	}
	else {
		$newinput['screen_reader_bypass'] = 'checked';
	}
	if(!preg_match('/^[a-zA-Z0-9- ]*$/i', $newinput['link_class_string'])) {
		$newinput['link_class_string'] = '';
	}
	if(!preg_match('/^[a-zA-Z0-9\[\] .\->→]*$/i', $newinput['link_text_string'])) {
		$newinput['link_text_string'] = '';
	}
	return $newinput;
}
function pqHCExL_options_page() {
	$path = plugin_dir_url( __FILE__ ); 
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h1>"Continue Reading" Link Options for Excerpts</h1>';
	echo '<form method="post" action="options.php"> ';
	settings_fields( 'pqHCExL_options' );
	do_settings_sections( 'pqHCExL' );
	echo '<p>If after saving the field reverts to blank/empty, you have entered invalid characters.</p>';
	submit_button();
	echo '</form>';
	echo '<p>Read about this plugin: <a href="https://www.pqinternet.com/wordpress/wordpress-continue-reading-link-plugin-for-excerpts/" target="_blank">WordPress Continue Reading Link Plugin for Excerpts</a></p>';
	echo '<p>If you find this plugin useful, please consider placing a link to <a href="https://www.pqInternet.com" target="_blank">https://www.pqInternet.com</a> on your site and making a <a href="https://www.pqinternet.com/donate/" target="_blank">donation</a> via the form on my website.<br/>~Thank you! Fred Black.</p>';
	echo '<a href="https://www.pqInternet.com" target="_blank"><img src="' . $path . 'images/Internet-Business-and-More-400x150.png"/></a>';
	echo '</div>';	
}