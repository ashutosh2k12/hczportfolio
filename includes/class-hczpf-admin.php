<?php
/**
 * @package HCZ_Portfolio
 * @author  Ashutosh Chaudhary <me@aboutashu.com>
 */

class HCZPF_Admin extends HCZPF_Front {

	static $instance = null;
	static $plugin_front = null;
	public $options = null;
	public $settings = array();
	

	public function __construct() { 

		self::$instance = &$this;

		add_action( 'init', array( &$this, 'register_cpt' ) );
		add_action( 'admin_init', array( &$this, 'HellcoderzPortfolio_init' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( &$this, 'hcz_portfolio_meta_save' ) );
		add_action( 'admin_menu' , array( &$this, 'hcz_SettingsPage' ) );
		add_action( 'plugins_loaded', array( &$this, 'hcz_load_plugin_textdomain' ) );

		// Activate plugin when new blog is added [Multisite]
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_blog' ) );

		parent::__construct( $this );

	}

	/**
	 * Load the text domain for translation.
	 */
	public function hcz_load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'hcz' . $domain, get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 */
	private static function get_blog_ids() {

		global $wpdb;

		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired when the plugin is activated (supports network-wide installations).
	 *
	 */
	public function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				$blog_ids = $this->get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					$this->single_activate();
				}

				restore_current_blog();

			} else {
				$this->single_activate();
			}

		} else {
			$this->single_activate();
		}
		
	}
	
	

	/**
	 * Fired when the plugin is deactivated.
	 */
	public function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 */
	public function single_activate() {
		$this->init_settings();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 */
	public function single_deactivate() {
		// @TODO: Destroy settings
	}

	public function register_cpt() {

		$labels = array(
	        'name'                => _x( 'Hellcoderz Portfolio', 'Hellcoderz Portfolio', $this->textdomain ),
	        'singular_name'       => _x( 'Hellcoderz Portfolio', 'Hellcoderz Portfolio', $this->textdomain ),
	        'menu_name'           => __( 'Hellcoderz Portfolio', $this->textdomain ),
	        'parent_item_colon'   => __( 'Parent Item:', $this->textdomain ),
	        'all_items'           => __( 'All Portfolio', $this->textdomain ),
	        'view_item'           => __( 'View Portfolio', $this->textdomain ),
	        'add_new_item'        => __( 'Add New Portfolio', $this->textdomain ),
	        'add_new'             => __( 'Add New Portfolio', $this->textdomain ),
	        'edit_item'           => __( 'Edit Portfolio', $this->textdomain ),
	        'update_item'         => __( 'Update Portfolio', $this->textdomain ),
	        'search_items'        => __( 'Search Portfolio', $this->textdomain ),
	        'not_found'           => __( 'No Gallery Found', $this->textdomain ),
	        'not_found_in_trash'  => __( 'No Gallery found in Trash', $this->textdomain ),
	    );

	    $args = array(
	        'label'               => __( 'Hellcoderz Portfolio', $this->textdomain ),
	        'description'         => __( 'Hellcoderz Portfolio', $this->textdomain ),
	        'labels'              => $labels,
	        'supports'            => array( 'title', 'editor', '', '', '', '', '', '', '', '', '', ),
	        'taxonomies'          => array( 'post_tag' ),
	         'hierarchical'        => false,
	        'public'              => true,
	        'show_ui'             => true,
	        'show_in_menu'        => true,
	        'show_in_nav_menus'   => false,
	        'show_in_admin_bar'   => false,
	        'menu_position'       => 5,
	        'menu_icon'           => $this->dashicon,
	        'can_export'          => true,
	        'has_archive'         => true,
	        'exclude_from_search' => false,
	        'publicly_queryable'  => true,
	        'rewrite' => array( 'slug' => $this->plugin_slug, 'with_front' => true ),
	        'capability_type'     => 'page',
	    );

	    register_post_type( $this->post_type, $args );

	}

	public function HellcoderzPortfolio_init() {

		add_meta_box('HellcoderzPortfolio_meta', __('Add New Images', $this->textdomain), array( &$this, 'hcz_portfolio_function' ), $this->post_type, 'normal', 'high');

		add_meta_box(__('Plugin Shortcode', $this->textdomain) , __('Plugin Shortcode', $this->textdomain), array( &$this, 'hcz_pf_shortcode' ), $this->post_type, 'side', 'low');

	    add_meta_box(__('Project Date', $this->textdomain) , __('Project Date', $this->textdomain), array( &$this, 'hcz_pf_datepicker' ), $this->post_type, 'side', 'low');

	    add_meta_box(__('Project URL', $this->textdomain) , __('Project URL', $this->textdomain), array( &$this, 'hcz_pf_plink' ), $this->post_type, 'side', 'low');

	}


	public function admin_enqueue_scripts( $hook ) {
		
		wp_enqueue_style('dashboard');
	    wp_enqueue_style( 'hcz-jquery-ui-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );
	    wp_enqueue_style('hcz-meta-css', $this->plugin_url .'css/hcz-meta.css');
	    wp_enqueue_style('thickbox');

		wp_enqueue_script('theme-preview');
	    wp_enqueue_script('hcz-media-uploads', $this->plugin_url .'js/hcz-media-upload-script.js',array('media-upload','thickbox','jquery'));
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script('hcz-jquery-ui-datepicker', $this->plugin_url.'js/hcz-datepicker.js',array('jquery'));

	}

	public function add_options() {

		add_option( $this->option_setting , serialize( $this->settings ) );

	}

	public function save_options() {

		update_option( $this->option_setting , serialize( $this->settings ) );

	}

	public function get_options() {

		return unserialize( get_option( $this->option_setting ) );

	}

	public function init_settings() {

		$this->settings[ $this->setting_layout ] = $this->default_layout_col;
		$this->settings[ $this->setting_taglist ] = $this->default_taglist_top;
		$this->add_options();
	}

	public function user_can_save( $post_id, $nonce ) {
        
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], plugin_basename( __FILE__ ) ) ) ? true : false;
        
        // Return true if the user is able to save; otherwise, false.
        return ! ( $is_autosave || $is_revision) && $is_valid_nonce;
	}

	public function hcz_pf_shortcode(){
	?>
	<p>Use below shortcode in any Page/Post to publish your Portfolio</p>
			<input readonly="readonly" type="text" value="<?php echo "[HCZPF id=".get_the_ID()."]"; ?>"> 
	<?php
	} 

	public function hcz_pf_plink(){
	    wp_nonce_field( plugin_basename( __FILE__ ), 'hcz-pf-link-nonce' );
	?>
	<p>Enter live url of project if any</p>
	        <input type="text" name="hcz_demo_link" value="<?php echo get_post_meta( get_the_ID(), 'hcz_demo_link', true ); ?>"> 
	<?php
	}

	public function hcz_pf_datepicker(){
	    wp_nonce_field( plugin_basename( __FILE__ ), 'hcz-pf-datepicker-nonce' );
	    echo '<input type="text" id="datepicker" name="hcz_pf_date" value="' . get_post_meta( get_the_ID(), 'hcz_pf_date', true ) . '" />';
	}

	public function hcz_portfolio_function() {
	    $hcz_all_photos_details = unserialize(get_post_meta( get_the_ID(), 'hcz_all_photos_details', true));
	    $TotalImages =  get_post_meta( get_the_ID(), 'hcz_total_images_count', true );
	    $i = 1;
	    ?>
		<style>
			#titlediv #title {
			margin-bottom:15px;
			}
		</style>
	    <input type="hidden" id="count_total" name="count_total" value="<?php if($TotalImages==0){ echo 0; } else { echo $TotalImages; } ?>"/>
	    <div style="clear:left;"></div>

	    <?php
	    /* load saved photos into gallery */
	    if($TotalImages) {
	        foreach($hcz_all_photos_details as $hcz_single_photos_detail) {
	            $name = $hcz_single_photos_detail['hcz_image_label'];
	            $url = $hcz_single_photos_detail['hcz_image_url'];
	            ?>
	                <div class="hcz-image-entry" id="hcz_img<?php echo $i; ?>">
	                        <a class="gallery_remove" href="#gallery_remove" id="hcz_remove_bt<?php echo $i; ?>"onclick="remove_meta_img(<?php echo $i; ?>)"><img src="<?php echo  $this->plugin_url.'images/Close-icon.png'; ?>" /></a>
	                        <img src="<?php echo  $url; ?>" class="hcz-meta-image" alt=""  style="">
	                        <input type="button" id="upload-background-<?php echo $i; ?>" name="upload-background-<?php echo $i; ?>" value="Upload Image" class="button-primary" onClick="weblizar_image('<?php echo $i; ?>')" />
	                        <input type="text" id="hcz_img_url<?php echo $i; ?>" name="hcz_img_url<?php echo $i; ?>" class="hcz_label_text"  value="<?php echo  $url; ?>"  readonly="readonly" style="display:none;" />
	                        <input type="text" id="image_label<?php echo $i; ?>" name="image_label<?php echo $i; ?>" placeholder="Enter Image Label" class="hcz_label_text" value="<?php echo $name; ?>">
	                </div>
	            <?php
	            $i++;
	        } // end of foreach
	    } else {
	        $TotalImages = 0;
	    }
	    ?>


	    <div id="append_hcz_img">
	    </div>
	    <div class="hcz-image-entry add_hcz_new_image" onclick="add_hcz_meta_img()">
	            <div class="dashicons dashicons-plus"></div>
	            <p><?php _e('Add New Image', $this->textdomain); ?></p>
	    </div>
	    <div style="clear:left;"></div>
	    <script>
	    var hcz_i = parseInt(jQuery("#count_total").val());
	    function add_hcz_meta_img() {
	        hcz_i = hcz_i + 1;

	        var hcz_output = '<div class="hcz-image-entry" id="hcz_img'+ hcz_i +'">'+
	                            '<a class="gallery_remove" href="#gallery_remove" id="hcz_remove_bt' + hcz_i + '"onclick="remove_meta_img(' + hcz_i + ')"><img src="<?php echo  $this->plugin_url.'images/Close-icon.png'; ?>" /></a>'+
	                            '<img src="<?php echo  $this->plugin_url.'images/hcz-default.jpg'; ?>" class="hcz-meta-image" alt=""  style="">'+
	                            '<input type="button" id="upload-background-' + hcz_i + '" name="upload-background-' + hcz_i + '" value="Upload Image" class="button-primary" onClick="weblizar_image(' + hcz_i + ')" />'+
	                            '<input type="text" id="hcz_img_url'+ hcz_i +'" name="hcz_img_url'+ hcz_i +'" class="hcz_label_text"  value="<?php echo  $this->plugin_url.'images/hcz-default.jpg'; ?>"  readonly="readonly" style="display:none;" />'+
	                            '<input type="text" id="image_label'+ hcz_i +'" name="image_label'+ hcz_i +'" placeholder="Enter Image Label" class="hcz_label_text"   >'+
	                        '</div>';
	        jQuery(hcz_output).hide().appendTo("#append_hcz_img").slideDown(500);
	        jQuery("#count_total").val(hcz_i);
	    }

	    function remove_meta_img(id){
	        jQuery("#hcz_img"+id).slideUp(600, function(){
	            jQuery(this).remove();
	        });

	        count_total = jQuery("#count_total").val();
	        count_total = count_total - 1;
	        var id_i= id + 1;

	        for(var i=id_i;i<=hcz_i;i++){
	            var j = i-1;
	            jQuery("#hcz_remove_bt"+i).attr('onclick','remove_meta_img('+j+')');
	            jQuery("#hcz_remove_bt"+i).attr('id','hcz_remove_bt'+j);
	            jQuery("#hcz_img_url"+i).attr('name','hcz_img_url'+j);
	            jQuery("#image_label"+i).attr('name','image_label'+j);
	            jQuery("#hcz_img_url"+i).attr('id','hcz_img_url'+j);
	            jQuery("#image_label"+i).attr('id','image_label'+j);

	            jQuery("#hcz_img"+i).attr('id','hcz_img'+j);
	        }
	        jQuery("#count_total").val(count_total);
	        hcz_i = hcz_i - 1;
	    }
	    </script>
	    <?php
	}

	public function hcz_portfolio_meta_save() {
	    if(isset($_POST['post_ID'])) {
	        $post_ID = $_POST['post_ID'];
	        $post_type = get_post_type($post_ID);
	        if($post_type == $this->post_type) {
	            $TotalImages = $_POST['count_total'];
	            $ImagesArray = array();
	            if($TotalImages) {
	                for($i=1; $i <= $TotalImages; $i++) {
	                    $image_label = "image_label".$i;
	                    $name = $_POST['image_label'.$i];
	                    $url = $_POST['hcz_img_url'.$i];
	                    $ImagesArray[] = array(
	                        'hcz_image_label' => $name,
	                        'hcz_image_url' => $url
	                    );
	                }
	                update_post_meta($post_ID, 'hcz_all_photos_details', serialize($ImagesArray));
	                update_post_meta($post_ID, 'hcz_total_images_count', $TotalImages);
	            } else {
	                $TotalImages = 0;
	                update_post_meta($post_ID, 'hcz_total_images_count', $TotalImages);
	                $ImagesArray = array();
	                update_post_meta($post_ID, 'hcz_all_photos_details', serialize($ImagesArray));
	            }

	            if( $this->user_can_save( $post_ID, 'hcz-pf-datepicker-nonce' ) ) { 
	         
	                if( get_post_meta( $post_ID, 'hcz_pf_date' ) ) {
	                    delete_post_meta( $post_ID, 'hcz_pf_date' );
	                } 
	                update_post_meta( $post_ID, 'hcz_pf_date', strip_tags( $_POST[ 'hcz_pf_date' ] ) );           
	            }

	            if( $this->user_can_save( $post_ID, 'hcz-pf-link-nonce' ) ) { 
	         
	                if( get_post_meta( $post_ID, 'hcz_demo_link' ) ) {
	                    delete_post_meta( $post_ID, 'hcz_demo_link' );
	                } 
	                update_post_meta( $post_ID, 'hcz_demo_link', strip_tags( $_POST[ 'hcz_demo_link' ] ) );           
	            }
	        }
	    }
	}

	public function hcz_SettingsPage() {

		add_submenu_page('edit.php?post_type='. $this->post_type , __('Settings', $this->textdomain ), __('Settings', $this->textdomain ), 'administrator', 'hcz-portfolio-settings', array( &$this, 'hcz_portfolio_settings_page_function' ) );

	}

	public function hcz_portfolio_settings_page_function() {
	     require_once( $this->directory ."/includes/hcz-portfolio-settings.php" );
	}
	
	
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}