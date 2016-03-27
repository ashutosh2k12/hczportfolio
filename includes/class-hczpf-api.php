<?php
/**
 * @package HCZ_Portfolio
 * @author  Ashutosh Chaudhary <me@aboutashu.com>
 */
class HCZPF_Api extends HCZPF {
	

	public function __construct( ) {

		$this->hczpfapi_enqueue_styles();
		$this->hczpfapi_enqueue_scripts();
	}

	
	public function hczpfapi_enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-bootstrap-css', $this->plugin_url . 'css/bootstrap.css', array(), $this->version);	
		wp_enqueue_style( $this->plugin_slug . '-bxslider-css', $this->plugin_url . 'css/jquery.bxslider.css', array(), $this->version);
		wp_enqueue_style( $this->plugin_slug . '-portfolio-css', $this->plugin_url . 'css/hcz-pf.css', array(), $this->version);	
	}

	public function hczpfapi_enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script( $this->plugin_slug . '-bxslider-script', $this->plugin_url . 'js/jquery.bxslider.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( $this->plugin_slug . '-portfolio-script', $this->plugin_url . 'js/hcz-pf.js', array( 'jquery' ), $this->version );
	}

}
