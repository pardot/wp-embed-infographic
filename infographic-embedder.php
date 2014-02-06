<?php
/*
Plugin Name: Infographic Embedder
Plugin URI: http://logoscreative.co
Description: Takes the infographic URL and adds content to the end of posts.
Version: 1.1.1
Author: Cliff Seal
Author URI: http://logoscreative.co
Author Email: cliff@logoscreative.co
License:

  Copyright 2014 Cliff Seal (cseal@salesforce.com)

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

	$infotypes = apply_filters( 'infographic_embedder_post_types', array( 'post' ) );

	foreach( $infotypes as $type ) {

		add_meta_box(
			'infographic_embedder-post-class',
			__( 'Infographic Embed', 'example' ),
			'infographic_embedder_post_class_meta_box',
			$type,
			'side',
			'default'
		);

	}

}

function infographic_embedder_post_class_meta_box( $object, $box ) { ?>
		<?php wp_nonce_field( 'infographic_embedder_post_class_meta_box', 'infographic_embedder_post_class_meta_box_nonce' ); ?>
		<label for="infographic_embedder-post-class"><?php _e( "Infographic URL (.jpg or .png)", 'example' ); ?></label>
		<input class="widefat" type="text" name="infographic_embedder-post-class" id="infographic_embedder-post-class" value="<?php echo get_post_meta( $object->ID, 'infographic_embedder_post_class', true ); ?>" size="30" />
<?php }

/* Let's Do This: Save the Info */

add_action( 'save_post', 'infographic_embedder_save_post_class_meta' );

function infographic_embedder_save_post_class_meta( $post_id ) {

	if ( !isset( $_POST['infographic_embedder_post_class_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['infographic_embedder_post_class_meta_box_nonce'], 'infographic_embedder_post_class_meta_box' ) ) {
		return $post_id;
	}

	$new_meta_value = ( isset( $_POST['infographic_embedder-post-class'] ) ? $_POST['infographic_embedder-post-class'] : '' );
	$meta_key = 'infographic_embedder_post_class';
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	if ( $new_meta_value && '' === $meta_value ) {
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	} elseif ( '' === $new_meta_value && $meta_value ) {
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}	
}

/* Hook Helpful Stuff to the End of the_content() */

add_filter('the_content', 'add_infographic_embed');

function add_infographic_embed($content) {

	$infourl = get_post_meta( get_the_ID(), 'infographic_embedder_post_class', true );

	if ( $infourl ) {
		$embedval = htmlentities(
			apply_filters(
				'infographic_embedder_image_code',
				'<img src="' . $infourl . '" alt="' . get_the_title() . ' - An Infographic from ' . get_bloginfo('name') . '" width="100%" class="infographic_embedder" /><p class="infographic_attr">Embedded from <a href="' . get_permalink() . '" target="_blank">' . get_bloginfo('name') . '</a></p>'
			)
		);
		$content .= apply_filters(
			'infographic_embedder_download_html',
			'<p><a href="' . $infourl . '" target="_blank">Download</a> this infographic.</p>'
		);
		$content .= '<div id="infographic_embed">';
		$content .= apply_filters(
			'infographic_embedder_embed_html',
			'<p class="infographic-embedder-callout">Embed Our Infographic On Your Site!</p><label for="embed_width">Image Width</label>'
		);
		$content .= '<input id="embed_width" value="100" type="text" size="4" maxlength="4" /><select id="embed_unit"><option value="percentage">%</option><option value="px">px</option></select><textarea id="embedcode">' . $embedval . '</textarea><div id="embed_width_hidden_div" style="display: none"><textarea id="embed_width_hidden">' . $embedval . '</textarea></div>';
		$content .= '</div>';
	}

	return $content;
}

/* Enqueue JavaScript Stuffs */

function infographic_embed_width_script() {

	wp_register_script( 'infographic-embedder', plugins_url( '/infographic-embedder.min.js' , __FILE__ ), array( 'jquery' ), false, true );

	$infourl = get_post_meta( get_the_ID(), 'infographic_embedder_post_class', true );
	if ( $infourl ) {
		wp_enqueue_script( 'infographic-embedder' );
	}
}

add_filter( 'wp_enqueue_scripts', 'infographic_embed_width_script' );
