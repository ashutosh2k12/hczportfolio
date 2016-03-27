<?php
/**
 * @package HCZ_Portfolio
 * @author  Ashutosh Chaudhary <me@aboutashu.com>
 */

class HCZPF{

	//Config
	public $version = '1.0.0';
	public $shortcode = 'HCZPF';
	public $prefix = 'HCZ_PF_';
	public $post_type = 'hczpf';
	public $textdomain = 'hcz_pf_txtdom';
	public $dashicon = 'dashicons-format-gallery';
	public $option_setting = 'HCZ_PF_Settings';
	public $plugin_slug = 'hczpf';
	public $default_layout_col = "col-md-4";
	public $default_taglist_top = "yes";
	public $default_placeholder =  '/images/hcz-default.jpg';

	//Settings keys
	public $setting_layout = 'HCZ_Gallery_Layout';
	public $setting_taglist = 'HCZ_Gallery_TaglistTop';

	public $directory = HCZ_PF_DIR;
	public $plugin_url = HCZ_PF_PLUGIN_URL;

	public function __construct() {

	}
}
