<h2><?php _e("Portfolio Settings", $this->textdomain); ?></h2>
<form action="?post_type=<?php echo $this->post_type; ?>&page=hcz-portfolio-settings" method="post">
    <input type="hidden" id="hcz_settings_action" name="hcz_settings_action" value="hcz-save-settings">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label><?php _e("Gallery Layout", $this->textdomain); ?></label></th>
                <td>
                    <select name="hcz-gallery-layout" id="hcz-gallery-layout">
                        <optgroup label="Select Gallery Layout">
                            <option value="col-md-4" <?php if($HCZ_Gallery_Layout == 'col-md-4') echo "selected=selected"; ?>><?php _e("Four Column", $this->textdomain); ?></option>
                            <option value="col-md-3" <?php if($HCZ_Gallery_Layout == 'col-md-3') echo "selected=selected"; ?>><?php _e("Three Column", $this->textdomain); ?></option>
                        </optgroup>
                    </select>
                    <p class="description"><?php _e("Choose a column layout for image gallery.", $this->textdomain); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label><?php _e("Tag List on top", $this->textdomain); ?></label></th>
                <td>
                    <input type="radio" name="hcz-taglist-top" id="hcz-taglist-top" value="yes" <?php if($HCZ_Taglist_Top == 'yes' ) { echo "checked"; } ?>> Yes
                    <input type="radio" name="hcz-taglist-top" id="hcz-taglist-top" value="no" <?php if($HCZ_Taglist_Top == 'no' ) { echo "checked"; } ?>> No
                    <p class="description"><?php _e("Select if you want to show tags list on top of portfolio", $this->textdomain); ?></p>
                </td>
            </tr>
			<tr>
				
			</tr>

        </tbody>
    </table>
    <p class="submit">
        <input type="submit" value="<?php _e("Save Changes", $this->textdomain); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>