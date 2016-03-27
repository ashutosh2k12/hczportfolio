<?php
/**
 * @package HCZ_Portfolio
 * @author  Ashutosh Chaudhary <me@aboutashu.com>
 */
class HCZPF_Front extends HCZPF {

	protected static $instance = null;

	protected $parent = null;

	public $hcz_portfolio = array('tags'=>array(),'projects'=>array());

	public $term_ids = array();

	private $settings = null;
	

	public function __construct( $parent ) {

		self::$instance = &$this;

		$this->parent = &$parent;

		$this->settings = $this->parent->get_options();
		
		add_filter('get_portfolio_photos', array( $this,'hczpf_get_portfolio_photos' ) ,10,0);
		add_filter('get_portfolio_photo', array( $this,'hczpf_get_portfolio_photo' ) ,10,0);
		add_filter('get_portfolio_date', array( $this,'hczpf_get_portfolio_date' ) ,10,0);
		add_filter('get_portfolio_link', array( $this,'hczpf_get_portfolio_link' ) ,10,0);
		add_filter('get_portfolio_tags', array( $this,'get_terms_by_post_type' ) ,10,2);
		add_filter( 'query_vars', array( $this,'hellcoderz_query_vars' ) , 10, 1 );

		//Add shortcode
		add_shortcode( $this->shortcode , array( $this, 'hczpf_shortcode' ));

		//Detect shortcode at front-end
		add_action( 'wp', array( $this, 'PortfolioShortCodeDetect' ) );
	}

	public function get_setting_layout() {
		$HCZ_PF_Settings  = $this->parent->get_options();
	    return ( count($HCZ_PF_Settings) > 0 ) ? 
                            $HCZ_PF_Settings[ $this->setting_layout ] :
                            $this->default_layout_col;
	}

	public function get_setting_showtagtop() {
		$HCZ_PF_Settings  = $this->parent->get_options();
	    return ( count($HCZ_PF_Settings) > 0 ) ? 
                            $HCZ_PF_Settings[ $this->setting_taglist ] :
                            $this->default_taglist_top;
	}

	public function get_terms_by_post_type( $taxonomies, $post_types ) {

	    global $wpdb;

	    $query = $wpdb->prepare(
	        "SELECT t.*, COUNT(*) from $wpdb->terms AS t
	        INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
	        INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
	        INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
	        WHERE p.post_type IN('%s') AND tt.taxonomy IN('%s')
	        GROUP BY t.term_id",
	        join( "', '", $post_types ),
	        join( "', '", $taxonomies )
	    );

	    $results = $wpdb->get_results( $query );

	    return $results;

	}

	public function hczpf_get_portfolio_photos( ) {
	    return unserialize(get_post_meta( get_the_ID(), 'hcz_all_photos_details' , true));
	}

	public function hczpf_get_portfolio_photo( ) {

		$hcz_photos_details = apply_filters('get_portfolio_photos', $this->post_type );
		return (count($hcz_photos_details) > 0) ? $hcz_photos_details[0]['hcz_image_url'] : $this->default_placeholder;

	}

	public function hczpf_get_portfolio_date( ) {
	    return get_post_meta( get_the_ID(), 'hcz_pf_date' , true);
	}

	public function hczpf_get_portfolio_link( ) {
	    return get_post_meta( get_the_ID(), 'hcz_demo_link' , true);
	}

	public function hellcoderz_query_vars( $qvars ) {
	  $qvars[] = 'tag';
	  return $qvars;
	}
	
	/**
	 * Apply Shortcode
	 */
	public function hczpf_shortcode( $atts ){

		$_gettag = (strlen( get_query_var('tag') ) > 0)?explode(',', get_query_var('tag') ):array();

		$term_objects = apply_filters( 'get_portfolio_tags',
										array( 'post_tag' ),
										array( $this->post_type )
									);

		$all_posts = wp_count_posts( $this->post_type )->publish;

		$AllGalleries = array('post_type' => $this->post_type , 'orderby' => 'ASC','posts_per_page' =>$all_posts);

		foreach ($term_objects as $term_object) {
	    	$this->hcz_portfolio['tags'][ $term_object->term_id ] = array(
													    		"name" 	=> $term_object->name,
													    		"slug" 	=> $term_object->slug,
													    	);
	    }

	    if( (count($_gettag) > 0) ){

	    	foreach ($this->hcz_portfolio['tags'] as $termid => $tag) {

	    		if(in_array($tag['slug'], $_gettag)) array_push($this->term_ids, $termid);

	    	}

	    	$AllGalleries['tax_query'] = array(array('taxonomy' => 'post_tag', 'field' => 'id', 'terms' => $this->term_ids) );
	    		
	    }

		if( is_array( $atts )) {
			extract( $atts );
			if( isset( $id ) ){

				$AllGalleries['post__in'] = array( $id );

	    		$loop = new WP_Query( $AllGalleries );

				ob_start();
				include( $this->parent->directory .'/tpl/shortcode-single.php' );
				return ob_get_clean();
			}
		}else{
			$loop = new WP_Query( $AllGalleries );

			ob_start();
			if( $this->settings[ $this->setting_taglist ] == "yes" )
				include( $this->parent->directory .'/tpl/tag-list.php' );

			include( $this->parent->directory .'/tpl/shortcode.php' );
			return ob_get_clean();
		}
	}


	function PortfolioShortCodeDetect() {
	    global $wp_query;
	    $Posts = $wp_query->posts;
	    $Pattern = get_shortcode_regex();

	    foreach ($Posts as $Post) {
	        if( preg_match_all( '/'. $Pattern .'/s', $Post->post_content, $Matches ) && array_key_exists( 2, $Matches ) && in_array( $this->shortcode, $Matches[2] ) ) {

				$this->hczpf_enqueue_styles();
				$this->hczpf_enqueue_scripts();
	            
	            break;
	        }
	    }
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 */
	public function hczpf_enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-bootstrap-css', $this->plugin_url . 'css/bootstrap.css', array(), $this->version);	
		wp_enqueue_style( $this->plugin_slug . '-bxslider-css', $this->plugin_url . 'css/jquery.bxslider.css', array(), $this->version);
		wp_enqueue_style( $this->plugin_slug . '-portfolio-css', $this->plugin_url . 'css/hcz-pf.css', array(), $this->version);	
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 */
	public function hczpf_enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script( $this->plugin_slug . '-bxslider-script', $this->plugin_url . 'js/jquery.bxslider.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( $this->plugin_slug . '-portfolio-script', $this->plugin_url . 'js/hcz-pf.js', array( 'jquery' ), $this->version );
	}

}
