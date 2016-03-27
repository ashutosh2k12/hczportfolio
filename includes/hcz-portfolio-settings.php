<?php
/**
 * Load Saved Image Gallery settings
 */
$HCZ_PF_Settings  = $this->get_options();

$HCZ_Gallery_Layout = $this->get_setting_layout();
$HCZ_Taglist_Top    = $this->get_setting_showtagtop();

include_once( $this->directory . '/tpl/settings.php' );

if(isset($_POST['hcz_settings_action'])) {
    $Action = $_POST['hcz_settings_action'];

    if($Action == "hcz-save-settings") {
   
        $HCZ_Gallery_Layout =   $_POST['hcz-gallery-layout'];
        $HCZ_Taglist_Top    =   $_POST['hcz-taglist-top'];
        
        $this->settings[ $this->setting_layout ]    = $HCZ_Gallery_Layout;
        $this->settings[ $this->setting_taglist ]   = $HCZ_Taglist_Top;
        $this->save_options();

        echo "<script>location.href = location.href;</script>";
    }
}
