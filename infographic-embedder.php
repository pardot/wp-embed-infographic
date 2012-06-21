<?php
/*
Plugin Name: Infographic Embedder
Plugin URI: http://pardot.com
Description: Takes the infographic URL and adds content to the end of posts.
Version: 1.0
Author: Cliff Seal (Pardot)
Author URI: http://pardot.com
Author Email: cliff.seal@pardot.com
License:

  Copyright 2012 Pardot (cliff.seal@pardot.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

/* Add Meta Boxes */

add_action( 'load-post.php', 'infographic_embedder_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'infographic_embedder_post_meta_boxes_setup' );

function infographic_embedder_post_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'infographic_embedder_add_post_meta_boxes' );
	add_action( 'save_post', 'infographic_embedder_save_post_class_meta', 10, 2 );
}

function infographic_embedder_add_post_meta_boxes() {
	add_meta_box(
		'infographic_embedder-post-class',
		__( 'Infographic Embed', 'example' ),
		'infographic_embedder_post_class_meta_box',
		'post',
		'side',
		'default'
	);
}

function infographic_embedder_post_class_meta_box( $object, $box ) { ?>
	<?php wp_nonce_field( basename( __FILE__ ), 'infographic_embedder_post_class_nonce' ); ?>
	<p>
		<label for="infographic_embedder-post-class"><?php _e( "Infographic URL (.jpg or .png)", 'example' ); ?></label>
		<br />
		<input class="widefat" type="text" name="infographic_embedder-post-class" id="infographic_embedder-post-class" value="<?php echo get_post_meta( $object->ID, 'infographic_embedder_post_class', true ); ?>" size="30" />
	</p>
<?php }

/* Let's Do This: Save the Info */

add_action( 'save_post', 'infographic_embedder_save_post_class_meta', 10, 2 );

function infographic_embedder_save_post_class_meta( $post_id, $post ) {
	if ( !isset( $_POST['infographic_embedder_post_class_nonce'] ) || !wp_verify_nonce( $_POST['infographic_embedder_post_class_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}
	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}	
	$new_meta_value = ( isset( $_POST['infographic_embedder-post-class'] ) ? $_POST['infographic_embedder-post-class'] : '' );
	$meta_key = 'infographic_embedder_post_class';
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if ( $new_meta_value && '' == $meta_value ) {
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	} elseif ( '' == $new_meta_value && $meta_value ) {
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}	
}

/* Hook Helpful Stuff to the End of the_content() */

add_filter('the_content', 'add_infographic_embed');

function add_infographic_embed($content) {
	global $post;
	$infourl = get_post_meta( $post->ID, 'infographic_embedder_post_class', true );
	if ( $infourl ) {
		$embedval = htmlentities('<img src="' . $infourl . '" alt="' . get_the_title() . ' - An Infographic from ' . get_bloginfo('name') . '" width="100%" class="infographic_embedder" />
<div class="infographic_attr"><p>Embedded from <a href="' . get_permalink() . '" target="_blank">' . get_bloginfo('name') . '</a></p></div>');
		$content .= '<p><a href="' . $infourl . '" target="_blank">Download</a> this infographic.</p>';
		$content .= '<div id="infographic_embed"><h3>Embed Our Infographic On Your Site!</h3><label for="embed_width">Image Width (must be in px or %)</label><input id="embed_width" value="100%" type="text" size="50" /><input type="button" value="Update" id="embedupdate" /><textarea id="embedcode">' . $embedval . '</textarea><div id="embed_width_hidden_div" style="display: none"><textarea id="embed_width_hidden">' . $embedval . '</textarea></div></div>';
	}
	remove_filter('the_content', 'add_infographic_embed');
	return $content;
}

/* Note: We're not enqueuing, because we want these to load only when the Custom Field is there, and conditional enqueuing is HARD: http://scribu.net/wordpress/optimal-script-loading.html */

/* Print the JavaScript Stuffs */

function infographic_embed_width_script() {
	global $post;
	$infourl = get_post_meta( $post->ID, 'infographic_embedder_post_class', true );
	if ( $infourl ) {
 ?>
<script type="text/javascript">
var prev = jQuery("#embed_width").val();
jQuery("#embedupdate").bind("click", function() {
	prev = jQuery('#embed_width').val();
	var _code = jQuery("#embedcode").val();
	var _test = new RegExp(/width=['"][0-9]{1,3}(%|px)['"]/);
	var valid = /[0-9]{1,3}(%|px)/g;
	if ( !valid.test(prev) ) {
		alert('Image width value must include a % sign or "px". For instance, 80% or 500px.');
		return false;
	} else {
		jQuery("#embedcode").val(_code.replace(_test, "width=\"" + prev + "\""));
		jQuery("#embed_width_hidden").text(_code.replace(_test, "width=\"" + prev + "\""));
		jQuery("#embedcode").val(jQuery('#embed_width_hidden').text());
		return true;
	}	
});
jQuery("#embedcode").focus(function() {
    var $this = jQuery(this);
    $this.select();
    $this.mouseup(function() {
        $this.unbind("mouseup");
        return false;
    });
});
</script>
<?php } 
}

add_filter('wp_footer', 'infographic_embed_width_script');

/* Print the Styles */

function infographic_embed_style() {
	global $post;
	$infourl = get_post_meta( $post->ID, 'infographic_embedder_post_class', true );
	if ( $infourl ) {
 ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo plugins_url( 'infographicembedder.css' , __FILE__ ); ?>" />
<?php } 
}

add_filter('wp_head', 'infographic_embed_style');