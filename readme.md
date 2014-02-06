# Infographic Embedder

Give your readers an easier way to embed your infographics (or other images) on their sites, with proper attribution back to you.

## Description

With a single URL in an 'Edit Post' page (and no setup), you can automatically add a properly attributed, easy-to-copy embed code to the bottom of your posts. Readers can also edit the width of the image in the embed code to either percent or pixels (the default is 100%).

While it was built with infographics in mind, you can certainly use any image you'd like.

Want to improve the plugin or add a feature? [Fork it on GitHub](https://github.com/Pardot/wp-embed-infographic) and let's work on it!

## Installation

1. Upload the `wp-embed-infographic` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Insert the URL of the image you're using in the 'Infographic Embedder' meta box.
1. Publish or update your post.

### Filter: Add Post Types

```
function infographics_on_my_post_types() {
	return array (
		'post',
		'page',
		'other-post-type'
	);
}

add_filter ( 'infographic_embedder_post_types', 'infographics_on_my_post_types' );
```

### Filter: Edit Embed Code

```
function infographics_custom_embed_code() {
	return '<img src###"' . get_post_meta( get_the_ID(), 'infographic_embedder_post_class', true ) . '" alt###"' . get_the_title() . ' - An Infographic from the Awesome ' . get_bloginfo('name') . '" width###"100%" class###"infographic_embedder" /><p class###"infographic_attr">How awesome is <a href###"' . get_permalink() . '" target###"_blank">' . get_bloginfo('name') . '?!</a></p>';
}

add_filter ( 'infographic_embedder_image_code', 'infographics_custom_embed_code' );
```

### Filter: Download Text

```
function infographics_custom_image_code() {
	return '<p><a href###"' . get_post_meta( get_the_ID(), 'infographic_embedder_post_class', true ) . '" target###"_blank">Download</a> our infographic today!</p>';
}

add_filter ( 'infographic_embedder_download_html', 'infographics_custom_image_code' );
```

### Filter: Custom Header and Labeling

```
function infographics_custom_labeling() {
	return '<h3>Embed</h3><label for###"embed_width">Image Width</label>';
}

add_filter ( 'infographic_embedder_embed_html', 'infographics_custom_labeling' );
```

## Screenshots

1. On any post where the Infographic Embed URL is present
2. Meta box on post pages

## Changelog

### 1.1.1
Fixes a bug with sites changing `wpautop`

### 1.1
1. Add filters
1. Remove default styling
1. Improve UI for changing image size
1. Bug fixes for themes that don't include jQuery by default
1. General code refactoring

### 1.0
Initial release.

## Upgrade Notice

### 1.1.1
This updates fixes a bug for sites changing wpautop() around. 1.1 is a complete refactoring: default styling is removed, changing the image size is improved, and filters were added. Bugs for themes without jQuery were also fixed.

### 1.1
This update is a complete refactoring: default styling is removed, changing the image size is improved, and filters were added. Bugs for themes without jQuery were also fixed.
