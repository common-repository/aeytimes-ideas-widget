<?php

/*
 * Plugin Name: Aeytimes Ideas Widget
 * Plugin URI: http://aeytimes.com/
 * Author: Alexey Yermolai
 * Author URI: http://aeytimes.com/
 * Version: 1.0.8
 * Description: Displays your latest ideas on AeyTimes.
 */

include_once(ABSPATH . WPINC . '/feed.php');

class AeytimesRSS extends WP_Widget {
	function AeytimesRSS() {
		$widget_ops = array( 'classname' => 'aeytimes', 'description' => 'A widget displaying your latest ideas on AeyTimes.' );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'aeytimes-widget' );
		$this->WP_Widget('aeytimes-widget', 'Aeytimes Ideas Widget', $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 900;' ) );
		extract($args, EXTR_SKIP);
		$title = apply_filters('widget_title', $instance['title']);
		$uri = "http://aeytimes.com/users_feeds/".$instance['userid'].".xml";
		$max = $instance['latestmax'];

		echo $before_widget;
		if ($title) {
			echo $before_title . $title . $after_title;
		}

		if($instance['userid'] == "") {
			echo "Invalid Username";
		}
		else
		{
		$rss = fetch_feed($uri);

		if (!is_wp_error($rss)) {
			$maxitems = $rss->get_item_quantity($max);
			$rss_items = $rss->get_items(0, $maxitems);
		}
		
		echo "<ul>";
		foreach ($rss_items as $item) {
			echo "<li style=\"list-style:disc;\">";
			echo "<a href=\"".$item->get_permalink()."\">";
			echo $item->get_title();
			echo "</a>";
			echo "</li>";
		}

		echo "</ul>";
		if($instance['displaylink'] == "on") {
			echo "<p style=\"text-align: right;\"><a href=\"http://aeytimes.com/profile_media/".$instance['aeyid']."/\" target=\"_blank\">... see all my AeyTimes ideas</a></p>";
		}
		}
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['aeyid'] = strip_tags($new_instance['aeyid']);
		$instance['userid'] = file_get_contents("http://aeytimes.com/getusername/".$instance['aeyid']."/");
		$instance['latestmax'] = strip_tags($new_instance['latestmax']);
		$instance['displaylink'] = strip_tags($new_instance['displaylink']);
		return $instance;
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$aeyid = esc_attr($instance['aeyid']);
		$latestmax = esc_attr($instance['latestmax']);
		$displaylink = esc_attr($instance['displaylink']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('aeyid'); ?>">Username:</label>
			<input id="<?php echo $this->get_field_id('aeyid'); ?>" name="<?php echo $this->get_field_name('aeyid'); ?>" value="<?php echo $aeyid; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('latestmax'); ?>">Number of ideas to display:</label>
			<select id="<?php echo $this->get_field_id('latestmax'); ?>" name="<?php echo $this->get_field_name('latestmax'); ?>" class="widefat" style="width:100%;">
				<option <?php if ( '1' == $latestmax ) echo 'selected="selected"'; ?>>1</option>
				<option <?php if ( '2' == $latestmax ) echo 'selected="selected"'; ?>>2</option>
				<option <?php if ( '3' == $latestmax ) echo 'selected="selected"'; ?>>3</option>
				<option <?php if ( '4' == $latestmax ) echo 'selected="selected"'; ?>>4</option>
				<option <?php if ( '5' == $latestmax ) echo 'selected="selected"'; ?>>5</option>
				<option <?php if ( '6' == $latestmax ) echo 'selected="selected"'; ?>>6</option>
				<option <?php if ( '7' == $latestmax ) echo 'selected="selected"'; ?>>7</option>
				<option <?php if ( '8' == $latestmax ) echo 'selected="selected"'; ?>>8</option>
				<option <?php if ( '9' == $latestmax ) echo 'selected="selected"'; ?>>9</option>
				<option <?php if ( '10' == $latestmax ) echo 'selected="selected"'; ?>>10</option>
				<option <?php if ( '15' == $latestmax ) echo 'selected="selected"'; ?>>15</option>
				<option <?php if ( '20' == $latestmax ) echo 'selected="selected"'; ?>>20</option>
				<option <?php if ( '30' == $latestmax ) echo 'selected="selected"'; ?>>30</option>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php if ($displaylink == 'on') echo 'checked="checked"'; ?> id="<?php echo $this->get_field_id('displaylink'); ?>" name="<?php echo $this->get_field_name('displaylink'); ?>" />
			<label for="<?php echo $this->get_field_id('displaylink'); ?>">Display link to your ideas on AeyTimes?</label>
		</p>

<?php
	}
}

function register_AeytimesRSSWidget(){
	register_widget('AeytimesRSS');
}

function addAeytimesRSSWidgetOptions() {
	echo '<div class="wrap">';
	echo '<h2>Instructions for using the AeyTimes Ideas Widget</h2>';
	echo '<p><a href="http://aeytimes.com/">AeyTimes</a> is an Idea Journal and Social Network that allows people to share ideas and inspirations, and to submit feedback or comments to improve websites, services, or products.</p>';
	echo '<p>To start using the Aeytimes Ideas Widget:</p>';
	echo '<ol>';
//	echo '<li>Upload aeytimes.php to the /wp-content/plugins/ directory</li>';
//	echo '<li>Activate the plugin through the \'Plugins\' menu in WordPress administration panel.</li>';
	echo '<li>First <a href="http://aeytimes.com/signup/">register</a> at <a href="http://aeytimes.com/">AeyTimes</a>.</li>';
	echo '<li>Once you have created your account, <a href="http://aeytimes.com/login/">login</a> and create your AeyTimes ideas pages.</li>';
	echo '<li>Within your WordPress administration panel, drag your widget to the desired position on the \'Widgets\' page under \'Appearance\'.</li>';
	echo '<li>Enter your AeyTimes username into the "Username" field of your widget.</li>';
	echo '<li>Select the number of ideas that you would like to display.</li>';
	echo '<li>Click on "Save".</li>';
	echo '<li>It may take up to 15 minutes before ideas start displaying.</li>';
	echo '</ol>';
	echo '</div>';
}

function addAeytimesRSSWidgetOptionsPage() {
	add_options_page('Ideas Widget Options', 'Ideas Widget', 'manage_options', 'aeytimes-ideas', 'addAeytimesRSSWidgetOptions' );
}

add_action('init', 'register_AeytimesRSSWidget', 1);
add_action('admin_menu', 'addAeytimesRSSWidgetOptionsPage' );

?>
