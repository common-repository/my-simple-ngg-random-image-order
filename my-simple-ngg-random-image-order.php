<?php

/**
 * Plugin Name: My Simple NGG Random Image Order
 * Version: 1.0.2
 * Plugin URI: http://mannwd.com/wordpress/my-simple-ngg-random-image-order/
 * Description: Simple method of displaying a random ordering of nextgen gallery images.
 * Author: Michael Mann
 * Author URI: http://mannwd.com
 * License: GPL v2

 * Copyright (C) 2016, Michael Mann - support@mannwd.com

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation version 2.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

**/

class NGGRandomOrder {

     /* Setup the environment for the plugin */
     public function bootstrap() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_shortcode( 'ngg-random-images', array( $this, 'ngg_random_images_sc' ) );
    }

    /* Flush Rewrite Rules on Activation */
    public function activate() {
		flush_rewrite_rules();
     }

	public function ngg_random_images_sc( $atts ) {

		global $wpdb;

		$atts = shortcode_atts( array(
			'gallery' => '',
			'template' => 'basic-thumbnails'
			),
		$atts );

		$templates = array (
			"basic-slideshow" => "photocrati-nextgen_basic_slideshow",
			"basic-thumbnails" => "photocrati-nextgen_basic_thumbnails",
			"basic-imagebrowser" => "photocrati-nextgen_basic_imagebrowser",
			"basic-singlepic" => "photocrati-nextgen_basic_singlepic",
			"basic-extended_album" => "photocrati-nextgen_basic_extended_album",
			"basic-compact_album" => "photocrati-nextgen_basic_compact_album",
			// Pro Templates
			"pro-slideshow" => "photocrati-nextgen_pro_slideshow",
			"pro-thumbnail-grid" => "photocrati-nextgen_pro_thumbnail_grid",
			"pro-horizontal-filmstrip" => "photocrati-nextgen_pro_horizontal_filmstrip",
			"pro-film" => "photocrati-nextgen_pro_film",
			"pro-blog-gallery" => "photocrati-nextgen_pro_blog_gallery",
			"pro-list-album" => "photocrati-nextgen_pro_list_album",
			"pro-grid-album" => "photocrati-nextgen_pro_grid_album",
			"pro-masonry" => "photocrati-nextgen_pro_masonry",
		);

		$template = $atts[ 'template' ];

		if ( $template != "" ) {

		$usetemplate = ( array_key_exists($template, $templates) && !empty($templates[$template]) ) 
				 ? $templates[$template] 
				 : 'non-existant or empty value key';

		}

		// Gallery ID
		$galleryID = $atts[ 'gallery' ];
		$randomimages = array();

		$images = $wpdb->get_results( "SELECT pid FROM $wpdb->nggpictures WHERE galleryid=$galleryID", ARRAY_A );

		// Build image array
		for ( $i = 0; $i < count( $images ); $i++ ) {
			$randomimages[] = $images[$i];
		}

		// Shuffles gallery images for random ordering
		shuffle( $randomimages );

		$randomizedimages = "";

		// Builds Randomized Results
		for ( $i = 0; $i < count( $randomimages ); $i++ ) {
			$randomizedimages .= $randomimages[$i]['pid'] . ',';
		}

		$randomizedimages = rtrim ( $randomizedimages, "," ); // Trims trailing apostrophe

		// Build NextGen Image Shortcode, complete with random ordering
		$randomized = do_shortcode('[ngg_images override_thumbnail_settings="1" image_ids="'.$randomizedimages.'" display_type="' . $usetemplate . '" order_by=\'RAND()\']');

		return $randomized;		

	}

}

global $nextgenrandomorder;
$nextgenrandomorder = new NGGRandomOrder();
$nextgenrandomorder->bootstrap();

?>
