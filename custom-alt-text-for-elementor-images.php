<?php
/**
 * Plugin Name: Custom Alt Text for Elementor Images
 * Plugin URI: https://github.com/MIGHTYminnow/custom-alt-text-for-elementor-images
 * Description: Adds options to the Elementor Image Widget, allowing you to set custom alt text, use the attachment alt text, or choose no alt text. This gives you more control over accessibility and SEO for your website's images.
 * Version: 1.0.0
 * Author: MIGHTYminnow
 * Author URI: https://mightyminnow.com
 * Text Domain: catei
 */

class Image_Widget_Custom_Alt {

	public $element;

	public function __construct() {
		add_action( 'elementor/element/image/section_image/before_section_end', [ $this, 'add_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render' ] );
		add_action( 'elementor/frontend/widget/after_render', [ $this, 'after_render' ] );
	}

	public function add_controls( $element, $args ) {
		$element->add_control(
			'alt_text_type',
			[
				'label' => esc_html__( 'Alternative Text', 'catei' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'attachment',
				'options' => [
					'attachment'  => esc_html__( 'Attachment Alt Text', 'catei' ),
					'none' => esc_html__( 'None', 'catei' ),
					'custom' => esc_html__( 'Custom', 'catei' ),
				],
			]
		);

		$element->add_control(
			'custom_alt_text',
			[
				'label' => esc_html__( 'Custom Alt Text', 'catei' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'alt_text_type' => 'custom',
				],
			]
		);
	}

	public function after_render( $element ) {
		if ( 'image' === $element->get_name() ) {
			remove_filter( 'get_post_metadata', [ $this, 'replace_alt_text' ] );
		}
	}

	public function before_render( $element ) {
		if ( 'image' === $element->get_name() ) {
			$this->element = $element;
			add_filter( 'get_post_metadata', [ $this, 'replace_alt_text' ], 10, 4 );
		}
	}

	public function replace_alt_text( $value, $object_id, $meta_key, $single ) {
		if ( '_wp_attachment_image_alt' === $meta_key ) {
			switch ( $this->element->get_settings( 'alt_text_type' ) ) {
				case 'custom' :
					return $this->element->get_settings( 'custom_alt_text' );
					break;
				case 'none':
					return '';
					break;
				default:
					return $value;
			}
		}
		return $value;
	}

}

global $image_widget_custom_alt;
$image_widget_custom_alt = new Image_Widget_Custom_Alt();
